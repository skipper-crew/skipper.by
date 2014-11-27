<?php
/**
 * XML Sitemap Feed Template for displaying an XML Sitemap feed.
 *
 * @package Google News Sitemap Feed With Multisite Support plugin for WordPress
 */

/* By gunter [dot] sammet [at] gmail [dot] com http://www.php.net/manual/en/function.htmlentities.php#88169 */
$entity_custom_from = false; 
$entity_custom_to = false;
function html_entity_decode_encode_rss($data) {
	global $entity_custom_from, $entity_custom_to;
	
	if(!is_array($entity_custom_from) || !is_array($entity_custom_to)) {
		$array_position = 0;
		foreach (get_html_translation_table(HTML_ENTITIES) as $key => $value) {
			switch ($value) {
				case '&nbsp;':
					break;
				case '&gt;':
				case '&lt;':
				case '&quot;':
				case '&apos;':
				case '&amp;':
					$entity_custom_from[$array_position] = $key; 
					$entity_custom_to[$array_position] = $value; 
					$array_position++; 
					break; 
				default: 
					$entity_custom_from[$array_position] = $value; 
					$entity_custom_to[$array_position] = $key; 
					$array_position++; 
			} 
		}
	}
	return str_replace($entity_custom_from, $entity_custom_to, $data); 
}

status_header('200'); // force header('HTTP/1.1 200 OK') for sites without posts
header('Content-Type: text/xml; charset=' . get_bloginfo('charset'), true);

echo '<?xml version="1.0" encoding="'.get_bloginfo('charset').'"?>
<!-- generated-on="'.date('Y-m-d\TH:i:s+00:00').'" -->
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">';

// Register the filtering function
add_filter('posts_where', array('XMLSitemapFeed','xml_sitemap_feed_news_filter_where'), 10, 1 );
add_filter('post_limits', array('XMLSitemapFeed','xml_sitemap_feed_news_filter_limits'), 10, 1 );

// Perform the query, the filter will be applied automatically
query_posts( array(
	'post_type' => 'post', 
	'ignore_sticky_posts' => 1,
	'nopaging' => true,
	'posts_per_page' => -1 )
); 

global $wp_query;
$wp_query->is_404 = false;	// force is_404() condition to false when on site without posts
$wp_query->is_feed = true;	// force is_feed() condition to true so WP Super Cache includes the sitemap in its feeds cache

if ( have_posts() ) : while ( have_posts() ) : the_post();

	// check if we are not dealing with an external URL :: Thanks, Francois Deschenes :)
	if(!preg_match('/^' . preg_quote(get_bloginfo('url'), '/') . '/i', get_permalink())) continue;

	unset($keywords);
	// default keywords can be added here
	// keyword list is on http://www.google.com/support/news_pub/bin/answer.py?answer=116037
	// $keywords = 'Entertainment, Celebrities, ';

	// any custom taxonomy can be included in the keywords here
	// $terms = get_the_terms( $post->id, $taxonomy );
	// if ($terms) foreach($terms as $term) $keywords = $keywords . $term->name . ', ';

	$terms = get_the_tags();
	if ($terms) foreach($terms as $term) $keywords = $keywords . $term->name . ', ';

	$terms = get_the_category();
	if ($terms) foreach($terms as $term) if (strcasecmp($term->name, 'Uncategorized') != 0) $keywords = $keywords . $term->name . ', ';

	if ($keywords) $keywords=substr($keywords, 0, -2);

?><url><loc><?php echo esc_url( get_permalink() ) ?></loc><news:news><news:publication><news:name><?php bloginfo('name'); ?></news:name><news:language><?php echo get_option('rss_language'); ?></news:language></news:publication><news:publication_date><?php echo mysql2date('Y-m-d\TH:i:s+00:00', $post->post_date_gmt, false); ?></news:publication_date><news:title><?php echo html_entity_decode_encode_rss(html_entity_decode(get_the_title(), ENT_QUOTES, 'UTF-8'));?></news:title><news:keywords><?php echo $keywords; ?></news:keywords><news:genres>Blog</news:genres></news:news></url><?php 

endwhile; endif; 

?></urlset>
