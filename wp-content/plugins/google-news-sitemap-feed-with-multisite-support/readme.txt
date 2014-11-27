=== Google News Sitemap Feed With Multisite Support ===
Contributors: timbrd
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=UTNH8GFGUJM78
Tags: Google, Google News, Google News Sitemap, sitemap, news sitemap, sitemap.xml, sitemap-news.xml, seo, wpmu, wordpress mu, multisite, multi-site, feed
Requires at least: 2.6
Tested up to: 3.1
Stable tag: 3.3

Dynamically generates a Google News Sitemap. Multisite compatible.

== Description ==

This plugin dynamically creates a feed that comply with the **Google News Sitemap** protocol. There are no options to neither be set nor file or dir access rights to be tampered with and the sitemap become instantly available. Google News Sitemap will be reachable at example.com/sitemap-news.xml (or example.com/?feed=sitemap-news), ready for indexing by Google News.

**Compatible with Wordpress MU**

**Compatible with caching plugins** like Super Cache, W3 Total Cache and Quick Cache that cache feeds.

= Credits =

The plugin is based on XML Sitemap & Google News Sitemap Feeds plugin by RavanH (http://wordpress.org/extend/plugins/xml-sitemap-feed/).
XML Sitemap Feed was originally based on the (discontinued?) plugin Standard XML Sitemap Generator by Patrick Chia.

== Installation ==

Quick installation: 

Go to **Plugins > Add New** back-end page, search for "Google News Sitemap Feed With Multisite Support", install the plugin and activate it. There are no settings.

Manual installation:

1. Download the plugin, unzip it and upload it to /plugins/ directory.

2. Activate the plugin through the 'Plugins' menu in WordPress.

3. There are no settings.

Check Google News Sitemap by visiting example.com/sitemap-news.xml with a browser or any online XML Sitemap validator.

== Frequently Asked Questions ==

= It says that the plugin will dynamically create Google News Sitemap. Will it slow down my blog? =

Not at all. Google News will be fetching your blog sitemap just several times per day. The plugin sends titles and dates for the latest posts and it is a very quick transaction.

= Can I run this on a WPMU / WP3+ Multi-Site setup? =

Yes. In fact, it has been designed for it. Tested on WPMU 2.9.2 and WPMS 3.0.4 both with normal activation and with Network Activate / Site Wide Activate.

= Can I run this on a Wordpress single site setup? =

Yes.

= Does it support different languages? =

Yes. Please make sure to properly set Rss Language in Site options. It should be an [ISO 639 Language Code](http://www.loc.gov/standards/iso639-2/php/code_list.php) (2 letters). 
Exception: For Chinese, please use zh-cn for Simplified Chinese or zh-tw for Traditional Chinese.

= Can I run this plugin from /mu-plugins/ on WP3.0 MS or WPMU ? =

Yes. Upload the complete /xml-sitemap-feed/ directory to /wp-content/mu-plugins/ and move the file xml-sitemap.php one dir up.

= My Google News Sitemap is empty! =

A News Sitemap lists news articles which have been published on your site within the past two days.

= Do I need to change my robots.txt? =

You can tell Google News about your Sitemap by adding the following line to your robots.txt file (updating the sample URL with the complete path to your own Sitemap):

Sitemap: http://example.com/sitemap-news.xml

The best option is to submit the sitemap via Google Webmaster Tools. Please read [News Sitemaps: Submitting a News Sitemap](http://www.google.com/support/news_pub/bin/answer.py?hl=en&answer=74289).

= Do I have to use it in order to be included to Google News? =

It is not the requirement but highly recommended by Google. Please read [News Sitemaps](http://www.google.com/support/news_pub/bin/topic.py?topic=11666).

= Where can I find more information on Google News? =

[Technical Requirements](http://www.google.com/support/news_pub/bin/topic.py?hl=en&topic=11665) / 
[Submitting Your Content](http://www.google.com/support/news_pub/bin/topic.py?hl=en&topic=11662) / 
[Additional Tips](http://www.google.com/support/news_pub/bin/topic.py?hl=en&topic=11673) / 
[Troubleshooting](http://www.google.com/support/news_pub/bin/topic.py?hl=en&topic=14534) / 
[Search Google News Help articles](http://www.google.com/support/news_pub/)

= How do I get my latest articles listed on Google News? =

Go to [Suggest News Content for Google News](http://www.google.com/support/news_pub/bin/answer.py?hl=en&answer=191208&rd=1) and submit your website.

You will also want to add the sitemap to your [Google Webmasters Tools account](https://www.google.com/webmasters/tools/) to check its validity and performance.

= How can I check that my site is on Google News? =

1. Open news.google.com

2. Then type **site:example.com** and click ‘Search News’ button. (Replace example.com will your blog or website URL)

3. If results are displayed, implies blog is included in Google News index otherwise you can apply for inclusion.

= My WordPress powered blog is installed in a subdirectory. Does that change anything? =

That depends on where the index.php and .htaccess of your installation reside:

- If they are in the root while the rest of the WP files are installed in a subdir, so the site is accessible from your domain root, you do not have to do anything.

- If the index.php is together with your wp-config.php and all other WP files in a subdir, meaning your blog is only accessible via that subdir, you need to manage your own robots.txt file in your **domain root**. It _has_ to be in the root (!) and needs a line starting with `Sitemap:` followed by the full URL to the sitemap feed provided by XML Sitemap Feed plugin. Like:
`
Sitemap: http://example.com/subdir/sitemap-news.xml
` 

If you already have a robots.txt file with another Sitemap reference like it, just add the full line below or above it.

= I see no sitemap-news.xml file in my site root! =

The sitemap is dynamically generated just like a feed. There is no actual file created.

= I see a sitemap-news.xml file in site root but it does not seem to get updated! =

You are most likely looking at a sitemap-news.xml file that has been created by another XML Sitemap plugin before you started using this plugin. Just remove it and let the plugin dynamically generate it just like a feed. There is no actual file created.

If that's not the case, you are probably using a caching plugin or your browser does not update to the latest feed output. Please verify.

= I use a caching plugin but the sitemap is not cached =

Some caching plugins have the option to switch on/off caching of feeds. Make sure it is turned on. 

Frederick Townes, developer of **W3 Total Cache**, says: "There's a checkbox option on the page cache settings tab to cache feeds. They will expire according to the expires field value on the browser cache setting for HTML."

= I get an ERROR when opening the sitemap! = 

The following errors might be encountered:

**404 page instead of my sitemap-news.xml**

Try to refresh the Permalink structure in WordPress. Go to Settings > Permalinks and re-save them. Then reload the XML Sitemap in your browser with a clean browser cache. ( Try Ctrl+R to bypass the browser cache -- this works on most but not all browsers. )

= I like the plugin! It brought me tons of traffic from Google News! =

Donate via [PayPal](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=UTNH8GFGUJM78). Any amount will help to keep the code updated. Thank you!

== Screenshots ==

1. XML Sitemap feed from nytimes.com in Mozilla Firefox browser.

== Upgrade Notice ==

= 3.3 =
Includes categories in keywords. Uncategorized category is omitted.

= 3.2 =
Properly shows titles with special characters.

== Changelog ==

= 3.3 =
* Includes categories in keywords. Uncategorized category is omitted.

= 3.2 =
* Properly shows titles with special characters.
* Database request tune up for blogs with more than 1000 posts for last 2 days.

= 3.1 =
* Based on XML Sitemap & Google News Sitemap Feeds plugin v.3.9.1.
