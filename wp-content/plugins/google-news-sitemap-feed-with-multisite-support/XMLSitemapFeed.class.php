<?php
/* ------------------------------
 *      XMLSitemapFeed CLASS
 * ------------------------------ */

class XMLSitemapFeed {

	function go() {		
		if ( $wpdb->blogid && function_exists('get_site_option') && get_site_option('tags_blog_id') == $wpdb->blogid ) {
			// we are on wpmu and this is a tags blog!
			// create NO sitemap since it will be full 
			// of links outside the blogs own domain...
		} else {
			// INIT
			add_action('init', array(__CLASS__, 'init') );
	
			// FEED
			add_action('do_feed_sitemap-news', array(__CLASS__, 'load_template_sitemap_news'), 10, 1);

			// REWRITES
			add_filter('generate_rewrite_rules', array(__CLASS__, 'rewrite') );
		}

		// DE-ACTIVATION
		register_deactivation_hook( XMLSF_PLUGIN_DIR . '/xml-sitemap.php', array(__CLASS__, 'deactivate') );
	}

	// set up the news sitemap template
	function load_template_sitemap_news() {
		load_template( XMLSF_PLUGIN_DIR . '/feed-sitemap-news.php' );
	}

	// Create a new filtering function that will add a where clause to the query,
	// used for the Google News Sitemap
	function xml_sitemap_feed_news_filter_where($where = '') {
	  //posts in the last 2 days
	  $where .= " AND post_date > '" . date('Y-m-d', strtotime('-2 days')) . "'";
	  return $where;
	}

	function xml_sitemap_feed_news_filter_limits($limits) {
	  // maximum number of URLs allowed in a news sitemap
	  $limits = ' LIMIT 1000';
	  return $limits;
	}

	// REWRITES //
	// add sitemap rewrite rules
	function rewrite($wp_rewrite) {
		$feed_rules = array(
			'sitemap-news.xml$' => $wp_rewrite->index . '?feed=sitemap-news',
		);
		$wp_rewrite->rules = $feed_rules + $wp_rewrite->rules;
	}

	// DE-ACTIVATION
	function deactivate() {
		remove_filter('generate_rewrite_rules', array(__CLASS__, 'rewrite') );
		delete_option('gn-sitemap-feed-mu-version');
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}

	// MULTI-LANGUAGE PLUGIN FILTERS

	// qTranslate
	function qtranslate($input) {
		global $q_config;

		if (is_array($input)) // got an array? return one!
			foreach ( $input as $url )
				foreach($q_config['enabled_languages'] as $language)
					$return[] = qtrans_convertURL($url,$language);
		else // not an array? just convert the string.
			$return = qtrans_convertURL($input);

		return $return;
	}

	// xLanguage
	function xlanguage($input) {
		global $xlanguage;
	
		if (is_array($input)) // got an array? return one!
			foreach ( $input as $url )
				foreach($xlanguage->options['language'] as $language)
					$return[] = $xlanguage->filter_link_in_lang($url,$language['code']);
	 	else // not an array? just convert the string.
	       		$return = $xlanguage->filter_link($input);

		return $return;
	}

	function init() {
		// FLUSH RULES after (site wide) plugin upgrade
		if (get_option('gn-sitemap-feed-mu-version') != XMLSF_VERSION) {
			update_option('gn-sitemap-feed-mu-version', XMLSF_VERSION);
			global $wp_rewrite;
			$wp_rewrite->flush_rules();
		}

		// check for qTranslate and add filter
		if (defined('QT_LANGUAGE'))
			add_filter('xml_sitemap_url', array(__CLASS__, 'qtranslate'), 99);

		// check for xLanguage and add filter
		if (defined('xLanguageTagQuery'))
			add_filter('xml_sitemap_url', array(__CLASS__, 'xlanguage'), 99);
	}

}
