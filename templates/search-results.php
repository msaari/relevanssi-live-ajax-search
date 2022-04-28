<?php
/**
 * Search results are contained within a div.relevanssi-live-search-results
 * which you can style accordingly as you would any other element on your site.
 *
 * Some base styles are output in wp_footer that do nothing but position the
 * results container and apply a default transition, you can disable that by
 * adding the following to your theme's functions.php:
 *
 * add_filter( 'relevanssi_live_search_base_styles', '__return_false' );
 *
 * There is a separate stylesheet that is also enqueued that applies the default
 * results theme (the visual styles) but you can disable that too by adding
 * the following to your theme's functions.php:
 *
 * wp_dequeue_style( 'relevanssi-live-search' );
 *
 * You can use ~/relevanssi-live-search/assets/styles/style.css as a guide to customize
 *
 * @package Relevanssi Live Ajax Search
 */

?>

<?php if ( have_posts() ) : ?>
	<?php
	$status_element = '<div class="relevanssi-live-search-result-status" role="status" aria-live="polite"><p>';
	// Translators: %s is the number of results found.
	$status_element .= sprintf( esc_html( _n( '%d result found.', '%d results found.', $wp_query->found_posts, 'relevanssi-live-ajax-search' ) ), intval( $wp_query->found_posts ) );
	if ( $wp_query->found_posts > 7 ) {
		$status_element .= ' ' . sprintf( esc_html( __( 'Press enter to see all the results.', 'relevanssi-live-ajax-search' ) ) );
	}
	$status_element .= '</p></div>';

	/**
	 * Filters the status element location.
	 *
	 * @param string The location. Possible values are 'before' and 'after'. If
	 * the value is 'before', the status element will be added before the
	 * results container. If the value is 'after', the status element will be
	 * added after the results container. Default is 'before'. Any other value
	 * will make the status element disappear.
	 */
	$status_location = apply_filters( 'relevanssi_live_search_status_location', 'before' );

	if ( ! in_array( $status_location, array( 'before', 'after' ), true ) ) {
		// No status element is displayed. Still add one for screen readers.
		$status_location = 'before';
		$status_element  = '<p class="screen-reader-text" role="status" aria-live="polite">';
		// Translators: %s is the number of results found.
		$status_element .= sprintf( esc_html( _n( '%d result found.', '%d results found.', $wp_query->found_posts, 'relevanssi-live-ajax-search' ) ), intval( $wp_query->found_posts ) );
		$status_element .= '</p>';
	}

	if ( 'before' === $status_location ) {
		// Already escaped.
		echo $status_element; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	while ( have_posts() ) :
		the_post();
		?>
		<div class="relevanssi-live-search-result" role="option" id="" aria-selected="false">
			<p><a href="<?php echo esc_url( get_permalink() ); ?>">
				<?php the_title(); ?> &raquo;
			</a></p>
		</div>
		<?php
	endwhile;

	if ( 'after' === $status_location ) {
		// Already escaped.
		echo $status_element; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	?>
	<?php else : ?>
	<p class="relevanssi-live-search-no-results" role="status">
		<?php esc_html_e( 'No results found.', 'relevanssi-live-ajax-search' ); ?>
	</p>
		<?php
		if ( function_exists( 'relevanssi_didyoumean' ) ) {
			relevanssi_didyoumean(
				$wp_query->query_vars['s'],
				'<p class="relevanssi-live-search-didyoumean" role="status">'
					. __( 'Did you mean', 'relevanssi-live-ajax-search' ) . ': ',
				'</p>'
			);
		}
		?>
<?php endif; ?>
