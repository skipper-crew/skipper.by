<?php
/*
    WP Posts per Cat list titles of recent posts in boxes for all single categories
    Copyright (C) 2009-2012 Aleksandar Urošević <urke@users.sourceforge.net>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

Plugin Name: Posts per Cat
Plugin URI: http://blog.urosevic.net/wordpress/posts-per-cat/
Description: Group latest posts by selected category and show post titles w/ or w/o excerpt, featured image and comments number in boxes organized in one, two, three or four columns.
Version: 1.1.0
Author: Aleksandar Urošević
Author URI: http://urosevic.net
License: GNU GPLv3
*/

define( 'POSTS_PER_CAT_VER', '1.1.0' );
define( 'POSTS_PER_CAT_URL', plugin_dir_url(__FILE__) );

// add PPC button to admin menu
add_action( 'admin_menu', 'ppc_postspercat_menu' );
// initialize PPC
add_action( 'ppc', 'ppc_echo');
// define shortcode
add_shortcode( 'ppc', 'posts_per_cat' );

function ppc_echo() { echo posts_per_cat(); }

// insert Settings link on plugins admin page
if ( is_admin() ) {
	$plugin = plugin_basename(__FILE__); 
	add_filter('plugin_action_links_'.$plugin, 'addConfigureLink' );
}
function addConfigureLink( $links ) { 
	$settings_link = '<a href="options-general.php?page=posts-per-cat">'.__('Settings').'</a>'; 
	array_unshift( $links, $settings_link ); 
	return $links; 
}

