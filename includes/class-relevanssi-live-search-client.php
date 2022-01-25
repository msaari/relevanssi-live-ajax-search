<?php
/**
 * The Relevanssi_Live_Search_Client class.
 *
 * @package Relevanssi Live Ajax Search
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( __FILE__ ) . '/class-relevanssi-live-search-template.php';

/**
 * Class Relevanssi_Live_Search_Client
 *
 * The Relevanssi Live Ajax Search client that performs searches
 *
 * @since 1.0
 */
class Relevanssi_Live_Search_Client extends Relevanssi_Live_Search {

	/**
	 * Equivalent of __construct() - implement our hooks
	 *
	 * @since 1.0
	 *
	 * @uses add_action() to utilize WordPress Ajax functionality
	 */
	public function setup() {
		add_action( 'wp_ajax_relevanssi_live_search', array( $this, 'search' ) );
		add_action( 'wp_ajax_nopriv_relevanssi_live_search', array( $this, 'search' ) );

		add_filter( 'option_active_plugins', array( $this, 'control_active_plugins' ) );
		add_filter( 'site_option_active_sitewide_plugins', array( $this, 'control_active_plugins' ) );
	}

	/**
	 * Potential (opt-in) performance tweak: skip any plugin that's not
	 * related to the search.
	 *
	 * @param array $plugins The active plugins.
	 *
	 * @return array Filtered plugins.
	 */
	public function control_active_plugins( array $plugins ) : array {
		/**
		 * If true, skip any plugin that's not related to the search.
		 *
		 * @param bool Whether to skip the unrelated plugins.
		 */
		$applicable = apply_filters( 'relevanssi_live_search_control_plugins_during_search', false );

		if ( ! $applicable || ! is_array( $plugins ) || empty( $plugins ) ) {
			return $plugins;
		}

		if ( ! isset( $_REQUEST['rlvquery'] ) || empty( $_REQUEST['rlvquery'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return $plugins;
		}

		// The default plugin whitelist is anything Relevanssi-related.
		$plugin_whitelist = array();
		foreach ( $plugins as $plugin_slug ) {
			if ( 0 === strpos( $plugin_slug, 'relevanssi' ) ) {
				$plugin_whitelist[] = $plugin_slug;
			}
		}

		/**
		 * Filters the plugin whitelist.
		 *
		 * If the plugin filtering is used, this filter hook can be used to
		 * adjust which plugins are allowed to be used during the search.
		 *
		 * @param array $plugin_whitelist The plugin whitelist.
		 */
		$active_plugins = array_values( (array) apply_filters( 'relevanssi_live_search_plugin_whitelist', $plugin_whitelist ) );

		return $active_plugins;
	}

	/**
	 * Perform a search
	 *
	 * @since 1.0
	 *
	 * @uses sanitize_text_field() to sanitize input
	 * @uses relevanssi_Live_Search_Client::get_posts_per_page() to retrieve the number of results to return
	 */
	public function search() {
		if ( ! isset( $_REQUEST['rlvquery'] ) || empty( $_REQUEST['rlvquery'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			wp_die();
		}

		$query = sanitize_text_field( stripslashes( $_REQUEST['rlvquery'] ) ); // phpcs:ignore WordPress.Security.NonceVerification

		$args      = $_POST; // phpcs:ignore WordPress.Security.NonceVerification
		$args['s'] = $query;

		$args['posts_per_page'] = isset( $_REQUEST['posts_per_page'] ) // phpcs:ignore WordPress.Security.NonceVerification
			? intval( $_REQUEST['posts_per_page'] ) // phpcs:ignore WordPress.Security.NonceVerification
			: $this->get_posts_per_page();

		/**
		 * Filters the search arguments.
		 *
		 * The arguments are later passed to query_posts(), so whatever works
		 * there is fine here.
		 *
		 * @param array $args The search arguments.
		 */
		$args = apply_filters( 'relevanssi_live_search_query_args', $args );

		$this->show_results( $args );

		wp_die();
	}

	/**
	 * Fire the results query and trigger the template loader
	 *
	 * @since 1.0
	 *
	 * @param array $args WP_Query arguments array.
	 *
	 * @uses query_posts() to prep the WordPress environment in it's entirety
	 * for the template loader
	 * @uses sanitize_text_field() to sanitize input
	 * @uses Relevanssi_Live_Search_Template
	 * @uses Relevanssi_Live_Search_Template::get_template_part() to load the
	 * proper results template
	 */
	public function show_results( $args = array() ) {
		global $wp_query;

		$args['relevanssi'] = true;

		// We're using query_posts() here because we want to prep the entire
		// environment for our template loader, allowing the developer to
		// utilize everything they normally would in a theme template (and
		// reducing support requests).
		query_posts( $args ); // phpcs:ignore WordPress.WP.DiscouragedFunctions

		do_action( 'relevanssi_live_search_alter_results', $args );

		// Output the results using the results template.
		$results = new Relevanssi_Live_Search_Template();

		global $rlv_live_search_get_posts_per_page;
		$rlv_live_search_get_posts_per_page = $this->get_posts_per_page();

		$results->get_template_part( 'search-results' );
	}

	/**
	 * Retrieve the number of items to display
	 *
	 * @since 1.0
	 *
	 * @uses apply_filters to ensure the posts per page can be filterable via
	 * relevanssi_live_search_posts_per_page.
	 * @uses absint()
	 *
	 * @return int $per_page the number of items to display.
	 */
	public function get_posts_per_page() : int {
		// The default is 7 posts, but that can be filtered.
		$per_page = absint( apply_filters( 'relevanssi_live_search_posts_per_page', 7 ) );

		return $per_page;
	}

}
