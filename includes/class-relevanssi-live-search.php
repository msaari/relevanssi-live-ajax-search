<?php
/**
 * The Relevanssi_Live_Search class.
 *
 * @package Relevanssi Live Ajax Search
 * @author  Mikko Saari
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
