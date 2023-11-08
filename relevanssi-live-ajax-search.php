<?php
/**
 * Plugin Name: Relevanssi Live Ajax Search
 * Plugin URI: https://www.relevanssi.com/live-ajax-search/
 * Description: Enhance your search forms with live search.
 * Version: 2.4
 * Requires PHP: 7.0
 * Author: Mikko Saari
 * Author URI: https://www.mikkosaari.fi/
 * Text Domain: relevanssi-live-ajax-search
 *
 * @package Relevanssi Live Ajax Search
 * @author  Mikko Saari
 * @license https://wordpress.org/about/gpl/ GNU General Public License
 * @see     https://www.relevanssi.com/live-ajax-search/
 */

/*
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, see <http://www.gnu.org/licenses/>.

	This plugin has been forked from the version 1.6.1 of the original
	SearchWP Live Ajax Search plugin by SearchWP, LLC. Copyright for the
	original code is 2014-2020 SearchWP, LLC.

	Copyright 2023 Mikko Saari (email: mikko@mikkosaari.fi)
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( __FILE__ ) . '/includes/class-relevanssi-live-search.php';

/**
 * Handles the search request.
 *
 * @param boolean $execute_search If true, run the search.
 */
function relevanssi_live_search_request_handler( $execute_search = false ) {
	include_once dirname( __FILE__ ) . '/includes/class-relevanssi-live-search-client.php';

	$client = new Relevanssi_Live_Search_Client();
	$client->setup();

	if ( $execute_search ) {
		$client->search();
	}
}

/**
 * Bootloader
 *
 * Runs on 'init' hook
 *
 * @since 1.0
 */
function relevanssi_live_search_init() {
	load_plugin_textdomain( 'relevanssi-live-ajax-search', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	// If an AJAX request is taking place, it's potentially a search so we'll
	// want to prepare for that else we'll prep the environment for the search
	// form itself.
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		relevanssi_live_search_request_handler();
	} else {
		include_once dirname( __FILE__ ) . '/includes/class-relevanssi-live-search-form.php';
		$form = new Relevanssi_Live_Search_Form();
		$form->setup();
	}
}

add_action( 'init', 'relevanssi_live_search_init' );
