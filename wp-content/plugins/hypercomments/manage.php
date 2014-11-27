<div class="wrap">
        <div id="import_hc" class="updated" style="display: none"><p><strong></strong></p></div>
        <div id="no_folder" class="updated" style="display: none"><p><strong></strong></p></div>
        <div id="code" style="display: none; padding: 10px; font-weight: bold;" class="updated"></div>
<?php  
    // header
    echo "<h2>" . __( 'HyperComments', 'hypercomments' ) . "</h2>";
    if(!get_option('hc_wid') || !get_option('hc_access'))
    {
        echo "<div class='hc_menu_box'><ul class='hc_menu_top'><li data-menu='login_first' class='active'>".__('Login','hypercomments')."</li></ul></div>";
    }elseif(get_option('hc_wid') && get_option('hc_access') == 'deny'){
        echo "<div class='hc_menu_box'><ul class='hc_menu_top'><li data-menu='login_first' class='active'>".__('Login','hypercomments')."</li></ul></div>";
    }elseif(get_option('hc_wid') && (get_option('hc_access') == 'admin' || get_option('hc_access') == 'moderator')){
        echo "<div class='hc_menu_box'><ul class='hc_menu_top'><li data-menu='manager' class='active'>".__('Comments','hypercomments')."</li><li data-menu='admin'>".__('Admin','hypercomments')."</li><li data-menu='settings'>".__('Settings','hypercomments')."</li></ul></div>";   
    }
   
    $local = get_locale();
    $local_lang = explode('_',$local);  
    if(in_array($local_lang[0], array('en','ru','ua','de','fr')))
    {
        $lang = $local_lang[0];
    }else{
        $lang = 'en';
    }       
?>
        
 <?php if(!get_option('hc_wid') || !get_option('hc_access')):?> 
  <div class="hc_box hc_login_first">
    <div><p><?php _e('To start using the plug-HyperComments, to authorize a Google Account', 'hypercomments' ) ?></p></div>
    <div class="hc_login"><?php _e('Login with Google','hypercomments');?>  </div>
 </div>        
 <?php endif;?> 
 
<?php if(get_option('hc_wid') && (get_option('hc_access') == 'admin' || get_option('hc_access') == 'moderator')):?> 
 <div class="hc_box hc_admin" style="display:none">
</div>
<?php endif;?>
 
<?php if(get_option('hc_wid') && (get_option('hc_access') == 'admin' || get_option('hc_access') == 'moderator')):?>       
<div class="hc_box hc_manager" <?php echo !get_option('hc_wid') ? 'style="display:none"' : ''?>>
 <div id="widget" style="width:1000px; margin: 0 auto;"></div>
<script type="text/javascript">
    var _hcp = {};
    _hcp.widget = "Adm";
    _hcp.append = "#widget";   
    _hcp.lang = '<?php echo $lang;?>';
    _hcp.widget_id = <?php echo get_option('hc_wid')?>;
    <?php if(HC_DEV): ?>
    _hcp.test = 1;
    _hcp.debug = 1;
    _hcp.dev = 1;
    <?php endif;?>
    (function() { 
        var hcc = document.createElement("script"); hcc.type = "text/javascript"; hcc.async = true;
        hcc.src = ("https:" == document.location.protocol ? "https" : "http")+"://widget.hypercomments.com/apps/js/hc.js";
        var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(hcc, s.nextSibling); 
    })();
</script>
 </div>
