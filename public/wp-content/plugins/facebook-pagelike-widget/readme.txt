=== Facebook Widget ===
Contributors: Milap
Tags: facebook, facebook like box, facebook simple like, facebook fan pages, facebook like button, facebook button share, facebook social bookmarking, facebook feeds, facebook feed widget, social share, wordpress social share, socialmedia, social media widget, social media sharing, social media icon 
Donate link: https://www.paypal.com/cgi-bin/webscr?business=cemilap.88@gmail.com&cmd=_xclick¤cy_code=USD&amount=5&item_name=Offer%20me%20coffee
Requires at least: 3.0.1
Tested up to: 4.9
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin adds a Simple Facebook page Like Widget into your WordPress sidebar, footer area(as widget) & Page or Post(as shortcode).

== Description ==

One of the most popular & lightweight plugin for Facebook page feeds widget with over 6,65,000 downloads and 1,00,000+ active installs.

An inside look:

http://www.youtube.com/watch?v=eS6ZWaO7UN0

This widget will provide you the most simple and attractive way to display Facebook page likes into your WordPress sidebar. 

**Why should you choose Facebook Page Like Widget from the many other plugins?**

* Light weight & easy to configure
* Add application id from your created facebook application (Or you may use default application id), add it into widget & also URL of your Facebook page. 
* Configuration options like show/hide posts from timeline, show/hide cover, show/hide profile photos , show small header, width options, language selection, custom css.
* Shortcode support
* Fast & helpful support.

It supports short code, open your Post or Page, Add **[fb_widget]** into Post or Page, Save it. You are done. Check FAQ for more shortcode options.

If you have any questions / issues / suggestions regarding my plugin, reach me at,

* Gmail : cemilap.88@gmail.com
* Blog : https://patelmilap.wordpress.com/contact-me/
* Skype : milap_for_skype
* Facebook : https://www.facebook.com/milap112

I will try my best to reply you within 1 business day.

If you loved my plugin & support, please leave your review [Here](https://wordpress.org/support/view/plugin-reviews/facebook-pagelike-widget?filter=5) , so people can use it with confidence.


== Installation ==

Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation and then activate the Plugin from Plugins page.

For more details,

http://codex.wordpress.org/Managing_Plugins

== Frequently Asked Questions ==


= How to use shortcode ? =
* You can use below shortcode in Post or Page.
`[fb_widget fb_url="http://www.facebook.com/Instagram"]`
You can use more parameters like below.
`[fb_widget fb_url="http://www.facebook.com/Instagram" width="500" height="450" data_small_header="false" select_lng="ru_RU" data_adapt_container_width="false" data_hide_cover="false" data_show_facepile="false" data_show_posts="true"]`

= Widget doesn't working in Mozilla Firefox  = 
* If widget works great in all browsers except Mozilla Firefox, You must press the settings off "Use protection against tracking in private window", its security settings for Mozilla, nothing to do with my plugin.

= How do I sign up for a Facebook application ID for my website = 
* You may create a new Facebook application or edit your existing Facebook application through the [Facebook Developers application interface](https://developers.facebook.com/apps/). You need to signup for a Facebook Developer account first.

= I am not sure how to get Facebook application ID ? =
* If you are not able to create Facebook application or you do not know how to do that, do not worry, you can use my default application id `1590918427791514` . I have created it for plugin users and it should work like a charm for you.

= It is working in some system, not working in other system ? =
* If it is working in some system & not working with other system, there are 2 possibilties :
	1) Anti virus of your computer can stop my widget loading. Some of my widget users had same problem in past. You can check with disable your anti virus temporary. 
	2) Your browser has some ad block extention installed that may cause stop my plugin loading.  


== Screenshots ==

1. screenshot-1.png - Explains how you can configure plugin in admin widget area.
2. screenshot-2.png - Shows how your plugin will display in frontend sidebar(widget) area.
3. screenshot-3.png - Shows how your plugin will display in Page or Post as Shortcode.


== Changelog ==

= Version 4.2.2 =
* Solved Shortcode issue with attributes.
* Code Optimization.

= Version 4.2.1 =
* Updated few default values for Widget.
* Added placeholders in widget settings.
* Code optimization.

= Version 4.2 =
* Facebook has deprecated language URL, so now added static json file to read languages.

= Version 4.1 =
* Now you can add individual widget into Page or Post using shortcode.

= Version 4.0 =
* Changes in code to make plugin compitable for Translation.

= Version 3.1 =
* Removed offset warning for language dropdown.

= Version 3.0 =
* Plugin updated to Facebook Graph API v2.3.
* Removed old options like show/hide border, color scheme.
* Removed notices and warnings.
* Added unclosed div.

= Version 2.3 =
* Added options like Border, Language and custom css to shortcode function.

= Version 2.2 =

* Added support for localization (Multilanguage support)(Added .pot file)
* Added option to add custom css for widget
* Added option to select language to show your Facebook widget in any language you want.

= Version 2.1 =

* Added option to show or hide border from widget
* Added default values to all needed fields while you setup widget, it will help you.

== Upgrade Notice ==

* With the release of Graph API v2.3, the Like Box plugin is deprecated. Please use the updated Plugin instead. The Page Plugin allows you to embed a simple feed of content from a Page into your websites.
* If you do not manually upgrade to the Page Plugin, your Like Box plugin implementation will automatically fall back to the Page Plugin by June 23rd 2015.