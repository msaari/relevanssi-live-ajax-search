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

		/**
		 * Filters whether to enable the AJAX messages template.
		 *
		 * If enabled, the messages template will be loaded live via AJAX. If
		 * disabled, the messages template will be loaded via JS localize
		 * script mechanism.
		 *
		 * @param bool $enable_ajax_messages Whether to enable the AJAX messages
		 * template.
		 */
		if ( apply_filters( 'relevanssi_live_ajax_search_ajax_messages', false ) ) {
			add_action( 'wp_ajax_relevanssi_live_search_messages', array( $this, 'get_ajax_messages_template' ) );
			add_action( 'wp_ajax_nopriv_relevanssi_live_search_messages', array( $this, 'get_ajax_messages_template' ) );
		}
	}

	/**
	 * Get the messages template.
	 */
	public function get_ajax_messages_template() {
		$messages_template = new Relevanssi_Live_Search_Template();
		$template_file     = $messages_template->get_template_part( 'search-results', 'messages', false, 'messages' );

		ob_start();
		include $template_file;
		$content = ob_get_clean();
		wp_send_json( $content );
	}

	/**
	 * Get the messages template as a string.
	 */
	public function get_ajax_message_template_string() {
		$messages_template = new Relevanssi_Live_Search_Template();
		$template_file     = $messages_template->get_template_part( 'search-results', 'messages', false, 'messages' );

		ob_start();
		include $template_file;
		$content = ob_get_clean();
		return $content;
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
		$args['relevanssi'] = true;

		/**
		 * Controls the query mode.
		 *
		 * The default value is 'query_posts', using the original query_posts()
		 * method of fetching the results. Any other value will use the new
		 * and safer method of fetching the results with new WP_Query().
		 *
		 * @param string $mode The query mode, default 'query_posts'.
		 */
		$mode = apply_filters( 'relevanssi_live_search_mode', 'query_posts' );

		if ( 'query_posts' === $mode ) {
			// We're using query_posts() here because we want to prep the entire
			// environment for our template loader, allowing the developer to
			// utilize everything they normally would in a theme template (and
			// reducing support requests).
			query_posts( $args ); // phpcs:ignore WordPress.WP.DiscouragedFunctions
			$template = 'search-results';
		} else {
			global $relevanssi_query;
			$relevanssi_query = new WP_Query( $args );
			$template         = 'search-results-query';
		}

		do_action( 'relevanssi_live_search_alter_results', $args );

		// Output the results using the results template.
		$results = new Relevanssi_Live_Search_Template();

		/**
		 * Filters the template function to use for displaying the results.
		 *
		 * The default is an empty string, which will use the default template
		 * function. If a function is specified and exists, it will be used
		 * instead.
		 *
		 * @param string $template_function The template function to use for
		 * displaying the results.
		 *
		 * @return string The template function to use for displaying the
		 * results.
		 */
		$template_function = apply_filters(
			'relevanssi_live_search_template_function',
			''
		);
		if ( function_exists( $template_function ) ) {
			call_user_func( $template_function, $mode );
		} else {
			$results->get_template_part( $template );
		}
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
