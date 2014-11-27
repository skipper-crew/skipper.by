<?php
/*
Plugin Name: Google News Sitemap Feed With Multisite Support
Description: Dynamically generates a Google News Sitemap. No settings. Happy with it? Make a <strong><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=UTNH8GFGUJM78">donation</a></strong>. Thanks!
Version: 3.3
Author: Tim Brandon
Author URI: http://profiles.wordpress.org/users/timbrd/
*/

/*  Copyright 2010 TimBrd  (timbrd@gmail.com) */

/*  Copyright 2010 RavanH  (http://4visions.nl/ email : ravanhagen@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* --------------------
 *  AVAILABLE HOOKS
 * --------------------
 *
 * FILTERS
 *	xml_sitemap_url	->	Filters the URL used in the sitemap reference in robots.txt
 *				(receives an ARRAY and MUST return one; can be multiple urls) 
 *				and for the home URL in the sitemap (receives a STRING and MUST)
 *				return one) itself. Useful for multi language plugins or other 
 *				plugins that affect the blogs main URL... See pre-defined filter
 *				XMLSitemapFeed::qtranslate() in XMLSitemapFeed.class.php as an
 *				example.
 * ACTIONS
 *	[ none at this point, but feel free to request, suggest or code one :) ]
 *	
 */

/* --------------------
 *      CONSTANTS
 * -------------------- */
define('XMLSF_VERSION','3.2');
define('XMLSF_MEMORY_LIMIT','128M');

if (file_exists(dirname(__FILE__).'/google-news-sitemap-feed-mu'))
	define('XMLSF_PLUGIN_DIR', dirname(__FILE__).'/google-news-sitemap-feed-mu');
else
	define('XMLSF_PLUGIN_DIR', dirname(__FILE__));		

/* -----------------
 *      CLASS
 * ----------------- */

if( class_exists('XMLSitemapFeed') || include( XMLSF_PLUGIN_DIR . '/XMLSitemapFeed.class.php' ) )
	XMLSitemapFeed::go();

/* -------------------------------------
 *      MISSING WORDPRESS FUNCTIONS
 * ------------------------------------- */

include_once(XMLSF_PLUGIN_DIR . '/hacks.php');