<?php endif;?> 
        
 <?php if(get_option('hc_wid') && get_option('hc_access') == 'admin'):?>       
 <div class="hc_box hc_settings" style="display:none">
     <form name="form_counter" id="form_settings" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
     <table>          
         <tr>
             <td><?php _e('HTML - selector to insert the counter comments', 'hypercomments' ); ?></td>
             <td>                                  
                     <p>
                         <input type="text" name="hc_form_selector" value="<?php echo get_option('hc_selector'); ?>" size="40">                    
                    </p>                                   
             </td>
         </tr>
         <tr>
             <td><?php _e('HyperComments under each article', 'hypercomments' ); ?></td>
             <td>                                
                     <p>
                         <label>
                         <input type="checkbox" name="hc_title_widget"  <?php echo (get_option('hc_title_widget') ? 'checked="checked"' : '');?> />                        
                         <?php _e('on/off', 'hypercomments' ); ?>
                         </label>
                    </p> 
                               
             </td>
         </tr>     
         <tr>
             <td><?php _e('Synchronizing users and comments', 'hypercomments' ); ?></td>
             <td>                                
                     <p>
                         <label>
                         <input type="checkbox" name="hc_synch"  <?php echo (get_option('hc_synch') ? 'checked="checked"' : '');?> />                        
                         <?php _e('on/off', 'hypercomments' ); ?>
                         </label>
                    </p> 
                               
             </td>
         </tr>
         <tr>
             <td><?php _e('Site\'s root  is in the subdirectory', 'hypercomments' ); ?></td>
             <td>                                
                     <p>
                         <label>
                         <input type="checkbox" name="hc_root"  <?php echo (get_option('hc_root') ? 'checked="checked"' : '');?> />                        
                         <?php _e('yes/no', 'hypercomments' ); ?><br />
                         <?php _e('Mark the checkbox if the site is located in a subdirectory and you import comments via hypercomments.com website', 'hypercomments' ); ?>
                         </label>
                    </p> 
                               
             </td>
         </tr>
         <tr>
             <td></td>
             <td>
                 <p>
                        <input type="hidden" name="hc_secret_key" value="" />
                        <input type="hidden" name="hc_form_counter_sub" value="Y" />
                        <input type="submit" name="Submit" id="sub_settings" value="<?php _e('Update Options', 'hypercomments' ) ?>" />
                  </p>
               
             </td>
         </tr>
           
         <tr>
             <td><?php _e('Import comments to HyperComments', 'hypercomments' ); ?></td>
             <td>
                 <img id="load_import" src="<?php echo HC_PLUGIN_URL;?>/css/loading.gif" alt="loading" style="float:right; display: none" />
                <button  id="wp_to_hc" class="button"><?php _e('Import comments to HyperComments', 'hypercomments' ) ?></button>             
             </td>
         </tr>   
         <tr>
             <td></td>
             <td> 
                 <div class="e_import_comments">                   
                    <table border="0" id="import_report" cellspacing="5" style="display:none;text-align:center">
                        <thead>
                        <tr>
                            <th></th>
                            <th><?php _e('Formation file', 'hypercomments' );?></th>
                            <th><?php _e('Import', 'hypercomments' );?></th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div></td>
         </tr>
    </table>
     </form>
 </div>      
  
 <?php endif;?>       
