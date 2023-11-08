=== Relevanssi Live Ajax Search ===
Contributors: msaari
Tags: search, live, ajax
Requires at least: 4.9
Tested up to: 6.4
Stable tag: 2.4
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

If Relevanssi Live Ajax Search does not automatically attach itself to your search form, you can enable it by adding a single HTML5 data attribute (<code>data-rlvlive="true"</code>) to the input field of your search form. [You can find more information in the plugin documentation.](https://www.relevanssi.com/live-ajax-search/)

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

= I'm using Astra, Storefront, or Divi, and this plugin doesn't work =

It does, Astra search form is just designed in a way that hides the search results. You can find solutions in the [plugin documentation](https://www.relevanssi.com/live-ajax-search/#astra). The easiest solution is adding this to your theme `functions.php`:

`add_filter( 'relevanssi_live_search_add_result_div', '__return_false' );`

= I want to change the number of results shown =

There's a filter for that! Add this to your theme functions.php or in a code snippet:

`add_filter( 'relevanssi_live_search_posts_per_page', function() { return 10; } );`

This will show 10 results. You can adjust the number as you wish.

= I'm using WPML and get no results! =

For some reason, the combination of Relevanssi Live Ajax Search, Relevanssi and WPML leads to problems. To solve this problem, use the `WP_Query` mode of fetching the results. To activate the mode, add this to your theme functions.php:

`add_filter( 'relevanssi_live_search_mode', function() { return 'wp_query'; } );`

This will make Relevanssi Live Ajax Search use a different method of fetching the results. This method is compatible with WPML. This method uses the `search-results-query.php` template instead of the default `search-results.php` template in Relevanssi Live Ajax Search, so take note if you want to customize the template to use the right base template for your customization.

== Changelog ==
= 2.4 =
* New filter hook `relevanssi_live_search_template_function` lets you override the default template function used to display the results.
* Removes the unnecessary origin_id parameter.
* If Relevanssi is active, Live Ajax Search makes sure min_chars cannot be lower than the minimum word length in Relevanssi.
* When you remove letters from the search terms and go below the min_chars value, the results disappear instead of showing the spinner.

= 2.3.1 =
* Fixes the bugs in search results placement introduced in 2.3.

= 2.3 =
* The messages template is no longer loaded with AJAX. Instead, it is included in the JS localization. If you want to load it with AJAX, have the `relevanssi_live_ajax_search_ajax_messages` filter hook return `true`.
* If the parent element is set with the `rlv-parentel` attribute and located away from the input field, the results will be placed under the parent element and not under the input field.
* If the window is resized and that makes the search form hidden, the search results will also be removed.

= 2.2 =
* The search form now also responds to the `input` event, which makes the search respond to context menu paste.
* You can now override the default messages template by placing your custom template in the `relevanssi-live-ajax-search` directory in your theme.
* New filter hook `relevanssi_live_search_messages_template` can be used to filter the messages template.
* The filter hook `relevanssi_live_search_get_template_part` has an extra parameter for the context of the template: it can be `results` or `messages`, depending which template is loaded.

= 2.1 =
* Stops errors when editing a page with a Gutenberg search block in it.
* New parameter `static_offset` helps when the JS counts the result box offset wrong. Setting this to `false` may solve these problems.
* Fixes missing spinners: spinners only appeared in the first search form of the page.

= 2.0.1 =
* Renames the `has-spinner` class to `rlv-has-spinner` to avoid collisions with other plugins.
* Include an updated version of spinner SCSS in the plugin instead of getting an outdated version from npm.

= 2.0.0 =
* The `relevanssi_live_search_control_plugins_during_search` functionality has been removed.
* The search now uses a CSS spinner instead of the old Javascript spinner.
* Improved accessibility: the instructions for screen reader users are moved from the `aria-label` parameter to a separate `span` element that is hidden from sight.

= 1.2.2 =
* Fixes the `relevanssi_live_search_base_styles` filter hook, which was in reverse: now it works as expected and disables styles when you give it `false`.

= 1.2.1 =
* Adds automatic hijack for WooCommerce product search form.

= 1.2.0 =
* The way this plugin uses `query_posts()` can lead to problems (for example with WPML). It is now possible to use a new `WP_Query` instead, which is safer.
* New filter hook `relevanssi_live_search_mode` controls which mode is used: `query_posts` (the default value) is the old way, any other value uses WP_Query.
* There's a new template file `search-results-query.php`. This template is used for the `WP_Query` method.

= 1.1.0 =
* Improved accessibility: screen reader users get better notifications of what's happening.
* Improved accessibility: the default location for the search results is now the next tab stop from the search form, within div#rlvlive_1.
* Removes JQuery migration deprecation warnings.
* The search results now show the total number of results found. These changes are in the default template, so if you're using a custom template, check the default template.
* New filter hook `relevanssi_live_search_status_location` controls where the total is displayed ('before', 'after' or nowhere for any other value, in which case only the screen reader status is added).
* New filter hook `relevanssi_live_search_add_result_div` controls whether an extra div is added for the results.
* If Relevanssi is available, searches that find no results show 'Did you mean' suggestions.

= 1.0.0 =
* First version based on the version 1.6.1 of SearchWP Live Ajax Search.

== Upgrade Notice ==
= 2.4 =
* Better minimum character handling, new filter hook for template override.

= 2.3.1 =
* Fixes bugs in search results placement.

= 2.3 =
* Changed how the message template is loaded.

= 2.2 =
* You can now override the messages template.

= 2.1 =
* New `static_offset` parameter, fixed spinners.

= 2.0.1 =
* Rename the CSS spinner class name.

= 2.0.0 =
* New CSS spinner, accessibility improvements.

= 1.2.2 =
* Fixes the `relevanssi_live_search_base_styles` hook.

= 1.2.1 =
* Support for automatic hijacking of WooCommerce product search forms.

= 1.2.0 =
* New compatibility mode for better third-party compatibility.

= 1.1.0 =
* Accessibility improvements and new features.

= 1.0.0 =
* First release, based on SearchWP Live Ajax Search 1.6.1.