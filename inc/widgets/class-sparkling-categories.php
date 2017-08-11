<?php

/**
 * Custom Categories Widget
 * Sparkling Theme
 */
class Sparkling_Categories extends WP_Widget {

	function __construct() {

		$widget_ops = array(
			'classname' => 'sparkling-cats',
			'description' => esc_html__( 'Sparkling Categories' ,'sparkling' ),
		);
		  parent::__construct( 'sparkling-cats', esc_html__( 'Sparkling Categories','sparkling' ), $widget_ops );
	}

	function widget( $args, $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'Categories' , 'sparkling' );
		$enable_count = '';
		if ( isset( $instance['enable_count'] ) ) {
			$enable_count = $instance['enable_count'] ? $instance['enable_count'] : 'checked';
		}

		$limit = ($instance['limit']) ? $instance['limit'] : 4;

		echo $args['before_widget'];
		echo $args['before_title'];
		echo $title;
		echo $args['after_title'];

		/**
		 * Widget Content
		 */

		?>


	<div class="cats-widget">

		<ul><?php
		if ( '' != $enable_count ) {
			  $categories_args = array(
				  'echo' => 0,
				  'show_count' => 1,
				  'title_li' => '',
				  'depth' => 1,
				  'orderby' => 'count',
				  'order' => 'DESC',
				  'number' => $limit,
			  );
		} else {
			$categories_args = array(
				'echo' => 0,
				'show_count' => 0,
				'title_li' => '',
				'depth' => 1,
				'orderby' => 'count',
				'order' => 'DESC',
				'number' => $limit,
			);
		}
		$variable = wp_list_categories( $categories_args );
		$variable = str_replace( '(' , '<span>', $variable );
		$variable = str_replace( ')' , '</span>', $variable );
		echo $variable; ?></ul>

	</div><!-- end widget content -->

		<?php

		echo $args['after_widget'];
	}


	function form( $instance ) {
		if ( ! isset( $instance['title'] ) ) {
			$instance['title'] = esc_html__( 'Categories' , 'sparkling' );
		}
		if ( ! isset( $instance['limit'] ) ) {
			$instance['limit'] = 4;
		}
		if ( ! isset( $instance['enable_count'] ) ) {
			$instance['enable_count'] = '';
		}

		?>

	  <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title ','sparkling' ) ?></label>

		<input  type="text" value="<?php echo esc_attr( $instance['title'] ); ?>"
				name="<?php echo $this->get_field_name( 'title' ); ?>"
				id="<?php $this->get_field_id( 'title' ); ?>"
				class="widefat" />
	  </p>

	  <p><label for="<?php echo $this->get_field_id( 'limit' ); ?>"> <?php esc_html_e( 'Limit Categories ','sparkling' ) ?></label>

		<input  type="text" value="<?php echo esc_attr( $instance['limit'] ); ?>"
				name="<?php echo $this->get_field_name( 'limit' ); ?>"
				id="<?php $this->get_field_id( 'limit' ); ?>"
				class="widefat" />
	  </p>

	  <p><label>
		<input  type="checkbox"
				name="<?php echo $this->get_field_name( 'enable_count' ); ?>"
				id="<?php $this->get_field_id( 'enable_count' ); ?>" <?php if ( '' != $instance['enable_count'] ) { echo 'checked=checked ';} ?>
		 />
			<?php esc_html_e( 'Enable Posts Count','sparkling' ) ?></label>
	   </p>

		<?php
	}
}

?>
