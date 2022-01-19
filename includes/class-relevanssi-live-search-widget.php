<?php
/**
 * Relevanssi_Live_Search_Widget.
 *
 * @package relevanssi-live-ajax-search
 */

/**
 * Class Relevanssi_Live_Search_Widget
 *
 * The Relevanssi Live Ajax Search Widget
 *
 * @since 1.0
 */
class Relevanssi_Live_Search_Widget extends WP_Widget {
	/**
	 * Register the Widget with WordPress
	 */
	public function __construct() {
		parent::__construct(
			'relevanssi_live_search',
			__( 'Relevanssi Live Search', 'relevanssi-live-ajax-search' ),
			array( 'description' => __( 'Relevanssi Live Search', 'relevanssi-live-ajax-search' ) )
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );

		$destination = empty( $instance['destination'] ) ? '' : $instance['destination'];
		$placeholder = empty( $instance['placeholder'] ) ? __( 'Search for...', 'relevanssi-live-ajax-search' ) : $instance['placeholder'];
		$config      = empty( $instance['config'] ) ? 'default' : $instance['config'];

		echo wp_kses_post( $args['before_widget'] );
		do_action( 'relevanssi_live_search_before_widget' );

		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] . $title . $args['after_title'] );
		}

		do_action(
			'relevanssi_live_search_widget_title',
			array(
				'before_title' => $args['before_title'],
				'title'        => $title,
				'after_title'  => $args['after_title'],
			)
		);

		?>
			<?php do_action( 'relevanssi_live_search_widget_before_form' ); ?>
			<form role="search" method="get" class="relevanssi-live-search-widget-search-form" action="<?php echo esc_url( $destination ); ?>">
				<?php do_action( 'relevanssi_live_search_widget_before_field' ); ?>
				<label>
					<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'relevanssi-live-ajax-search' ); ?></span>
					<input type="search" class="search-field" placeholder="<?php echo esc_attr( $placeholder ); ?>" value="" name="rlvquery" data-rlvlive="true" data-rlvconfig="<?php echo esc_attr( $config ); ?>" title="<?php echo esc_attr( $placeholder ); ?>" autocomplete="off">
				</label>
				<?php do_action( 'relevanssi_live_search_widget_after_field' ); ?>
				<input type="submit" class="search-submit" value="<?php esc_html_e( 'Search', 'relevanssi-live-ajax-search' ); ?>">
				<?php do_action( 'relevanssi_live_search_widget_after_submit' ); ?>
			</form>
			<?php do_action( 'relevanssi_live_search_widget_after_form' ); ?>
		<?php

		echo wp_kses_post( $args['after_widget'] );
		do_action( 'relevanssi_live_search_after_widget' );
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 *
	 * @return string|void
	 */
	public function form( $instance ) {

		$widget_title       = isset( $instance['title'] ) ? $instance['title'] : __( 'Search', 'relevanssi-live-ajax-search' );
		$widget_placeholder = isset( $instance['placeholder'] ) ? $instance['placeholder'] : __( 'Search for...', 'relevanssi-live-ajax-search' );
		$widget_destination = isset( $instance['destination'] ) ? $instance['destination'] : '';

		// We're going to utilize Relevanssi_Live_Search_Form to populate the
		// config dropdown.
		$widget_config = isset( $instance['config'] ) ? $instance['config'] : 'default';
		if ( ! class_exists( 'Relevanssi_Live_Search_Form' ) ) {
			include_once dirname( __FILE__ ) . '/class-relevanssi-live-search-form.php';
		}
		$form = new Relevanssi_Live_Search_Form();
		$form->setup();
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'relevanssi-live-ajax-search' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $widget_title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'config' ) ); ?>"><?php esc_html_e( 'Configuration:', 'relevanssi-live-ajax-search' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'config' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'config' ) ); ?>">
				<?php foreach ( $form->configs as $config => $val ) : ?>
					<option value="<?php echo esc_attr( $config ); ?>" <?php selected( $widget_config, $config ); ?>><?php echo esc_html( $config ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php $rlvuniqid = uniqid( 'rlv' ); ?>
		<p><a href="#" class="button relevanssi-widget-<?php echo esc_attr( $rlvuniqid ); ?>"><?php esc_html_e( 'Advanced', 'relevanssi-live-ajax-search' ); ?></a></p>
		<div class="relevanssi-live-search-widget-advanced" style="display:none;">
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'placeholder' ) ); ?>"><?php esc_html_e( 'Placeholder:', 'relevanssi-live-ajax-search' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'placeholder' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'placeholder' ) ); ?>" type="placeholder" value="<?php echo esc_attr( $widget_placeholder ); ?>">
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'destination' ) ); ?>"><?php esc_html_e( 'Destination fallback URL (optional):', 'relevanssi-live-ajax-search' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'destination' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'destination' ) ); ?>" type="text" value="<?php echo esc_attr( $widget_destination ); ?>">
			</p>
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function($){
				$('.relevanssi-widget-<?php echo esc_attr( $rlvuniqid ); ?>').click(function(){
					var $advanced = $(this).parents().find('.relevanssi-live-search-widget-advanced');
					if($advanced.is(':visible')){
						$advanced.hide();
					}else{
						$advanced.show();
					}
					return false;
				});
			});
		</script>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title']       = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['destination'] = ( ! empty( $new_instance['destination'] ) ) ? wp_strip_all_tags( $new_instance['destination'] ) : '';
		$instance['placeholder'] = ( ! empty( $new_instance['placeholder'] ) ) ? wp_strip_all_tags( $new_instance['placeholder'] ) : '';
		$instance['config']      = ( ! empty( $new_instance['config'] ) ) ? wp_strip_all_tags( $new_instance['config'] ) : '';

		return $instance;
	}
}

/**
 * Registers the widget.
 */
function relevanssi_live_search_register_widget() {
	register_widget( 'relevanssi_Live_Search_Widget' );
}

add_action( 'widgets_init', 'relevanssi_live_search_register_widget' );
