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
	add_meta_box(
		'siderbar-layout', //Unique ID
		__( 'Select layout for this specific Page only ( Note: This setting only reflects if page Template is set as Default Template and Blog Type Templates.)', 'sparkling' ), //Title
		'sparkling_sidebar_layout', //Callback function
		'page' //show metabox in pages
	);
	add_meta_box(
		'siderbar-layout', //Unique ID
		__( 'Select layout for this specific Post only', 'sparkling' ), //Title
		'sparkling_sidebar_layout', //Callback function
		'post', //show metabox in posts
		'side'
	);
	if ( class_exists( 'WooCommerce' ) ) {
		add_meta_box(
			'product-siderbar-layout', //Unique ID
			__( 'Select layout for this specific Product only', 'sparkling' ), //Title
			'sparkling_sidebar_layout', //Callback function
			'product', //show metabox in posts
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

	<table id="sidebar-metabox" class="form-table" width="100%">
		<tbody>
			<tr>
				<label class="description">
				<?php
					$layout = get_post_meta( $post_id, 'site_layout', true );
					?>
					<select name="site_layout" id="site_layout">
						<option value=""><?php esc_html_e( 'Default', 'sparkling' ); ?></option>
						<?php
						foreach ( (array) $site_layout as $key => $val ) {
						?>
						<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $layout, $key ); ?> ><?php echo esc_html( $val ); ?></option>
													<?php
						}
						?>
					</select>
				</label>
			</tr>
		</tbody>
	</table>
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
