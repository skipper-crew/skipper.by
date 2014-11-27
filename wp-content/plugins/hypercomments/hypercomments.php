<?php
/*
Plugin Name: HyperComments
Plugin URI: http://hypercomments.com/
Description: HyperComments - New dimension of comments. Hypercomments technology allows commenting a specific word or a piece of text. 
Version: 1.1.0
Author:  Alexandr Bazik, Dmitry Goncharov, Inna Goncharova
Author URI: http://hypercomments.com/
*/
define('HC_DEV',false);
define('HC_URL', 'http://hypercomments.com');
require_once(dirname(__FILE__) . '/export.php');
define('HC_CONTENT_URL', get_option('siteurl') . '/wp-content');
define('HC_PLUGIN_URL', HC_CONTENT_URL . '/plugins/hypercomments');
define('HC_XML_PATH',$_SERVER['DOCUMENT_ROOT'].'/wp-content/uploads');
$is_append = false;

register_deactivation_hook(__FILE__,'hc_delete');
register_activation_hook(__FILE__,'hc_active');          
add_action('init', 'hc_request_handler');
add_action('admin_head', 'hc_admin_head');
add_filter('the_content', 'hc_the_content_filter', 50);
add_filter('wp_trim_excerpt', 'hc_the_content_filter', 50); 

add_action('wp_footer', 'hc_count_widget',20);
add_filter('comments_template', 'hc_comments_template');
add_filter('comments_number', 'hc_comments_text');
add_filter('get_comments_number', 'hc_comments_number');

add_action('admin_menu', 'hc_add_pages', 10);
add_action('admin_notices', 'hc_messages');
/**
 * The event handler
 * @global type $post
 * @global type $wpdb
 * @return type 
 */
