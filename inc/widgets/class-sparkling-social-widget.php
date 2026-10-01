<?php

/**
 * Social  Widget
 * Sparkling Theme
 */
class Sparkling_Social_Widget extends WP_Widget {

	function __construct() {

						$widget_ops = array(
							'classname'   => 'sparkling-social',
							'description' => esc_html__( 'Sparkling Social Widget', 'sparkling' ),
						);
						parent::__construct( 'sparkling-social', esc_html__( 'Sparkling Social Widget', 'sparkling' ), $widget_ops );
	}

	/**
	 * Front-end display of the widget.
	 *
	 * @param array $args     Widget area arguments.
	 * @param array $instance Saved settings.
	 */
	public function widget( $args, $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'Follow us', 'sparkling' );
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core hook.

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Widget area markup from register_sidebar().

		if ( '' !== trim( (string) $title ) ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Widget area markup from register_sidebar().
		}
		?>

		<div class="social-icons sticky-sidebar-social">
			<?php sparkling_social_icons(); ?>
		</div>

		<?php
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Widget area markup from register_sidebar().
	}

	/**
	 * Sanitize widget settings before they are saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';

		return $instance;
	}

	function form( $instance ) {
		if ( ! isset( $instance['title'] ) ) {
			$instance['title'] = esc_html__( 'Follow us', 'sparkling' );
		}
		?>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title ', 'sparkling' ); ?></label>

		<input type="text" value="<?php echo esc_attr( $instance['title'] ); ?>"
							name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
							id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
							class="widefat" />
		</p><?php
	}
}
?>
