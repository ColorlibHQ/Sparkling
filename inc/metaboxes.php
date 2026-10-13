<?php
/**
 * Sparkling Meta Boxes
 *
 */

add_action( 'add_meta_boxes', 'sparkling_add_custom_box' );
/**
 * Add Meta Boxes.
 *
 * Add Meta box in page and post post types.
 */
function sparkling_add_custom_box() {
	foreach ( array( 'page', 'post', 'product' ) as $sparkling_post_type ) {
		if ( 'product' === $sparkling_post_type && ! class_exists( 'WooCommerce' ) ) {
			continue;
		}

		// The ids are unchanged so the boxes keep their saved position and state.
		add_meta_box(
			'product' === $sparkling_post_type ? 'product-siderbar-layout' : 'siderbar-layout',
			__( 'Layout', 'sparkling' ),
			'sparkling_sidebar_layout',
			$sparkling_post_type,
			'side'
		);
	}
}

/****************************************************************************************/

global $site_layout;

/****************************************************************************************/

/**
 * Displays metabox to for sidebar layout
 */
function sparkling_sidebar_layout( $post ) {
	global $site_layout;

	$post_id = ( $post instanceof WP_Post ) ? $post->ID : get_the_ID();

	if ( ! $post_id ) {
		return;
	}

	// Use nonce for verification
	wp_nonce_field( basename( __FILE__ ), 'custom_meta_box_nonce' ); ?>

	<p>
		<label for="site_layout"><?php esc_html_e( 'Sidebar layout', 'sparkling' ); ?></label>
		<?php $layout = get_post_meta( $post_id, 'site_layout', true ); ?>
		<select name="site_layout" id="site_layout" class="widefat">
			<option value=""><?php esc_html_e( 'Default', 'sparkling' ); ?></option>
			<?php foreach ( (array) $site_layout as $key => $val ) : ?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $layout, $key ); ?>><?php echo esc_html( $val ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<?php if ( 'page' === get_post_type( $post_id ) ) : ?>
		<p class="description"><?php esc_html_e( 'Pages using the Full-width (no sidebar) template ignore this.', 'sparkling' ); ?></p>
	<?php endif; ?>
	<?php
}

/****************************************************************************************/


add_action( 'save_post', 'sparkling_save_custom_meta' );
/**
 * save the custom metabox data
 * @hooked to save_post hook
 */
function sparkling_save_custom_meta( $post_id ) {
	global $site_layout;

	// Verify the nonce before proceeding.
	if ( ! isset( $_POST['custom_meta_box_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['custom_meta_box_nonce'] ) ), basename( __FILE__ ) ) ) {
		return;
	}

	// Stop WP from clearing custom fields on autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	$post_type = isset( $_POST['post_type'] ) ? sanitize_key( wp_unslash( $_POST['post_type'] ) ) : '';

	if ( 'page' === $post_type ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}
	} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	$layout = isset( $_POST['site_layout'] ) ? sanitize_key( wp_unslash( $_POST['site_layout'] ) ) : '';

	// Only ever store a layout the theme actually offers.
	if ( $layout && is_array( $site_layout ) && array_key_exists( $layout, $site_layout ) ) {
		update_post_meta( $post_id, 'site_layout', $layout );
	} else {
		delete_post_meta( $post_id, 'site_layout' );
	}
}