</div>
<script type="text/javascript">
    jQueryHC(document).ready(function(){            
         jQueryHC('.hc_menu_top li').click(function(){
            var menu = jQueryHC(this).data('menu');      
            jQueryHC('.hc_menu_top li').removeClass('active');
            jQueryHC(this).addClass('active');
            jQueryHC('.hc_box').hide();
            jQueryHC('.hc_'+menu).show();
            if(menu == 'admin'){              
                 var iframe = jQueryHC('<iframe></iframe>');
                 iframe.attr('src','<?php echo HC_URL;?>/wordpress?action=settings&service=wordpress&wid=<?php echo get_option('hc_wid')?>&lang=<?php echo $lang;?>&time=<?php echo time();?>');
                 iframe.attr('style','width: 100%;display: inline-block;height: 900px;overflow-x: hidden;overflow-y: hidden;"');
                 iframe.attr('scrolling','no');
                 iframe.attr('frameborder','no');
                 jQueryHC('.hc_admin').html(iframe);            
            }
        });
        
        jQueryHC('.hc_login').click(function(){                 
                var callback = function(){createWidget();}
                jQueryHC(document).trigger('popup', [600, 450, 'hc_auth', '<?php echo HC_URL;?>/auth?service=google', callback]);
        });  
         
        function createWidget(){           
            jQueryHC.getJSON('<?php echo HC_URL;?>/<?php echo $lang;?>/widget/wordpresscreate?jsoncallback=?',
            {
                site: "<?php echo get_option('siteurl');?>",
                title: "<?php echo get_option('blogname');?>",
                plugins: "comments,rss,login,count_messages,authors,topics,hypercomments",
                hypertext: "*",
                limit: 20,
                template: "index",
                cluster: "c1",
                platform: "wordpress",
                notify_url:"<?php echo admin_url('index.php');?>?hc_action=notify",
                <?php if(hc_enableParams()){echo 'enableParams: true';}?>                
            },
            function (data) {          
                if(data.result == 'success'){
                    setCookie("wid", data.wid, "Mon, 01-Jan-2100 00:00:00 GMT", "/");
                    setCookie("hc_es", data.es, "Mon, 01-Jan-2100 00:00:00 GMT", "/");
                    saveWid(data);
                }else{
                    jQueryHC('#code').text(data.description).show(function(){
                        jQueryHC(this).delay(5000).fadeOut();
                    });
                }              
            });
        }
         
    function saveWid(data)
    {
        data.hc_action = 'save_wid';
        jQueryHC.get('<?php echo admin_url('index.php'); ?>',
            data,
            function() {
                document.location.href = 'edit-comments.php?page=hypercomments';
            }
        );     
    }                   
    // Open PopUp window
    jQueryHC(document).bind('popup', function(e, width, height, name, url, callback){     
	    var x = (640 - width)/2;
	    var y = (480 - height)/2;			
	    if (screen) {
	       y = (screen.availHeight - height)/2;
	       x = (screen.availWidth - width)/2;
	    }
        var w = window.open(url, name , "menubar=0,location=0,toolbar=0,directories=0,scrollbars=0,status=0,resizable=0,width=" + width + ",height=" + height + ',screenX='+x+',screenY='+y+',top='+y+',left='+x);
	    w.focus();
			    
	    if(callback)
	    var interval = setInterval(function(){
	       if (!w || w.closed){
	  	    clearInterval(interval);
	  	    callback();
	    }
	    }, 500);
         	   
    });
                
    function setCookie (name, value, expires, path, domain, secure) {
      document.cookie = name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
    }
    
    jQueryHC('#wp_to_hc').click(function(){
        jQueryHC('#load_import').show(); 
        var posts = [];
        <?php foreach (hc_get_post_export() as $p): ?>
        posts.push(<?php echo $p->ID;?>);
        <?endforeach;?> 

        var query = function(posts){
            var page = posts[0];
            posts.shift();
            get_param = {
                url:'<?php echo admin_url('index.php'); ?>',
                data:'hc_action=export_comments&post='+page,
                success: function(data) {   
                        if(data && data.length > 0){  
                        try{  
                            jQueryHC('#import_report').show();                  
                            var packet = JSON.parse(data);                            
                            for(var i=0; i<packet.length; i++){
                                if(packet[i].result == 'success'){
                                    var dom = '<tr><td><b>Page ID: '+packet[i].pageID+'</b></td><td><img src="<?php echo HC_PLUGIN_URL;?>/css/button_ok.png" alt="" /></td><td class="importpage_'+packet[i].pageID+'"></td></tr>';                                                 
                                    sendNotify(packet[i]);                       
                                }else{
                                    if(packet[i].description == 'Error writing XML'){
                                        jQueryHC('#no_folder strong').text('<?php _e('You need to create a folder "uploads" with 777 permissions in the "/path/to/wordpress/wp-content" directory.', 'hypercomments' ); ?>'); 
                                        jQueryHC('#no_folder').show(function(){
                                            jQueryHC(this).delay(30000).fadeOut();
                                        });
                                        jQueryHC('#load_import').hide();
                                    }
                                    if(packet[i].pageID)
                                        var dom = '<tr><td><b>Page ID: '+packet[i].pageID+'</b></td><td><img src="<?php echo HC_PLUGIN_URL;?>/css/button_no.png" alt="" /></td><td class="importpage_'+packet[i].pageID+'"></td></tr>';                                                      
                                    else
                                        var dom = '<tr><td><b>Page ID: error</b></td><td><img src="<?php echo HC_PLUGIN_URL;?>/css/button_no.png" alt="" /></td><td class="importpage_"></td></tr>';
                                }    
                                jQueryHC('#import_report tbody').append(dom);       
                                if(posts.length == 0){
                                    jQueryHC('#import_hc strong').text('<?php _e('Comments will be imported at least 15 minutes.', 'hypercomments' ); ?>'); 
                                    jQueryHC('#import_hc').show(function(){
                                        jQueryHC(this).delay(30000).fadeOut();
                                    });
                                    jQueryHC('#load_import').hide();
                                }
                            }
                        }catch(e){
                            jQueryHC('#no_folder strong').text('<?php _e('You need to create a folder "uploads" with 777 permissions in the "/path/to/wordpress/wp-content" directory.', 'hypercomments' ); ?>'); 
                            jQueryHC('#import_hc').show(function(){
                                    jQueryHC(this).delay(30000).fadeOut();
                            });
                            jQueryHC('#load_import').hide();
                        }
                        }else{
                            jQueryHC('.e_import_comments p').first().find('span').html('<img src="<?php echo HC_PLUGIN_URL;?>/css/button_no.png" alt="" />');
                            jQueryHC('#import_hc strong').text('<?php _e('Error when trying to generate XML', 'hypercomments' ); ?>');
                            jQueryHC('#import_hc').show(function(){
                                    jQueryHC(this).delay(30000).fadeOut();
                            });
                        }
                        if(posts.length > 0) query(posts);         
                                                                  
                    },
                error: function(data){                  
                        var dom = '<tr><td><b>Page ID: '+page+'</b></td><td><img src="<?php echo HC_PLUGIN_URL;?>/css/button_no.png" alt="" /></td><td class="importpage_'+page+'"><img src="<?php echo HC_PLUGIN_URL;?>/css/button_no.png" alt="" /></td></tr>';
                        jQueryHC('#import_report tbody').append(dom);
                        if(posts.length > 0){
                            query(posts);
                            jQueryHC('#load_import').hide();
                        } 
                    }   
            };
            jQueryHC.ajax(get_param);
        
        };
        query(posts);    
        return false;
    });
     
    function sendNotify(obj){  
        jQueryHC('.e_import_comments p').last().show();
        jQueryHC.getJSON('<?php echo HC_URL;?>/api/import?response_type=callback&callback=?',obj,
        function (data) {
            if(data.result == 'success'){
                jQueryHC('.importpage_'+obj.pageID).html('<img src="<?php echo HC_PLUGIN_URL;?>/css/button_ok.png" alt="" />');                     
            }else{                          
                jQueryHC('.importpage_'+obj.pageID).html('<img src="<?php echo HC_PLUGIN_URL;?>/css/button_no.png" alt="" />');                              
            }              
        });          
    }    
      
    jQueryHC('#sub_settings').click(function(){
        var site_auth = jQueryHC('input[name=hc_synch]').is(':checked');
        jQueryHC.getJSON('<?php echo HC_URL;?>/widget/auth?response_type=callback&callback=?',
        {site_auth:site_auth,wid:<?php echo (get_option('hc_wid')) ? get_option('hc_wid') : '" "';?>,notify_url:"<?php echo admin_url('index.php');?>?hc_action=notify"},
        function (data) {
            if(data.result == 'success'){      
                jQueryHC('input[name=hc_secret_key]').val(data.secret_key);
                jQueryHC('#form_settings').submit();                     
            }else{                      
                jQueryHC('#import_hc strong').text(data.description);
                jQueryHC('#import_hc').show(function(){
                    jQueryHC(this).delay(30000).fadeOut();
                });   
                     
            }              
        });    
        return false;
      })
 });
     
</script>