<?php
/**
 * The Relevanssi_Live_Search_Template class.
 *
 * @package Relevanssi Live Ajax Search
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Relevanssi_Live_Search_Template
 *
 * Template loader class based on Pippin Williamson's guide
 * http://pippinsplugins.com/template-file-loaders-plugins/
 *
 * @since 1.0
 */
class Relevanssi_Live_Search_Template extends Relevanssi_Live_Search {
	/**
	 * Retrieve the template directory within this plugin
	 *
	 * @since 1.0
	 *
	 * @return string The template directory within this plugin
	 */
	public function get_template_directory() : string {
		return trailingslashit( $this->directory_name ) . 'templates';
	}

	/**
	 * Set up the proper template part array and locate it
	 *
	 * @sine 1.0
	 *
	 * @param string      $slug    The template slug (without file extension).
	 * @param string|null $name    The template name (appended to $slug if
	 * provided), default null.
	 * @param bool        $load    Whether to load the template part.
	 * @param string      $context The context in which the template is being
	 * loaded. Can be 'results' or 'messages'.
	 *
	 * @return bool|string The location of the applicable template file
	 */
	public function get_template_part( string $slug, $name = null, bool $load = true, string $context = 'results' ) {

		do_action( 'get_template_part_' . $slug, $slug, $name );

		$templates = array();

		if ( isset( $name ) ) {
			$templates[] = $slug . '-' . $name . '.php';
		}
		$templates[] = $slug . '.php';

		/**
		 * Allow filtration of template parts.
		 *
		 * @param array  $templates The template parts to be loaded.
		 * @param string $slug      The template slug (without file extension).
		 * @param string $name      The template name (appended to $slug if
		 * provided), default null.
		 * @param string $context   The context in which the template is being
		 * loaded. Can be 'results' or 'messages'.
		 *
		 * @return array The template parts to be loaded.
		 */
		$templates = apply_filters( 'relevanssi_live_search_get_template_part', $templates, $slug, $name, $context );

		return $this->locate_template( $templates, $load, false, true, $context );
	}

	/**
	 * Check for the applicable template in the child theme, then parent theme,
	 * and in the plugin dir as a last resort and output it if it was located.
	 *
	 * @since 1.0
	 *
	 * @param array  $template_names The potential template names in order of
	 * precedence.
	 * @param bool   $load           Whether to load the template file.
	 * @param bool   $require_once   Whether to require the template file once.
	 * @param string $context        The context in which the template is being
	 * loaded. Can be 'results' or 'messages'.
	 *
	 * @return bool|string The location of the applicable template file
	 */
	private function locate_template( array $template_names, bool $load = false, bool $require_once = true, string $context = 'results' ) {
		// Default to not found.
		$located = false;

		/**
		 * Allow filtering of the template directory name.
		 *
		 * @param string $template_dir The template directory name.
		 *
		 * @return string The template directory name.
		 */
		$template_dir = apply_filters( 'relevanssi_live_search_template_dir', 'relevanssi-live-ajax-search' );

		// Try to find the template file.
		foreach ( (array) $template_names as $template_name ) {
			if ( empty( $template_name ) ) {
				continue;
			}
			$template_name = ltrim( $template_name, '/' );

			// Check the child theme first.
			$maybe_child_theme = trailingslashit( get_stylesheet_directory() ) . trailingslashit( $template_dir ) . $template_name;
			if ( file_exists( $maybe_child_theme ) ) {
				$located = $maybe_child_theme;
				break;
			}

			if ( ! $located ) {
				// Check parent theme.
				$maybe_parent_theme = trailingslashit( get_template_directory() ) . trailingslashit( $template_dir ) . $template_name;
				if ( file_exists( $maybe_parent_theme ) ) {
					$located = $maybe_parent_theme;
					break;
				}
			}

			if ( ! $located ) {
				// Check theme compat.
				$maybe_theme_compat = trailingslashit( $this->get_template_directory() ) . $template_name;
				if ( file_exists( $maybe_theme_compat ) ) {
					$located = $maybe_theme_compat;
					break;
				}
			}
		}

		$filter_hook = 'messages' === $context
			? 'relevanssi_live_search_messages_template'
			: 'relevanssi_live_search_results_template';

		/**
		 * Allow filtering of the template file location.
		 *
		 * The name of the filter hook depends on the context.
		 *
		 * @param string                          $located The location of the
		 * template file.
		 * @param Relevanssi_Live_Search_Template $this    The template object.
		 *
		 * @return string The location of the template file.
		 */
		$located = apply_filters( $filter_hook, $located, $this );

		if ( ( true === $load ) && ! empty( $located ) ) {
			load_template( $located, $require_once );
		}

		return $located;
	}

}
