<?php

/**
 * Custom Categories Widget
 * Sparkling Theme
 */
class Sparkling_Categories extends WP_Widget {

	public function __construct() {

		$widget_ops = array(
			'classname'   => 'sparkling-cats',
			'description' => esc_html__( 'Sparkling Categories', 'sparkling' ),
		);
			parent::__construct( 'sparkling-cats', esc_html__( 'Sparkling Categories', 'sparkling' ), $widget_ops );
	}

	/**
	 * Front-end display of the widget.
	 *
	 * @param array $args     Widget area arguments.
	 * @param array $instance Saved settings.
	 */
	public function widget( $args, $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'Categories', 'sparkling' );
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core hook.

		/*
		 * "Enable Posts Count" was read as on whenever the key existed, so
		 * unticking it and saving still showed the counts.
		 */
		$show_count = ! empty( $instance['enable_count'] );

		// $instance['limit'] is unset until the widget is first saved, which raised
		// an "Undefined array key" warning on PHP 8.
		$limit = ! empty( $instance['limit'] ) ? absint( $instance['limit'] ) : 4;

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Widget area markup from register_sidebar().

		if ( '' !== trim( (string) $title ) ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Widget area markup from register_sidebar().
		}

		$categories = wp_list_categories(
			array(
				'echo'       => 0,
				'show_count' => $show_count ? 1 : 0,
				'title_li'   => '',
				'depth'      => 1,
				'orderby'    => 'count',
				'order'      => 'DESC',
				'number'     => $limit,
			)
		);

		/*
		 * Wrap each post count in a <span> for styling. This used to replace
		 * every "(" and ")" in the list, which broke the markup of any
		 * category whose name contains a parenthesis.
		 */
		$categories = preg_replace( '#</a>\s*\(([^()<>]+)\)#', '</a> <span>$1</span>', $categories );
		?>

		<div class="cats-widget">
			<ul>
				<?php echo $categories; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_list_categories() output. ?>
			</ul>
		</div>

		<?php
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Widget area markup from register_sidebar().
	}

	/**
	 * Sanitize widget settings before they are saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                 = $old_instance;
		$instance['title']        = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['limit']        = isset( $new_instance['limit'] ) ? absint( $new_instance['limit'] ) : 4;
		$instance['enable_count'] = ! empty( $new_instance['enable_count'] ) ? 1 : '';

		return $instance;
	}

	public function form( $instance ) {
		if ( ! isset( $instance['title'] ) ) {
			$instance['title'] = esc_html__( 'Categories', 'sparkling' );
		}
		if ( ! isset( $instance['limit'] ) ) {
			$instance['limit'] = 4;
		}
		if ( ! isset( $instance['enable_count'] ) ) {
			$instance['enable_count'] = '';
		}

		?>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title ', 'sparkling' ); ?></label>

		<input  type="text" value="<?php echo esc_attr( $instance['title'] ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				class="widefat" />
		</p>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"> <?php esc_html_e( 'Limit Categories ', 'sparkling' ); ?></label>

		<input  type="text" value="<?php echo esc_attr( $instance['limit'] ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>"
				id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"
				class="widefat" />
		</p>

		<p><label>
		<input  type="checkbox"
				name="<?php echo esc_attr( $this->get_field_name( 'enable_count' ) ); ?>"
				id="<?php echo esc_attr( $this->get_field_id( 'enable_count' ) ); ?>"
				<?php checked( ! empty( $instance['enable_count'] ) ); ?>
		/>
			<?php esc_html_e( 'Enable Posts Count', 'sparkling' ); ?></label>
		</p>

		<?php
	}
}

?>
