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
	public $version = '2.4';

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
		$this->directory_name = dirname( dirname( __FILE__ ) );
		$this->url            = plugins_url( 'relevanssi-live-ajax-search', $this->directory_name );

		// Cleaning up SearchWP Live Ajax Search legacy.
		add_action(
			'admin_init',
			function() {
				delete_option( 'relevanssi_live_search_last_update' );
				delete_option( 'relevanssi_live_search_version' );
			}
		);
	}
}
