<?php
/* ------------------------------
 *      XMLSitemapFeed CLASS
 * ------------------------------ */

class XMLSitemapFeed {

	/**
	* FEEDS
	*/

	// set up the sitemap template
	public static function load_template() {
		load_template( XMLSF_PLUGIN_DIR . '/feed-sitemap.php' );
	}

	// set up the news sitemap template
	public static function load_template_news() {
		load_template( XMLSF_PLUGIN_DIR . '/feed-sitemap-news.php' );
	}

	// override default feed limit
	public static function filter_limits( $limits ) {
		return 'LIMIT 0, 49999';
	}

	// Create a new filtering function that will add a where clause to the query,
	// used for the Google News Sitemap
	public static function filter_news_where($where = '') {
		//posts from the last 2 days (48 hours + 1 hour to be sure)
		return $where . " AND post_date > '" . date('Y-m-d', strtotime('-49 hours')) . "'";
	}
	
	/**
	* REWRITES
	*/

	// add sitemap rewrite rules
	public static function rewrite($wp_rewrite) {
		$feed_rules = array(
			XMLSF_NAME . '$' => $wp_rewrite->index . '?feed=sitemap',
			XMLSF_NEWS_NAME . '$' => $wp_rewrite->index . '?feed=sitemap-news',
		);
		$wp_rewrite->rules = $feed_rules + $wp_rewrite->rules;
	}
	
	/**
	 * Remove the trailing slash from permalinks that have an extension,
	 * such as /sitemap.xml (thanks to Permalink Editor plugin for WordPress)
	 *
	 * @param string $request
	 */

	public static function trailingslash($request) {
		if (pathinfo($request, PATHINFO_EXTENSION)) {
			return untrailingslashit($request);
		}
		return trailingslashit($request);
	}

	/**
	* ROBOTSTXT 
	*/

	// add sitemap location in robots.txt generated by WP
	// available filter : xml_sitemap_url
	public static function robots() {

		// hook for filter 'xml_sitemap_url' provides an array here and MUST get an array returned
		$blog_url = trailingslashit(get_bloginfo('url'));
		$sitemap_array = apply_filters('xml_sitemap_url',array($blog_url.XMLSF_NAME,$blog_url.XMLSF_NEWS_NAME));

		echo "\n# XML Sitemap Feed ".XMLSF_VERSION." (http://4visions.nl/en/wordpress-plugins/xml-sitemap-feed/)";

		if ( is_array($sitemap_array) && !empty($sitemap_array) )
			foreach ( $sitemap_array as $url )
				echo "\nSitemap: " . $url;
		else
			echo "\n# Warning: xml sitemap url is missing, was filtered out or filter did not return an array.";
	
		echo "\n\n";
	}
	
	/**
	* DE-ACTIVATION
	*/

	public static function deactivate() {
		remove_filter('generate_rewrite_rules', array(__CLASS__, 'rewrite') );
		delete_option('xml-sitemap-feed-version');
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}

	/**
	* REQUEST FILTER
	*/

	public static function filter_request( $request ) {
		if (isset($request['feed'])) {
			if ( $request['feed'] == 'sitemap' ) {
				add_filter( 'post_limits', array( __CLASS__, 'filter_limits' ) );
				
				$types_arr = explode(',',XMLSF_POST_TYPE);
				$request['post_type'] = (in_array('any',$types_arr)) ? 'any' : $types_arr;
				$request['orderby'] = 'modified';
			}
			if ( $request['feed'] == 'sitemap-news' ) {
				add_filter( 'post_limits', array( __CLASS__, 'filter_limits' ) );
				add_filter( 'posts_where', array( __CLASS__, 'filter_news_where' ), 10, 1  );

				$types_arr = explode(',',XMLSF_NEWS_POST_TYPE);
				$request['post_type'] = (in_array('any',$types_arr)) ? 'any' : $types_arr;
			}
		}

	    return $request;
	}

	/**
	* MULTI-LANGUAGE PLUGIN FILTERS
	*/

	// Polylang
	public static function polylang($input) {
		global $polylang;
		$options = get_option('polylang');

		if (is_array($input)) { // got an array? return one!
			if ('1' == $options['force_lang'] )
				foreach ( $input as $url )
					foreach($polylang->get_languages_list() as $language)
						$return[] = $polylang->add_language_to_link($url,$language);
			else
				foreach ( $input as $url )
					foreach($polylang->get_languages_list() as $language)
						$return[] = add_query_arg('lang', $language->slug, $url);
		} else { // not an array? do nothing, Polylang does all the work :)
			$return = $input;
		}

		return $return;
	}

	// qTranslate
	public static function qtranslate($input) {
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
	public static function xlanguage($input) {
		global $xlanguage;
	
		if (is_array($input)) // got an array? return one!
			foreach ( $input as $url )
				foreach($xlanguage->options['language'] as $language)
					$return[] = $xlanguage->filter_link_in_lang($url,$language['code']);
	 	else // not an array? just convert the string.
	       	$return = $xlanguage->filter_link($input);

		return $return;
	}

	public static function init() {

		global $wpdb, $query;
		
		if ( '0' == get_option( 'blog_public' ) || ( $wpdb->blogid && function_exists('get_site_option') && get_site_option('tags_blog_id') == $wpdb->blogid ) ) 
			return;
			// we are on a blog that blocks spiders! >> create NO sitemap
			// - OR -
			// we are on wpmu and this is a tags blog! >> create NO sitemap
			// since it will be full of links outside the blogs own domain...

		// DE-ACTIVATION
		register_deactivation_hook( XMLSF_PLUGIN_DIR . '/xml-sitemap.php', array(__CLASS__, 'deactivate') );

		// FEEDS
		add_action('do_feed_sitemap', array(__CLASS__, 'load_template'), 10, 1);
		add_action('do_feed_sitemap-news', array(__CLASS__, 'load_template_news'), 10, 1);

		// REWRITES
		add_filter('generate_rewrite_rules', array(__CLASS__, 'rewrite') );
		add_filter('user_trailingslashit', array(__CLASS__, 'trailingslash') );
		
		// FLUSH RULES after (site wide) plugin upgrade
		if (get_option('xml-sitemap-feed-version') != XMLSF_VERSION) {
			update_option('xml-sitemap-feed-version', XMLSF_VERSION);
			global $wp_rewrite;
			$wp_rewrite->flush_rules();
			// TODO fix PHP Fatal error:Call to a member function flush_rules() on a non-object in /var/www/wordpress/wp-content/plugins/xml-sitemap-feed/XMLSitemapFeed.class.php after (any?) plugin upgrade
		}
		
		// ROBOTSTXT
		add_action('do_robotstxt', array(__CLASS__, 'robots') );

		// REQUEST 
		add_filter('request', array(__CLASS__, 'filter_request'), 1 );


		// LANGUAGE PLUGINS
		// check for Polylang and add filter
		global $polylang;
		if (isset($polylang))
			add_filter('xml_sitemap_url', array(__CLASS__, 'polylang'), 99);
		// check for qTranslate and add filter
		elseif (defined('QT_LANGUAGE'))
			add_filter('xml_sitemap_url', array(__CLASS__, 'qtranslate'), 99);
		// check for xLanguage and add filter
		elseif (defined('xLanguageTagQuery'))
			add_filter('xml_sitemap_url', array(__CLASS__, 'xlanguage'), 99);
	}

}