<?php
/**
 * Plugin Name: Relevanssi Live Ajax Search
 * Plugin URI: https://www.relevanssi.com/
 * Description: Enhance your search forms with live search, powered by SearchWP (if installed)
 * Version: 1.6.1
 * Requires PHP: 7.0
 * Author: SearchWP, LLC
 * Author URI: https://searchwp.com/
 * Text Domain: relevanssi-live-ajax-search
 *
 * @package relevanssi-live-ajax-search
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

	This plugin has been forked from the original SearchWP Live Ajax
	Search plugin by SearchWP, LLC. Copyright for the original code
	is 2014-2020 SearchWP, LLC.

	Copyright 2022 Mikko Saari  (email: mikko@mikkosaari.fi)
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Widget support.
require_once dirname( __FILE__ ) . '/includes/class-relevanssi-live-search-widget.php';

/**
 * Class Relevanssi_Live_Search
 *
 * The main Relevanssi Live Ajax Search Class properly routes searches and all
 * other requests/utilization.
 *
 * @since 1.0
 */
class Relevanssi_Live_Search {
	/**
	 * The plugin file dirname().
	 *
	 * @var string $directory_name
	 */
	public $directory_name;

	/**
	 * The plugin file plugins_url().
	 *
	 * @var string $url
	 */
	public $url;

	/**
	 * Plugin version number.
	 *
	 * @var string $version
	 */
	public $version = '1.6.1';

	/**
	 * The search results.
	 *
	 * @var array $results
	 */
	public $results = array();

	/**
	 * The class constructor.
	 */
	public function __construct() {
		$this->directory_name = dirname( __FILE__ );
		$this->url            = plugins_url( 'relevanssi-live-ajax-search', $this->directory_name );

		$this->upgrade();
	}

	/**
	 * Updates the plugin last updated option.
	 */
	private function upgrade() {
		$last_version = get_option( 'relevanssi_live_search_version' );

		if ( false === $last_version ) {
			$last_version = 0;
		}

		if ( ! version_compare( $last_version, $this->version, '<' ) ) {
			return;
		}

		if ( version_compare( $last_version, '1.6.1', '<' ) ) {
			update_option( 'relevanssi_live_search_last_update', time(), 'no' );
			$this->after_upgrade();
		}
	}

	/**
	 * Updates the plugin version number in the options.
	 */
	private function after_upgrade() {
		update_option( 'relevanssi_live_search_version', $this->version, 'no' );
	}
}

/**
 * Handles the search request.
 *
 * @param boolean $execute_search If true, run the search.
 */
function relevanssi_live_search_request_handler( $execute_search = false ) {
	include_once dirname( __FILE__ ) . '/includes/class-relevanssi-live-search-client.php';
	include_once dirname( __FILE__ ) . '/includes/class-relevanssi-bridge.php';

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

/**
 * Loads jquery if necessary.
 */
function relevanssi_live_search_admin_scripts() {
	if ( ! relevanssi_live_search_notice_applicable() ) {
		return;
	}

	wp_enqueue_script( 'jquery' );
}

add_action( 'admin_enqueue_scripts', 'relevanssi_live_search_admin_scripts' );

/**
 * If the notice is dismissed, updates the option.
 */
function relevanssi_live_search_notice_dismissed() {
	check_ajax_referer( 'relevanssi_live_search_notice_dismiss_nonce' );

	update_user_meta( get_current_user_id(), 'relevanssi_live_search_notice_dismissed', true );

	wp_send_json_success();
}

add_action( 'wp_ajax_relevanssi_live_search_notice_dismiss', 'relevanssi_live_search_notice_dismissed' );

/**
 * Checks is the notice should be displayed or not.
 *
 * @return boolean True if the notice should be displayed, false otherwise.
 */
function relevanssi_live_search_notice_applicable() : boolean {
	// If Relevanssi is installed, bail out.
	if (
		is_plugin_active( 'relevanssi/relevanssi.php' )
		|| is_plugin_active( 'relevanssi-premium/relevanssi.php' )
		) {
		return false;
	}

	// If it's been less than 3 days since the last update, bail out.
	$last_update = get_option( 'relevanssi_live_search_last_update' );
	if ( empty( $last_update ) || ( time( 'timestamp' ) < absint( $last_update ) + ( DAY_IN_SECONDS * 3 ) ) ) {
		return false;
	}

	// If notice was dismissed, bail out.
	$dismissed = get_user_meta( get_current_user_id(), 'relevanssi_live_search_notice_dismissed', true );
	if ( $dismissed ) {
		return false;
	}

	return true;
}

/**
 * Show the notice.
 */
function relevanssi_live_search_notice() {
	if ( ! relevanssi_live_search_notice_applicable() ) {
		return;
	}

	?>
	<div class="notice notice-info is-dismissible relevanssi-live-search-notice-dismiss">
		<p><strong>Relevanssi Live Ajax Search</strong><br><a href="https://www.relevanssi.com/" target="_blank">Improve your search results</a> and find out <a href="https://searchwp.com/extensions/metrics/?utm_source=wordpressorg&utm_medium=link&utm_content=notice&utm_campaign=liveajaxsearch" target="_blank">what your visitors are searching for</a> at the same time with <a href="https://searchwp.com/?utm_source=wordpressorg&utm_medium=link&utm_content=notice&utm_campaign=liveajaxsearch" target="_blank">SearchWP!</a></p>
		<script>
		(function( $ ) {
			'use strict';
			$( function() {
				$('.relevanssi-live-search-notice-dismiss').on( 'click', '.notice-dismiss', function( event, el ) {
					var $notice = $(this).parent('.notice.is-dismissible');
					$.post(ajaxurl, {
						action: 'relevanssi_live_search_notice_dismiss',
						_ajax_nonce: '<?php echo esc_js( wp_create_nonce( 'relevanssi_live_search_notice_dismiss_nonce' ) ); ?>'
					});
				});
			} );
		})( jQuery );
		</script>
	</div>
	<?php
}

add_action( 'admin_notices', 'relevanssi_live_search_notice' );