function hc_request_handler() {
    global $post;
    global $wpdb; 
    
    if(function_exists('load_plugin_textdomain')) {
        load_plugin_textdomain('hypercomments', 'wp-content/plugins/hypercomments/locales');
    }

    if (!empty($_GET['hc_action'])) {
        switch ($_GET['hc_action']) {      
            case 'export_comments':               
                if (current_user_can('manage_options')) {  
                    try{   
                        $response_array = array();
                        $id_post = $_GET['post'];
                        require_once(dirname(__FILE__) . '/export.php');
                        $posts = $wpdb->get_results($wpdb->prepare("
                            SELECT * FROM $wpdb->posts WHERE ID=$id_post")); 
                        foreach ($posts as $p) {
                            $wxr = hc_export_wp($p);
                            if($wxr){
                                $dir_root  = dirname(dirname(dirname(__FILE__))).'/uploads';
                                if(is_dir($dir_root)){
                                    $file_name = time().'_'.rand(0,100).'.xml';                    
                                    $file_root = $dir_root.'/'.$file_name;                      
                                    $file_path = HC_CONTENT_URL.'/uploads/'.$file_name;
                                    $write_file = file_put_contents($file_root, $wxr);
                                    if($write_file){
                                        $json_arr = array(
                                            'service'     => 'wordpress',
                                            'pageID'      => $p->ID,
                                            'widget_id'   => get_option('hc_wid'),
                                            'request_url' => $file_path,                      
                                            'result_url'  => admin_url('index.php').'?hc_action=delete_xml&xml='.$file_name,
                                            'result'      => 'success'
                                        );                                             
                                                              
                                    }else{
                                        $json_arr = array('result'=>'error','description'=>'Error writing XML');
                                    }
                                }else{
                                    $json_arr = array('result'=>'error','description'=>'Error writing XML');
                                }
                            }else{
                                 $json_arr = array('result'=>'error','description'=>'Failed to generate XML');
                            }
                            $response_array[] = $json_arr;
                        }
                        echo json_encode($response_array);                       
                        die();
                    }catch(Exception $e){
                         $json_arr = array('result'=>'error','description'=>'Error');
                    }
                }                     
            break;   
            case 'save_wid':
                update_option('hc_wid', $_GET['wid']);
                update_option('hc_access', $_GET['access']);
                update_option('hc_secret_key', $_GET['secret_key']);
                update_option('hc_synch', 'on');
                echo $_GET['access'];
                die();
            break;
            case 'delete_xml':
                if(isset($_GET['result']) && $_GET['result'] == 'success'){
                    $filename = dirname(dirname(dirname(__FILE__))).'/uploads/'.$_GET['xml'];
                    unlink($filename);
                    return json_encode(array('result'=>'success'));
                }else{
                    return json_encode(array('result'=>'error'));
                }
                exit();
            break;
            case 'notify':
                $data = stripslashes($_POST['data']);
                $time = $_POST['time'];
                $signature = $_POST['signature'];
                
                if((time() - $time) <= 60){                  
                    if(get_option('hc_secret_key')){                                                                 
                         if($signature == md5((string)get_option('hc_secret_key').(string)$data.(string)$time)){
                             $data_decode = json_decode($data);                                                               
                             foreach($data_decode as $cmd){                                 
                                 switch($cmd->cmd){
                                     case 'streamMessage':                                       
                                        $post_id_mas = explode('?', $cmd->xid);
                                        $pos = strpos($cmd->xid,'=');                                         
                                        $post_id = substr($cmd->xid, $pos+1);
                                        
                                        $data = array(
                                            'comment_post_ID' => $post_id,
                                            'comment_author' => $cmd->nick,                                          
                                            'comment_content' => $cmd->text,                                         
                                            'comment_parent' => $cmd->parent_id,                                                                                   
                                            'comment_date' => date('Y-m-d H:i:s', time() + (get_option('gmt_offset') * 3600)),
                                            'comment_date_gmt' => date('Y-m-d H:i:s'),
                                            'comment_approved' => 1,
                                        );    
                                        if(isset($cmd->user_id)){
                                            $data['user_id'] = $cmd->user_id;
                                        }else{
                                            $data['user_id'] = 0;
                                        }
                                        if(isset($cmd->ip)){
                                            $data['comment_author_IP'] = $cmd->ip;
                                        }else{
                                            $data['comment_author_IP'] = '';
                                        }
                                        $comments_id = wp_insert_comment($data);
                                        update_comment_meta($comments_id,'hc_comment_id',$cmd->id);    
                                        http_response_code(200);                                        
                                     break;
                                     case 'streamEditMessage':                                      
                                         $comments_id = $wpdb->get_var($wpdb->prepare( "SELECT comment_id FROM $wpdb->commentmeta WHERE meta_key = 'hc_comment_id' AND meta_value = %s LIMIT 1", $cmd->id));
                                         $commentarr = array();
                                         $commentarr['comment_ID'] = $comments_id;  
                                         $commentarr['comment_content'] = $cmd->text;                                       
                                         wp_update_comment( $commentarr ); 
                                         http_response_code(200);                                            
                                     break;
                                     case 'streamRemoveMessage':
                                         $comments_id = $wpdb->get_var($wpdb->prepare( "SELECT comment_id FROM $wpdb->commentmeta WHERE meta_key = 'hc_comment_id' AND meta_value = %s LIMIT 1", $cmd->id));
                                         wp_delete_comment ($comments_id);   
                                         http_response_code(200);                                     
                                     break;
                                 }                                
                             }
                         }
                    }                  
                }
            break;
        }
    }
}
/**
 * Include styles and files in the admin
 */
function hc_admin_head(){
    $page = (isset($_GET['page']) ? $_GET['page'] : null);
    if ( $page == 'hypercomments') {
?>
    <link rel='stylesheet' href='<?php echo HC_PLUGIN_URL;?>/css/hypercomments.css'  type='text/css' />
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    <script>jQueryHC = jQuery.noConflict(true);</script>
<?php
    }
}
/**
 * Action by activating the plugin
 */
function hc_active(){  
      update_option('hc_selector', '.hc_counter_comments');
}
/**
 * Action when uninstall plugin
 */
function hc_delete(){  
    delete_option('hc_wid');
    delete_option('hc_access');
    delete_option('hc_selector');
    delete_option('hc_title_widget');
    delete_option('hc_secret_key');
    delete_option('hc_synch');
    delete_option('hc_root');
}
/**
 * Changing the template comments
 * @param type $value
 * @return type 
 */
function hc_comments_template($value){     
   return dirname(__FILE__) . '/comments.php';
}

function hc_comments_number($count) {
    global $post;
    return $count;
}
/**
 * Replacement of counters
 * @global type $post
 * @param type $comment_text
 * @return type 
 */
function hc_comments_text($comment_text) {
    global $post;  
    $parse = parse_url($post->guid);
    $url =  str_replace($parse['scheme'].'://'.$parse['host'], get_option('home'), $post->guid);   
    return '<span class="hc_counter_comments" href="'.$url.'">'.$comment_text.'</span>'; 
}
/**
 * Insert widget on the site of the old comments
 * @global type $post 
 */
function hc_show_script() {      
    global $post;      
    global $is_append;
    $parse = parse_url($post->guid);
    if(get_option('hc_root')){
        $url = str_replace('https://','',str_replace('http://','',str_replace('www.','',$post->guid)));  
    }else{
        $url = str_replace('https://','',str_replace('http://','',str_replace('www.','',str_replace($parse['host'], get_option('home'), $post->guid))));  
    }  
    if($is_append === false && $post->comment_status == 'open'){
?>
<div id="hypercomments_widget"></div>
<script type="text/javascript">
var _hcp = _hcp || {};_hcp.widget_id = <?php echo get_option('hc_wid');?>;_hcp.widget = "Stream";_hcp.platform = "wordpress";
_hcp.language = "<?php echo hc_get_language();?>";_hcp.xid = "<?php echo $url?>";<?php echo hc_get_auth();?>
<?php if(HC_DEV) echo '_hcp.hc_test=1;';?>
(function() { 
var hcc = document.createElement("script"); hcc.type = "text/javascript"; hcc.async = true;
hcc.src = ("https:" == document.location.protocol ? "https" : "http")+"://widget.hypercomments.com/apps/js/hc.js";
var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(hcc, s.nextSibling); 
})();
</script>
<?php
      }else{
?>
<div id="hypercomments_widget_newappend"></div>
<script type="text/javascript">
_hcp.append = "#hypercomments_widget_newappend";
</script>
<?php
      }
}
/**
 * Insert widget counters
 */
function hc_count_widget() {       
  if(!is_singular() && !(is_page() && is_single())) {        
?>
<script type="text/javascript">
<?php if(HC_DEV) echo 'HCDeveloper = true';?>   
var _hcp = _hcp || {};_hcp.widget_id = <?php echo get_option('hc_wid');?>;_hcp.widget = "Bloggerstream";_hcp.selector='<?php echo get_option('hc_selector');?>';
_hcp.platform = "wordpress";_hcp.language = "<?php echo hc_get_language();?>";
<?php
if(get_option('hc_title_widget')){
    echo '_hcp.selector_widget = ".hc_content_comments";';
}
?>
<?php
    if(hc_enableParams()){
        echo '_hcp.enableParams=true;';
    }
 ?>
(function() { 
var hcc = document.createElement("script"); hcc.type = "text/javascript"; hcc.async = true;
hcc.src = ("https:" == document.location.protocol ? "https" : "http")+"://widget.hypercomments.com/apps/js/hc.js";
var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(hcc, s.nextSibling); 
})();
</script>
<?php
  }
}
/**
 * Include manage file
 */
function hc_options_page() {
    if( $_POST['hc_form_counter_sub'] == 'Y' ) {
        update_option( 'hc_selector',  $_POST['hc_form_selector'] );
        if(isset($_POST['hc_title_widget'])){
            update_option( 'hc_title_widget',  $_POST['hc_title_widget'] );
        }else{
            delete_option('hc_title_widget');
        }
        if(isset($_POST['hc_root'])){
            update_option( 'hc_root',  $_POST['hc_root'] );
        }else{
            delete_option('hc_root');
        }
        if(isset($_POST['hc_synch'])){
            update_option( 'hc_synch',  $_POST['hc_synch'] );
        }else{
            delete_option('hc_synch');
        }
        if(isset($_POST['hc_secret_key'])){
            update_option( 'hc_secret_key',  $_POST['hc_secret_key'] );
        }else{
            delete_option('hc_secret_key');
        }
        echo '<div class="updated"><p><strong>'.__('Options saved', 'hypercomments').'</strong></p></div>';
    }
    include_once(dirname(__FILE__) . '/manage.php');
}
/**
 * Insert menu in the Comments section
 */
function hc_add_pages() {
    add_submenu_page(
        'edit-comments.php',
        'HyperComments',
        'HyperComments',
        'moderate_comments',
        'hypercomments',
        'hc_options_page'
    );
}
/**
 * Notice of setting the widget
 */
function hc_messages() {
    $page = (isset($_GET['page']) ? $_GET['page'] : null);
    if ( !get_option('hc_wid') && $page != 'hypercomments') {
       echo '<div class="updated"><p><b>'.__('You must <a href="edit-comments.php?page=hypercomments">configure the plugin</a> to enable HyperComments.', 'hypercomments').'</b></p></div>';
    }
}
/**
 * Consider GET-params?
 * @global type $wpdb
 * @return type 
 */
function hc_enableParams()
{
     global $wpdb;
     $results = $wpdb->get_results( "SELECT guid FROM $wpdb->posts WHERE post_type !='revision' AND post_status = 'publish' LIMIT 1");
     foreach ( $results as $result ) {
       $link = $result->guid;
     }
     return strstr($link,'?');
}
/**
 * Returns the locale
 * @return type 
 */
function hc_get_language()
{
    $local = get_locale();
    $local_lang = explode('_',$local);     
    return $local_lang[0];  
}
/**
 * Filter content
 * @global type $post
 * @param type $content
 * @return string 
 */
function hc_the_content_filter( $content ){ 
    global $post;     
    global $is_append;    
    global $user;

    if(!hc_searchbot_detect($_SERVER['HTTP_USER_AGENT'])){
        $parse = parse_url($post->guid);
        if(get_option('hc_root')){
            $url = str_replace('https://','',str_replace('http://','',str_replace('www.','',$post->guid)));  
        }else{
            $url = str_replace('https://','',str_replace('http://','',str_replace('www.','',str_replace($parse['host'], get_option('home'), $post->guid))));  
        }       
        if(get_option('hc_title_widget')){
            if($post->comment_status == 'open'){  
                if ( !is_singular()){          
                    $content = sprintf(
                        '%s<div class="hc_content_comments" data-xid="'.$url.'"></div>',
                      $content          
                    );
                }
            }
        }    
        
        if(is_singular()){   
            if($post->comment_status == 'open'){   
                $is_append = true;         
                $wid = '<div id="hypercomments_widget"></div>
                <script type="text/javascript">                                      
                var _hcp = _hcp || {};_hcp.widget_id = '.get_option('hc_wid').';_hcp.widget = "Stream";_hcp.platform="wordpress";
                _hcp.language = "'.hc_get_language().'";_hcp.xid = "'.$url.'";'.hc_get_auth().'         
                (function() { 
                var hcc = document.createElement("script"); hcc.type = "text/javascript"; hcc.async = true;
                hcc.src = ("https:" == document.location.protocol ? "https" : "http")+"://widget.hypercomments.com/apps/js/hc.js";
                var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(hcc, s.nextSibling); 
                })();
                </script>';
                $content = $content.$wid;     
            }
        }
    }else{
        $is_append = true;
    }
    return $content;
}
/**
 * Get auth token
 * @global type $user
 * @return string 
 */
function hc_get_auth(){
    global $current_user;
    get_currentuserinfo();
    if(is_user_logged_in() && get_option('hc_synch')){
        $user = array(
            'nick'   => $current_user->user_nicename,
            'avatar' => hc_parse_avatar($current_user->ID, 36),
            'id'     => $current_user->ID,
            'email'  => $current_user->user_email
        );
        $secret = get_option('hc_secret_key');
        $time   = time();    
        $base64 = base64_encode(json_encode($user));    
        $sign   = md5($secret . $base64 . $time);
        $auth = $base64 . "_" . $time . "_" . $sign;
        $hcp = '_hcp.auth="'.$auth.'";';              
    }else{
        $hcp = '';
    }
    return $hcp;
}
/**
 * Get avatar path
 * @param type $email
 * @return string 
 */
function hc_parse_avatar($email){
    $html_avatar = get_avatar($email);
    preg_match("/src=(\'|\")(.*)(\'|\")/Uis", $html_avatar, $matches);
    $avatar_src = substr(trim($matches[0]), 5, strlen($matches[0]) - 6);
    if(strpos($avatar_src, 'http') === false)
    {
        $avatar_src = get_option('siteurl').$avatar_src;
    }
    return $avatar_src;
}
/**
 * Detect Search Bot
 * @param type $user_agent
 * @return boolean
 */
function hc_searchbot_detect($user_agent)
{
    $engines = array(
            'Aport','Google','msnbot','Rambler','Yahoo','AbachoBOT',
            'accoona','AcoiRobot','ASPSeek','CrocCrawler','Dumbot',
            'FAST-WebCrawler','GeonaBot','Gigabot','Lycos','MSRBOT',
            'Scooter','AltaVista','WebAlta','IDBot','eStyle','Mail.Ru',
            'Scrubby','Yandex','YaDirectBot'
        );
    foreach ($engines as $engine) {
        if(strstr(strtolower($user_agent), strtolower($engine))){
            return true;
        }
    }
    return false;
}
/**
 * Return all post comments
 * @global type $wpdb
 * @global type $post
 * @return boolean array
 */
function hc_get_comments_post()
{
    global $wpdb;
    global $post;
    $commentQ = "SELECT * FROM $wpdb->comments WHERE comment_post_ID=".$post->ID ;
    $comments = $wpdb->get_results( $wpdb->prepare($commentQ));
    return $comments;
}
/**
 * Return all posts with comments
 * @global type $wpdb
 * @global type $post
 * @returnarray
 */
function hc_get_post_export()
{
    global $wpdb;
    global $post;
    $posts = $wpdb->get_results($wpdb->prepare("
            SELECT * FROM $wpdb->posts WHERE post_type != 'revision' AND post_status = 'publish' AND comment_count > 0")); 
    return $posts;
}
?>