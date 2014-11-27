<?php
/*
Plugin Name: Menu User Tools
Plugin URI: http://webmasterguy.com
Description: Add conditional Login&#47;Logout & Register&#47;My Profile links &#40;selectively&#41; to your custom WordPress Menu&#40;s&#41;.
Version: 1.0
Author: Luke Wiedmer
Author URI: http://webmasterguy.com
License: GPL
*/

add_action('wp_nav_menu_items', 'add_menu_user_tools');

add_filter('wp_nav_menu_items', 'add_menu_user_tools', 10, 2);

function add_menu_user_tools($items, $args) {

	if($args->theme_location == 'top-menu') {
 
        ob_start();

	wp_register();

	$registerlink = ob_get_contents();

        ob_end_clean();

	ob_start();

	$items .= '<li>'. $registerlink.'</li>';

	wp_loginout('index.php');

	$loginoutlink = ob_get_contents();
	
	ob_end_clean();
 
        $items .= '<li>'. $loginoutlink.'</li>';
 
    return $items; }

	else {
		return $items;
	}

}

add_action( 'register' , 'customize_reg_text' );

function customize_reg_text( $link ){
	if ( ! is_user_logged_in() ) {
		if ( get_option('users_can_register') )
			$link = $before . '<a href="' . site_url('wp-login.php?action=register', 'login') . '">' . __('Register') . '</a>' . $after;
		else
			$link = '';
	} else {
		$link = $before . '<a href="' . admin_url() . '">' . __('My Profile') . '</a>' . $after;
	}
	return $link;
}

add_action('admin_menu', 'menu_user_tools_menu');

function menu_user_tools_menu() {
	add_options_page('Menu User Tools', 'Menu User Tools', 'manage_options', 'Menu-User-Tools', 'menu_user_tools_options');
}

function menu_user_tools_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

	echo '<div class="wrap">';
	echo '<h2>Menu User Tools Options... Possibly coming soon</h2>';
	echo '<h3>Plugin Author: <a href="http://webmasterguy.com/about" target="_blank">Luke Wiedmer</a></h3>';
	echo '<p><form action="https://www.paypal.com/cgi-bin/webscr" method="post">

<input type="hidden" name="cmd" value="_s-xclick">

<input type="hidden" name="hosted_button_id" value="9N7PJ4C9RRE3Y">

<input type="image" src="https://www.paypalobjects.com/WEBSCR-640-20110429-1/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!"></form> If this plugin has helped you, then by all means, enjoy it! &#58;&#41;<br /><br /><a href="http://webmasterguy.com/contact" target="_blank">Questions&#63; Comments&#63;</a></p>';
	echo '<p>This plugin allows you to add the conditional Login&#47;Logout, and Register&#47;My Profile links to any <br />custom WordPress menu you&#39;ve created. You can add them selectively, and&#47;or to more than one menu if necessary. &#40;Requires more code, which I won&#39;t provide.&#41;<br /></p>';
	echo '<br />';
	echo '<p>I haven&#39;t coded an &#39;admin backend&#39; for this plugin yet, it&#39;s simply the core functions as of now.<br />I coded this for my own website <a href="http://webmasterguy.com">Web Master Guy</a> in which I needed such a plugin.</p>';					
	echo '<br />';
	echo '<p>To edit which menu it selects through the plugins IF statement, open &#39;Menu User Tools.php&#39; and find this code:</p>';
	echo '<code>if(&#36;args&#45;&#62;theme&#95;location &#61;&#61; &#39;top&#45;menu&#39;)</code>';
	echo '<p>Change top&#45;menu to the menu slug of the menu you would like to have Menu User Tools hook onto.</p>';
	echo '<br />';
	echo '<p>You can easily have Menu User Tools hook onto all menus, simply by removing the above IF statement in the plugin file.</p>';
	echo '<p>If you want more functionality out of this plugin you&#39;re going to have to code it yourself for now.</p>';
	echo '</div>';
}
?>