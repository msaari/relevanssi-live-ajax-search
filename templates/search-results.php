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
	while ( have_posts() ) :
		the_post();
		?>
		<div class="relevanssi-live-search-result" role="option" id="" aria-selected="false">
			<p><a href="<?php echo esc_url( get_permalink() ); ?>">
				<?php the_title(); ?> &raquo;
			</a></p>
		</div>
	<?php endwhile; ?>
<?php else : ?>
	<p class="relevanssi-live-search-no-results" role="option">
		<em><?php esc_html_e( 'No results found.', 'relevanssi-live-ajax-search' ); ?></em>
	</p>
<?php endif; ?>