// start PPC
if ( function_exists('posts_per_cat') ) {
	$blog_url = get_bloginfo('url');
	// init textdomain for localisation
	load_plugin_textdomain( 'ppc', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	// call PPC CSS header insertion
	add_filter('wp_head', 'ppc_header_css', 10);
}

// insert PPC CSS in page head
function ppc_header_css() {
	echo '<link rel="stylesheet" href="'.POSTS_PER_CAT_URL.'ppc.css" type="text/css" media="screen" />';
	$options = get_option('postspercat');
	if ( $options['ppccss'] ) {
		echo '<link rel="stylesheet" href="'.POSTS_PER_CAT_URL.'ppc-list.css" type="text/css" media="screen" />';
	}
?>
<style type="text/css" media="screen">
.ppc .attachment-<?php echo $options['tsize'].'x'.$options['tsize']; ?> {
	width: <?php echo $options['tsize']; ?>px !important;
	height: <?php echo $options['tsize']; ?>px !important;
}
</style>
<?php
}

/* prepare Options page */
function ppc_postspercat_menu() {
	add_options_page(__('Posts per Cat Options', 'ppc'),  __('Posts per Cat', 'ppc'), 'manage_options', 'posts-per-cat', 'ppc_postspercat_options');
}


function ppc_postspercat_options() {
	// update options on submit
	if ( isset($_POST['ppc-submit']) ) {
		$options['posts']    = htmlspecialchars($_POST['ppc-posts']);
		$options['titlelen'] = htmlspecialchars($_POST['ppc-titlelen']);
		$options['shorten']  = isset($_POST['ppc-shorten']);
		$options['excerpt']  = htmlspecialchars($_POST['ppc-excerpt']);
		$options['content']  = isset($_POST['ppc-content']);
		$options['excleng']  = htmlspecialchars($_POST['ppc-excleng']);
		$options['parent']   = isset($_POST['ppc-parent']);
		$options['order']    = htmlspecialchars($_POST['ppc-order']);
		$options['nosticky'] = isset($_POST['ppc-nosticky']);
		$options['include']  = htmlspecialchars($_POST['ppc-include']);
		$options['exclude']  = htmlspecialchars($_POST['ppc-exclude']);
		$options['ppccss']   = isset($_POST['ppc-ppccss']);
		$options['minh']     = htmlspecialchars($_POST['ppc-minh']);
		$options['column']   = htmlspecialchars($_POST['ppc-column']);
		$options['noctlink'] = isset($_POST['ppc-noctlink']);
		$options['more']     = isset($_POST['ppc-more']);
		$options['moretxt']  = htmlspecialchars($_POST['ppc-moretxt']);
		$options['thumb']    = isset($_POST['ppc-thumb']);
		$options['tsize']    = htmlspecialchars($_POST['ppc-tsize']);
		$options['catonly']  = isset($_POST['ppc-catonly']);
		$options['commnum']  = isset($_POST['ppc-commnum']);

		update_option('postspercat', $options);
	}

	// get options
	$options = get_option('postspercat');
	// set default options
	if ( !is_array( $options ) ) {
		$options = array(
			'posts'    => '5',
			'titlelen' => '',
			'shorten'  => false,
			'excerpt'  => 'none',
			'content'  => false,
			'excleng'  => '100',
			'parent'   => false,
			'order'    => 'ID',
			'include'  => '',
			'exclude'  => '',
			'ppccss'   => true,
			'minh'     => '',
			'nosticky' => false,
			'column'   => '2',
			'noctlink' => false,
			'more'     => false,
			'moretxt'  => __('More from', 'ppc'),
			'thumb'    => false,
			'tsize'    => '60',
			'catonly'  => false,
			'commnum'  => false
		);
		update_option('postspercat', $options);
	}
	// Now print Options page
?>
<script type="text/javascript">

jQuery(document).ready(function($){
	$('.allcats').hide();
	$('#togglecattable').click(function(){
		$('.allcats').fadeToggle('slow');
		btn_title = $('#togglecattable').val();
		if ( btn_title == "<?php _e('Show available categories', 'ppc'); ?>" ) {
			$('#togglecattable').val("<?php _e('Hide available categories', 'ppc'); ?>");
		} else {
			$('#togglecattable').val("<?php _e('Show available categories', 'ppc'); ?>");
		}
	});
});

</script>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2><?php echo sprintf(__('Posts per Cat v%s', 'ppc'), POSTS_PER_CAT_VER); ?></h2>
	<form method="post" action="" id="ppc-conf">
	<?php if (function_exists('wp_nonce_field')) { wp_nonce_field('ppc-updatesettings'); } ?>
	<p><?php _e('This plugin list latest N articles from all, top level only or manually choosen categories and group them in category boxes organized in one, two, three or four columns.', 'ppc'); ?></p>
	<h3><?php _e('Usage', 'ppc'); ?></h3>
	<p><?php _e('Put next code to template files in place where you wish to display PPC boxes (but not in Loop!):', 'ppc'); ?></p>
	<code>&lt;?php do_action('ppc'); ?&gt;</code>
	<p><?php _e('You can even use shortcode. Simply put <strong>[ppc]</strong> in post or page content.', 'ppc');?></p>
	<p><?php _e('If you wish to show Posts per Cat in widget, use shortcode in text widget. If theme you use does not support shortcodes in widget, simply put next code in <em>functions.php</em> of your theme:', 'ppc');?></p>
	<code>add_filter('widget_text', 'do_shortcode');</code>
	<h3><?php _e('Boxes', 'ppc'); ?></h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label><?php _e('Number of columns', 'ppc'); ?></label></th>
			<td>
				<input type="radio" id="ppc-column" name="ppc-column" value="1" <?php checked( $options['column'], 1 ); ?>/> <?php _e('One column per row (full width)', 'ppc'); ?><br />
				<input type="radio" id="ppc-column" name="ppc-column" value="2" <?php checked( $options['column'], 2 ); ?>/> <?php _e('Two columns per row', 'ppc'); ?><br />
				<input type="radio" id="ppc-column" name="ppc-column" value="3" <?php checked( $options['column'], 3 ); ?>/> <?php _e('Three columns per row', 'ppc'); ?><br />
				<input type="radio" id="ppc-column" name="ppc-column" value="4" <?php checked( $options['column'], 4 ); ?>/> <?php _e('Four columns per row', 'ppc'); ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label><?php _e('Minimal height of box', 'ppc'); ?></label></th>
			<td><input type="text" value="<?php echo $options['minh']; ?>" name="ppc-minh" id="ppc-minh" size="2" /> <?php _e('px (leave empty to disable min-height)', 'ppc'); ?></td>
		</tr>
	</table>

	<h3><?php _e('Categories', 'ppc'); ?></h3>
	<table class="form-table">
		<tr valign="top">
		<th scope="row"><input type="button" id="togglecattable" class="button-secondary" value="<?php _e('Show available categories', 'ppc'); ?>" /> <!-- onclick="jQuery('.allcats').slideToggle('slow');" / --></th>
			<td>
				<table class="wp-list-table widefat fixed allcats" cellspacing="0">
					<thead>
						<?php print_cat_headfoot(); ?>
					</thead>
					<tfoot>
						<?php print_cat_headfoot(); ?>
					</tfoot>
					<tbody class="list:tag">
						<?php
						// get top level categories
						$categories = get_categories('hide_empty=0&orderby=id&parent=0');
						foreach( $categories as $category ) {
							print_cat_row($category);
							// get subcategories
							$subcats = get_categories('hide_empty=0&orderby=id&child_of='.$category->term_id);
							if ( $subcats ) {
								foreach( $subcats as $subcat ) {
									if ( $subcat != '' ) { // print subcategory row if subcat is not empty
										print_cat_row($subcat);
									}
								} // foreach $subcats
							} // if $subcats
						} // foreach $categories
						?>
					</tbody>
				</table>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label><?php _e('Include category', 'ppc'); ?></label></th>
			<td><input type="text" value="<?php echo ($options['include']); ?>" name="ppc-include" id="ppc-include" /> <?php _e('comma separated category ID\'s', 'ppc'); ?></td>
		</tr>
		<tr valign="top">
			<th scope="row"></label><?php _e('Exclude category', 'ppc'); ?></label></th>
			<td><input type="text" value="<?php echo ($options['exclude']); ?>" name="ppc-exclude" id="ppc-exclude" /> <?php _e('comma separated category ID\'s', 'ppc'); ?></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label><?php _e('Only top level categories', 'ppc'); ?></label></th>
			<td><input type="checkbox" <?php checked( (bool) $options['parent'], true ); ?> name="ppc-parent" id="ppc-parent" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label><?php _e('Order categories by', 'ppc'); ?></label></th>
			<td>
				<input type="radio" id="ppc-order" name="ppc-order" value="ID" <?php checked( $options['order'], "ID" ); ?>/> <?php _e('Category ID', 'ppc'); ?><br />
				<input type="radio" id="ppc-order" name="ppc-order" value="name" <?php checked( $options['order'], "name" ); ?>/> <?php _e('Category Name', 'ppc'); ?><br />
				<input type="radio" id="ppc-order" name="ppc-order" value="custom" <?php checked( $options['order'], "custom" ); ?>/> <?php echo sprintf(__('Custom, as listed in <em>%s</em>', 'ppc'), __('Include category', 'ppc') ); ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label><?php _e('Only from displayed category archive', 'ppc'); ?></label></th>
			<td><input type="checkbox" <?php checked( (bool) $options['catonly'], true ); ?> name="ppc-catonly" id="ppc-catonly" /> <?php _e('exclude categories different from currently displayed on category archive and ignore first category rules on category archive', 'ppc'); ?></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label><?php _e('Do not link category name', 'ppc'); ?></label></th>
			<td><input type="checkbox" <?php checked( (bool) $options['noctlink'], true ); ?> name="ppc-noctlink" id="ppc-noctlink" /> <?php _e('leave unchecked to link category title to archive', 'ppc'); ?></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label><?php _e('Standalone link to archives', 'ppc'); ?></label></th>
			<td><input type="checkbox" <?php checked( (bool) $options['more'], true ); ?> name="ppc-more" id="ppc-more" /> <?php _e('check to print "read more" link bellow list of headlines', 'ppc'); ?></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label><?php _e('Archive link prefix', 'ppc'); ?></label></th>
			<td><input type="text" value="<?php echo ($options['moretxt']) ? $options['moretxt'] : _e('More from', 'ppc'); ?>" name="ppc-moretxt" id="ppc-moretxt" size="25" /></td>
		</tr>
	</table>

	<h3><?php _e('Headlines', 'ppc'); ?></h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label><?php _e('Number of headlines', 'ppc'); ?></label></th>
			<td><input type="text" value="<?php echo $options['posts']; ?>" name="ppc-posts" id="ppc-posts" size="2" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label><?php _e('Headline length', 'ppc'); ?></label></th>
			<td><input type="text" value="<?php echo $options['titlelen']; ?>" name="ppc-titlelen" id="ppc-titlelen" size="2" /> <?php _e('leave blank for full post title length, optimal 34 characters', 'ppc'); ?></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label><?php _e('Shorten headline', 'ppc'); ?></label></th>
			<td><input type="checkbox" <?php checked( (bool) $options['shorten'], true ); ?> name="ppc-shorten" id="ppc-shorten" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label><?php _e('Display comment number', 'ppc'); ?></label></th>
			<td><input type="checkbox" <?php checked( (bool) $options['commnum'], true ); ?> name="ppc-commnum" id="ppc-commnum" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label><?php _e('Hide sticky posts', 'ppc'); ?></label></th>
			<td><input type="checkbox" <?php checked( (bool) $options['nosticky'], true ); ?> name="ppc-nosticky" id="ppc-nosticky" /></td>
		</tr>
	</table>

	<h3><?php _e('Content', 'ppc'); ?></h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label><?php _e('Show excerpt', 'ppc'); ?></label></th>
			<td>
				<input type="radio" id="ppc-excerpt" name="ppc-excerpt" value="none" <?php checked( $options['excerpt'], 'none' ); ?>/> <?php _e('Don\'t display', 'ppc'); ?><br />
				<input type="radio" id="ppc-excerpt" name="ppc-excerpt" value="first" <?php checked( $options['excerpt'], 'first' ); ?>/> <?php _e('For first article only', 'ppc'); ?><br />
				<input type="radio" id="ppc-excerpt" name="ppc-excerpt" value="all" <?php checked( $options['excerpt'], 'all' ); ?>/> <?php _e('For all articles', 'ppc'); ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label><?php _e('Use post content as excerpt', 'ppc'); ?></label></th>
			<td><input type="checkbox" <?php checked( (bool) $options['content'], true ); ?> name="ppc-content" id="ppc-content" /> <?php _e('use post content in stead of post excerpt', 'ppc'); ?></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label><?php _e('Excerpt length', 'ppc'); ?></label></th>
			<td><input type="text" value="<?php echo $options['excleng']; ?>" name="ppc-excleng" id="ppc-excleng" size="2" /> <?php _e('leave empty for full excerpt length', 'ppc'); ?></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label><?php _e('Show thumbnail with excerpt', 'ppc'); ?></label></th>
			<td><input type="checkbox" <?php checked( (bool) $options['thumb'], true ); ?> name="ppc-thumb" id="ppc-thumb" /> <?php _e('thumbnail is shown only if theme support it, and excerpt is enabled', 'ppc'); ?></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label><?php _e('Thumbnail size', 'ppc'); ?></label></th>
			<td><input type="text" value="<?php echo ($options['tsize']) ? $options['tsize'] : "60"; ?>" name="ppc-tsize" id="ppc-tsize" size="2" /> <?php _e('enter size in px for thumbnail width (height is same)', 'ppc'); ?></td>
		</tr>
	</table>

	<h3><?php _e('Styling', 'ppc'); ?></h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label><?php _e('Use PPC for styling list?', 'ppc'); ?></label></th>
			<td><input type="checkbox" <?php checked( (bool) $options['ppccss'], true ); ?> name="ppc-ppccss" id="ppc-ppccss" /> <?php _e('enable this option if U see ugly lists in PPC boxes', 'ppc'); ?></td>
		</tr>
	</table>

	<input type="hidden" name="action" value="update" />

	<p class="submit">
		<input type="submit" name="ppc-submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>

	</form>
	<table class="widefat">
		<thead><tr><th><?php _e('Support', 'ppc'); ?></th></tr></thead>
		<tbody>
		<tr>
			<td>
				<p><?php echo sprintf(__('For all questions, feature request and communication with author and users of this plugin, use our <a href="%s">support forum</a>.', 'ppc'), 'http://wordpress.org/tags/posts-per-cat?forum_id=10'); ?>
				<p><?php echo sprintf(__('If you like <a href="%s">Posts per Cat</a> and my other <a href="%s">WordPress extensions</a>, feel free to support my work with <a href="%s">donation</a>.', 'ppc'), 'http://wordpress.org/extend/plugins/posts-per-cat/', 'http://profiles.wordpress.org/users/urkekg/', 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=Q6Q762MQ97XJ6'); ?></p>
			</td>
		</tr>
		</tbody>
	</table>

</div>
<?php
}

function posts_per_cat() {
	global $blog_url;
	
	$options      = get_option('postspercat');
	$ppc_posts    = $options['posts'];   // number of posts to list
	$ppc_shorten  = $options['shorten']; // do we need to cut down titles
	$ppc_titlelen = $options['titlelen'];// length of title
	$ppc_parent   = $options['parent'];  // list only top level categories
	$ppc_excerpt  = $options['excerpt']; // where to print excerpts
	$ppc_content  = $options['content']; // use post content in stead of post excerpt
	$ppc_excleng  = $options['excleng']; // length of excerpt
	$ppc_order    = $options['order'];   // category ordering: id, name or custom
	$ppc_nosticky = $options['nosticky'];// include sticky posts?
	$ppc_noctlink = $options['noctlink'];  // disable link for category title
	$ppc_more     = $options['more'];    // print link for category archive
	$ppc_moretxt  = $options['moretxt']; // prefix for category archive link text
	$ppc_include  = $options['include']; // list only this categories
	$ppc_exclude  = $options['exclude']; // do not list this categories
	$ppc_thumb    = $options['thumb'];   // display thumbnail with excerpt?
	$ppc_catonly  = $options['catonly']; // only posts from current category in archive
	$ppc_commnum  = $options['commnum']; // display comment number
	
	if ( $options['tsize'] != "60" ) { // setup thumbnail size
		$ppc_tsize = array($options['tsize'],$options['tsize']); // custom
	} else {
		$ppc_tsize = array(60,60); // default
	}
	switch ( $options['column'] ) { // setup number of columns
		case 1: $ppc_column = "one"; break;
		case 3: $ppc_column = "three"; break;
		case 4: $ppc_column = "four"; break;
		default: $ppc_column = "two";
	}

	// do we need to force minimal height of box?
	if ( $options['minh'] > 0 ) {
		$ppc_minh = 'style="min-height: '.$options['minh'].'px !important;"';
	} else {
		$ppc_minh = "";
	}

	// do we need to display only current category on archive?
	if ( $ppc_catonly && is_category() && !is_home() ) {
		$cats = get_categories('orderby='.$ppc_order.'&include='.get_query_var('cat'));
	} else {
		// custom or other category ordering?
		if ( $ppc_order == "custom" ) { // custom
			$custom_order = split(",", $ppc_include);
			foreach ( $custom_order as $custom_order_cat_ID ) {
				$custom_order_cat_object = get_categories('include='.$custom_order_cat_ID);
				$custom_order_cat[] = $custom_order_cat_object[0];
			}
			$cats = $custom_order_cat;
		} else { // by cat_ID or name
			$cats = get_categories('orderby='.$ppc_order.'&include='.$ppc_include.'&exclude='.$ppc_exclude);
		}
	}
	// set number of boxes for clear fix
	$boxnum = 0;
	// print PPC body header
	$ppc_str .= '
<!-- start of Posts per Cat version '.POSTS_PER_CAT_VER.' -->
	<div id="ppc-box">
';

	foreach ( $cats as $cat ) { // process all category
		// get only non-empty categories
		if ( $cat->count > 0 && ( ($ppc_parent == True && $cat->category_parent == 0) || $ppc_parent == False || ( $ppc_catonly == True && is_category() ) ) ) {
			// where to put category archive link
			if ( $ppc_more ) { // add more link
				$ppc_cattitle = $cat->cat_name;
				$ppc_moreadd = '<div class="ppc-more"><a href="'.get_category_link( $cat->cat_ID ).'">'.$ppc_moretxt.' '.__('&#8220;', 'ppc').$ppc_cattitle.__('&#8221;', 'ppc').'</a></div>';
			} else { // on category title
				if ( $ppc_noctlink ) {
					$ppc_cattitle = '<a href="'.get_category_link( $cat->cat_ID ).'">'.$cat->cat_name.'</a>';
					$ppc_moreadd = "";
				} else {
					$ppc_cattitle = $cat->cat_name;
					$ppc_moreadd = "";

				}
			}
			// start category box
			$ppc_str .= '
		<!-- start of Category Box -->
		<div class="ppc-box '.$ppc_column.'">
			<div class="ppc" '.$ppc_minh.'>
			<h3>'.$ppc_cattitle.'</h3>
			<ul>';

			// get latest N posts from category $cat
			if ( $ppc_nosticky ) { // exclude sticky posts
				$posts = get_posts(array(
					'post__not_in' => get_option("sticky_posts"),
					'numberposts' => $ppc_posts,
					'order' => "DSC",
					'orderby' => "date",
					'category' => $cat->cat_ID
				));
			} else { // include sticky posts
				$posts = get_posts('numberposts='.$ppc_posts.'&order=DSC&orderby=date&category='.$cat->cat_ID);
			}
			
			$br = 0; // control number for number of excerpts

			// process all posts from category
			foreach ( $posts as $post ) {
				// do we need to cut down post title?
				if ( $ppc_shorten ) {
					if ( $ppc_titlelen && mb_strlen_dh($post->post_title) > ($ppc_titlelen+1) ) {
						$title = substr_utf8($post->post_title, 0, $ppc_titlelen)."&hellip;";
					} else {
						$title = $post->post_title;
					}
					$title_full = $post->post_title;
				} else {
					$title_full = $title = $post->post_title;
				}

				$ppc_str .= '
				<li><a href="'.get_permalink($post->ID).'" class="ppc-post-title" title="'.sprintf(__('Article %s published at %s', 'ppc'), $title_full, date_i18n(__('F j, Y g:i a'), strtotime($post->post_date)) ).'">'.$title.'</a>';
				if ( $ppc_commnum ) {
					$comments_num = get_comments_number($post->ID);
						$ppc_str .= ' <span class="ppc-comments-num">(<a href="'.get_permalink($post->ID);
					if ( $comments_num == 0 ) {
						$ppc_str .= '#respond" title="'.sprintf(__('Be first to comment %s', 'ppc'), $title_full);
					} else {
						$ppc_str .= '#comments" title="'.sprintf(__('Read comments on %s', 'ppc'), $title_full);
					}
					$ppc_str .= '">'.get_comments_number($post->ID).'</a>)</span>';
				}
				if ( $ppc_content ) {
					$excerpt = strip_tags(substr_utf8($post->post_content, 0, 500)).'&hellip;';
					if ( $ppc_excleng && mb_strlen_dh($excerpt) > ($ppc_excleng+1) ) {
						$excerpt = substr_utf8($excerpt, 0, $ppc_excleng).'&hellip;';
					}
				} else {
					if ( $ppc_excleng && mb_strlen_dh($post->post_excerpt) > ($ppc_excleng+1) ) {
						$excerpt = substr_utf8($post->post_excerpt, 0, $ppc_excleng).'&hellip;';
					} else {
						$excerpt = $post->post_excerpt;
					}
				}

				if ( $br++ == 0 && ($ppc_excerpt == 'first') ) { // print excerpt for first post
					$ppc_str .= '<p>';
					if ( $ppc_thumb ) { // print thumbnail
						if ( function_exists('has_post_thumbnail') && has_post_thumbnail($post->ID) ) {
							$ppc_str .= wp_get_attachment_image( get_post_thumbnail_id($post->ID), $ppc_tsize);
						}
					}
					$ppc_str .= $excerpt.'</p>';
				} elseif ( $br++ > 0 && $ppc_excerpt == 'all' ) { // print excerpt for other posts
					$ppc_str .= '<p>';
					if ( $ppc_thumb ) { // print thumbnails
						if ( function_exists('has_post_thumbnail') && has_post_thumbnail($post->ID) ) {
						$ppc_str .= wp_get_attachment_image( get_post_thumbnail_id($post->ID), $ppc_tsize ); }
					}
					$ppc_str .= $excerpt.'</p>';
				}
				$ppc_str .= '</li>';
			} // end of processing every post from category $cat

			// close category box
			$ppc_str .= '
			</ul>
			'.$ppc_moreadd.'
			</div>
		</div>
		<!-- end of Category Box -->
			';

			// print row cleaner
			$boxnum++;
			if ( $boxnum == $options['column'] ) {
				$ppc_str .= '<div class="clear"></div>';
				$boxnum = 0;
			} // boxnum equal to number of columns
		} // end of processing non-empty categories
	} // end foreach $cats as $cat

	// print row cleaner at end of PPC
	if ( $boxnum != $options['column'] ) {
		$ppc_str .= '<div class="clear"></div>';
	}
	
	// close PPC container
	$ppc_str .= '
	</div>
<!-- end of Posts per Cat -->
';

return $ppc_str;
} // posts_per_cat()

// category table row print
function print_cat_row($category) {
?>
	<tr>
		<td class="name column-name">
		<a href="edit-tags.php?action=edit&taxonomy=category&tag_ID=<?php echo $category->cat_ID; ?>&post_type=post"><?php if ( $category->parent != 0 ) { echo "— "; } echo $category->cat_name; ?></a></td>
		<td class="column-description description"><?php echo $category->category_description; ?></td>
		<td class="slug column-slug"><?php echo $category->slug; ?></td>
		<td class="num column-rating"><?php echo $category->cat_ID; ?></td>
		<td class="num column-posts"><a href="edit.php?category_name=<?php echo $category->slug; ?>&post_type=post"><?php echo $category->count; ?></a></td>
	</tr>
<?php
}

function print_cat_headfoot() {
?>
			<tr>
				<th scope="col" class="manage-column column-name row-title"><?php _e('Name'); ?></th>
				<th scope="col" class="manage-column column-description desc row-title"><?php _e('Description'); ?></th>
				<th scope="col" class="manage-column column-slug slug row-title"><?php _e('Slug'); ?></th>
				<th scope="col" class="manage-column column-rating num row-title"><?php _e('ID'); ?></th>
				<th scope="col" class="manage-column column-posts num row-title"><?php _e('Posts'); ?></th>
			</tr>
<?php
}
// unicode substr workaround from http://en.jinzorahelp.com/forums/viewtopic.php?f=18&t=6231
function substr_utf8($str,$from,$len)
{
	# utf8 substr
	# http://www.yeap.lv
	return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}'.
'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s',
'$1',$str);
}

// dirty hack for missing mb_strlen() found at http://www.php.net/manual/en/function.mb-strlen.php#87114
function mb_strlen_dh($utf8str)
{
	if ( function_exists('mb_strlen') ) {
		return mb_strlen($utf8str);
	} else {
		return preg_match_all('/.{1}/us', $utf8str, $dummy);
	}
}
?>
