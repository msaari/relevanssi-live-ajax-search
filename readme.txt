=== Relevanssi Live Ajax Search ===
Contributors: msaari
Tags: search, live, ajax
Requires at least: 4.9
Tested up to: 5.9
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Template powered live search for any WordPress theme. Compatible with Relevanssi search!

== Description ==

Relevanssi Live Ajax Search enables ajax live search for your search forms. It won't swamp you with settings, and generally, Relevanssi Live Ajax Search works without any modifications necessary. If you want to customize it, you have complete control over how it works and what it does.

Relevanssi Live Ajax Search displays the search results using templates. You can easily override the default templates from your theme to make the results look the way you want them to look.

= Works best with Relevanssi =

Relevanssi Live Ajax Search only provides you with live search results. To get really good results, use [Relevanssi](https://wordpress.org/plugins/relevanssi/), [Relevanssi Premium](https://www.relevanssi.com/buy-premium/) or [Relevanssi Light](https://wordpress.org/plugins/relevanssi-light/). Relevanssi Live Ajax Search automatically uses Relevanssi to power the search results if Relevanssi is installed and active. However, you don't need Relevanssi; Relevanssi Live Ajax Search also works with the default WP search.

= Changes from SearchWP Live Ajax Search =

Relevanssi Live Ajax Search is a fork of [SearchWP Live Ajax Search](https://wordpress.org/plugins/searchwp-live-ajax-search/). I forked it from version 1.6.1 because it looked like SearchWP Live Ajax Search wasn't getting updates anymore. I also wanted to improve the Relevanssi compatibility.

Relevanssi Live Ajax Search drops all SearchWP compatibility. You can use SearchWP Live Ajax Search, which has built-in SearchWP support.

The widget has also been removed. It was unnecessary and outdated.

Relevanssi Live Ajax Search can now take over the Gutenberg `core/search` search form.

= This plugin is on GitHub =

Feel free to open up issues at
[https://github.com/msaari/relevanssi-live-ajax-search](https://github.com/msaari/relevanssi-live-ajax-search).

== Installation ==

1. Install the plugin from the WordPress plugin screen.
1. Activate the plugin.
1. That's it! Most of the time, that's all you need.

If Relevanssi Live Ajax Search does not automatically attach itself to your search form, you can enable it by adding a single HTML5 data attribute (<code>data-rlvlive="true"</code>) to the input field of your search form.

== Frequently Asked Questions ==

= How do I create a custom search results template =

Relevanssi Live Ajax Search uses a template loader, making it easy to replace the search results. There is a `templates` folder in the plugin folder, which includes `search-results.php`. This file is the template used to output search results. To customize the search results:

1. Create a folder called `relevanssi-live-ajax-search` in your theme directory.
1. Copy the `search-results.php` file into the new folder.
1. Relevanssi Live Ajax Search will now use that file to show the results.

If you want to override the default CSS styles, you can remove them this way:

`add_filter( 'relevanssi_live_search_base_styles', '__return_false' );`

This filter hook removes the base styles that control the live search result position.

`wp_dequeue_style( 'relevanssi-live-search' );`

This removes the actual search result styles.

== Changelog ==

= 1.0.0 =
* First version based on the version 1.6.1 of SearchWP Live Ajax Search.

== Upgrade Notice ==

= 1.0.0 =
* First release, based on SearchWP Live Ajax Search 1.6.1.