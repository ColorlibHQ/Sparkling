<?php

/**
 * Sparkling Top Posts Widget
 * Sparkling Theme
 */
class Sparkling_Popular_Posts extends WP_Widget {
	function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue' ) );
		$widget_ops = array(
			'classname'   => 'sparkling-popular-posts',
			'description' => esc_html__( 'Sparkling Popular Posts Widget', 'sparkling' ),
		);
			parent::__construct( 'sparkling_popular_posts', esc_html__( 'Sparkling Popular Posts Widget', 'sparkling' ), $widget_ops );
	}

	/**
	 * Enqueue the widget's media picker, but only where the widget form renders.
	 *
	 * This used to run on admin_init, which fires on every admin screen. That
	 * mattered because wp_enqueue_media() is one-shot -- core guards it with
	 * `if ( did_action( 'wp_enqueue_media' ) ) { return; }` -- so calling it
	 * here with no arguments won the race against
	 * edit-form-advanced.php's `wp_enqueue_media( array( 'post' => $post->ID ) )`.
	 * The post edit screen was then left with the default
	 * `'post' => array( 'id' => 0 )`, so wp.media had no update-post_{id} nonce
	 * and setting a featured image failed with a 403.
	 *
	 * Scoped to the widgets screen and the Customizer, where this widget's form
	 * is actually shown. See https://github.com/ColorlibHQ/Sparkling/issues/272
	 *
	 * @param string $hook Current admin page, passed by admin_enqueue_scripts.
	 */
	public function enqueue( $hook = '' ) {

		$on_widgets   = ( 'widgets.php' === $hook );
		$on_customize = ( 'customize.php' === $hook || 'customize_controls_enqueue_scripts' === current_action() );

		if ( $on_widgets || $on_customize ) {
			wp_enqueue_media();
			wp_enqueue_script( 'sparkling-popular-post-script', get_template_directory_uri() . '/assets/js/widget.js', array( 'jquery' ), SPARKLING_VERSION, true );
			$args = array(
				'ajaxurl'     => admin_url( 'admin-ajax.php' ),
				'nonce'       => wp_create_nonce( 'sparkling_widget_nonce' ),
				'frameTitle'  => __( 'Select a default image', 'sparkling' ),
				'frameButton' => __( 'Use this image', 'sparkling' ),
			);
			wp_localize_script( 'sparkling-popular-post-script', 'Sparkling', $args );
		}
	}

	/**
	 * Front-end display of the widget.
	 *
	 * @param array $args     Widget area arguments.
	 * @param array $instance Saved settings.
	 */
	public function widget( $args, $instance ) {
		$title         = isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'Popular Posts', 'sparkling' );
		$title         = apply_filters( 'widget_title', $title, $instance, $this->id_base ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core hook.
		$limit         = ! empty( $instance['limit'] ) ? absint( $instance['limit'] ) : 5;
		$default_image = ! empty( $instance['default_image'] ) ? absint( $instance['default_image'] ) : 0;

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Widget area markup from register_sidebar().

		// An emptied title used to print an empty heading.
		if ( '' !== trim( (string) $title ) ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Widget area markup from register_sidebar().
		}
		?>

		<div class="popular-posts-wrapper">
			<?php
			$featured_query = new WP_Query(
				array(
					'posts_per_page'      => $limit,
					'orderby'             => 'comment_count',
					'order'               => 'DESC',
					'ignore_sticky_posts' => 1,
					'no_found_rows'       => true,
					'post_status'         => 'publish',
				)
			);

			while ( $featured_query->have_posts() ) :
				$featured_query->the_post();

				if ( '' === get_the_content() ) {
					continue;
				}

				if ( has_post_thumbnail() ) {
					$image = get_the_post_thumbnail( get_the_ID(), 'tab-small' );
				} elseif ( $default_image ) {
					$image = wp_get_attachment_image( $default_image, 'tab-small' );
				} else {
					$image = '';
				}
				?>

				<div class="post">
					<div class="post-image <?php echo esc_attr( get_post_format() ); ?>">
						<?php
						/*
						 * Only with an image: without one this was an empty link. The
						 * title links to the same post, so the image link is kept out
						 * of the tab order and away from screen readers.
						 */
						if ( $image ) :
							?>
							<a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core image markup. ?></a>
						<?php endif; ?>
					</div>

					<div class="post-content">
						<a href="<?php the_permalink(); ?>"><?php echo esc_html( get_the_title() ); ?></a>
						<span class="date"><?php echo esc_html( get_the_date() ); ?></span>
					</div>
				</div>

				<?php
			endwhile;

			// A secondary WP_Query, so wp_reset_postdata(), not wp_reset_query().
			wp_reset_postdata();
			?>
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
		$instance['limit'] = isset( $new_instance['limit'] ) ? absint( $new_instance['limit'] ) : 5;

		// The form posts back an attachment ID; store it as one.
		$instance['default_image'] = isset( $new_instance['default_image'] ) ? absint( $new_instance['default_image'] ) : '';

		return $instance;
	}

	function form( $instance ) {

		if ( ! isset( $instance['title'] ) ) {
			$instance['title'] = esc_html__( 'Popular Posts', 'sparkling' );
		}
		if ( ! isset( $instance['limit'] ) ) {
			$instance['limit'] = 5;
		}
		/*
		 * The stored value is an attachment ID. This used to be overwritten with
		 * the attachment URL for the preview, which meant the hidden field posted a
		 * URL back and the saved setting stopped being an ID -- so widget() could no
		 * longer resolve the fallback image. Keep the ID in the field and resolve
		 * the preview URL separately.
		 */
		$default_image_id  = isset( $instance['default_image'] ) ? absint( $instance['default_image'] ) : 0;
		$default_image_url = $default_image_id ? wp_get_attachment_image_url( $default_image_id, 'medium' ) : '';

		?>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'sparkling' ); ?></label>

			<input  type="text" value="<?php echo esc_attr( $instance['title'] ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				class="widefat" />
			</p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php esc_html_e( 'Limit Posts Number', 'sparkling' ); ?></label>

			<input  type="text" value="<?php echo esc_attr( $instance['limit'] ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>"
				id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"
				class="widefat" />
			<p>
			<div class="sparkling-media-container media-widget-control">
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'default_image' ) ); ?>"><?php esc_html_e( 'Default Image', 'sparkling' ); ?></label>
					<input  type="hidden" value="<?php echo esc_attr( $default_image_id ? $default_image_id : '' ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'default_image' ) ); ?>"
					id="<?php echo esc_attr( $this->get_field_id( 'default_image' ) ); ?>"
					class="widefat" />
				</p>
				<div class="media-widget-preview">
				<div class="attachment-media-view">
					<div class="placeholder" <?php echo $default_image_url ? 'style="display:none;"' : ''; ?>><?php echo esc_html__( 'No media selected', 'sparkling' ); ?></div>
					<?php if ( $default_image_url ) : ?>
						<img src="<?php echo esc_url( $default_image_url ); ?>" alt="">
					<?php endif ?>
				</div>
			</div>
			<p class="media-widget-buttons">
				<button type="button" class="button upload-button">
					<?php echo esc_html_x( 'Add Media', 'label for button in the media widget', 'sparkling' ); ?>
				</button>
				<button type="button" class="button remove-button">
					<?php echo esc_html_x( 'Remove Media', 'label for button in the media widget; should preferably not be longer than ~13 characters long', 'sparkling' ); ?>
				</button>
			</p>
		</div>

		<?php
	}
}
?>
