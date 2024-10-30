=== LiveLib Widget ===
Contributors: SergeyBiryukov
Tags: books, library, livelib, sidebar, widget
Requires at least: 2.7
Tested up to: 2.9.2
Stable tag: 1.2

Displays the latest books you have read from your LiveLib account.

== Description ==

Displays the latest books you have read in your sidebar.
The list of books is imported from your [LiveLib](http://www.livelib.ru/ "Saving knowledge") account via RSS feed.

== Installation ==

1. Upload `livelib-widget` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Open 'LiveLib Widget' item under the 'Options' menu and set up the link to your LiveLib RSS feed.
4. Use the 'Presentation' menu to add the widget to your sidebar, or place `<?php livelib_display(); ?>` in `sidebar.php` manually.

== Frequently Asked Questions ==

= What is LiveLib? =

[LiveLib](http://www.livelib.ru/ "Saving knowledge") is a social network for book readers with the largest base of independent book reviews.
With the help of this site you'll be able to find answers to the following questions:

* What is worth reading on some topic, and where can I find this book?
* What books do my friends read?
* What did I read yesterday or 5 years ago?
* Who are my associates? How to find them and what are their interests?

== Screenshots ==

1. LiveLib Widget in WordPress Default theme.

== Changelog ==

= 1.2 =
* Fixed display of book titles (as of current LiveLib feed format)
* Added new CSS classes for custom styling
* Using WordPress 2.7+ settings API

= 1.1 =
* Added standalone WordPress widget
* Using object cache functions for better performance

= 1.0 =
* Initial release
