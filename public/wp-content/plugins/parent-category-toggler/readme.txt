=== Parent Category Toggler ===
Contributors: blobaugh, guar, freshmuse, Aw Guo, NikV
Tags: category, toggle, parent
Requires at least: 2.5
Tested up to: 4.8
Stable tag: trunk

Automatically toggle the parent categories when a sub category is selected.

== Installation ==

1. Unzip the zip package in your plugin folder
2. Activate it in your wp-admin menu :)

== FAQ ==

= Can I control which categories automatically toggle? =
Absolutely! version 1.2 introduced a filter called super_category_toggler. 
Pass back an array of categories you want to toggle and the rest will be excluded
by default.

== Screenshots ==

1. A simple introduction:

== Changelog =

=== 1.3.4 ==
* Compatibility updates for WordPress 4.8
=== 1.3.3 ==
* Tested with WP 4.4

=== 1.3.2 ==
* Only load plugin assets on post (CPT included) new/edit pages in wp-admin ( Props bishoya )

== 1.3.1 == 
* Verify working with WP 4.0 Alpha

== 1.3 ==
* Updated functionality to 3.7.1

= 1.2 =
* Added filter super_category_toggler to control which taxonomies will automagically toggle ( Props guar )

= 1.03 =
* Plugin maint taken over by Ben Lobaugh due to abandonment
* Tested up to version updated
* Bumped back into the repository
