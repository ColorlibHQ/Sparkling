<?php
/**
 * Block editor integration: the theme.json palette, the editor's column
 * width, and block patterns.
 *
 * Sparkling is a classic theme, but posts and pages are written in the block
 * editor, so the editor and the blocks in content should match the site.
 *
 * @package sparkling
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'sparkling_theme_json_customizer_colors' ) ) :
	/**
	 * Write the Customizer's colours into the theme.json palette.
	 *
	 * theme.json carries the defaults; a colour chosen in the Customizer
	 * replaces its palette entry, so the block editor's colour pickers, the
	 * has-*-color classes and block buttons all follow it.
	 *
	 * @param WP_Theme_JSON_Data $theme_json The theme's theme.json data.
	 *
	 * @return WP_Theme_JSON_Data
	 */
	function sparkling_theme_json_customizer_colors( $theme_json ) {
		$map = array(
			'primary'       => 'link_color',
			'primary-hover' => 'link_hover_color',
			'button'        => 'element_color',
			'button-hover'  => 'element_color_hover',
			'heading'       => 'heading_color',
		);

		$overrides = array();

		foreach ( $map as $slug => $option ) {
			// The same output-time validation the theme's inline CSS uses.
			$color = sparkling_css_color( $option );

			if ( '' !== $color ) {
				$overrides[ $slug ] = $color;
			}
		}

		$typography = of_get_option( 'main_body_typography' );

		if ( is_array( $typography ) && isset( $typography['color'] ) && is_string( $typography['color'] ) && preg_match( '/^#(?:[0-9a-f]{3}|[0-9a-f]{6})$/i', trim( $typography['color'] ) ) ) {
			$overrides['body'] = trim( $typography['color'] );
		}

		if ( empty( $overrides ) ) {
			return $theme_json;
		}

		$data    = $theme_json->get_data();
		$palette = isset( $data['settings']['color']['palette'] ) ? $data['settings']['color']['palette'] : array();

		// The resolver keys presets by origin; a bare list is the file's own shape.
		if ( isset( $palette['theme'] ) ) {
			$palette = $palette['theme'];
		}

		foreach ( $palette as $i => $entry ) {
			if ( isset( $entry['slug'], $overrides[ $entry['slug'] ] ) ) {
				$palette[ $i ]['color'] = $overrides[ $entry['slug'] ];
			}
		}

		return $theme_json->update_with(
			array(
				'version'  => 2,
				'settings' => array(
					'color' => array(
						'palette' => array_values( $palette ),
					),
				),
			)
		);
	}
endif;
add_filter( 'wp_theme_json_data_theme', 'sparkling_theme_json_customizer_colors' );

if ( ! function_exists( 'sparkling_get_post_layout' ) ) :
	/**
	 * The layout a post or page renders with: get_layout_class() for a given
	 * post rather than the current query.
	 *
	 * @param WP_Post|object $post Post, or a stand-in with post_type and ID.
	 *
	 * @return string side-pull-left, side-pull-right, no-sidebar or full-width.
	 */
	function sparkling_get_post_layout( $post ) {
		$post_id   = isset( $post->ID ) ? (int) $post->ID : 0;
		$post_type = isset( $post->post_type ) ? $post->post_type : 'post';

		if ( $post_id && in_array( get_page_template_slug( $post_id ), sparkling_full_width_templates(), true ) ) {
			return 'full-width';
		}

		$layout = $post_id ? get_post_meta( $post_id, 'site_layout', true ) : '';

		if ( 'product' === $post_type ) {
			$layout = $layout ? $layout : of_get_option( 'woo_site_layout', 'full-width' );
		}

		return $layout ? $layout : of_get_option( 'site_layout', 'side-pull-left' );
	}
endif;

if ( ! function_exists( 'sparkling_editor_layout_sizes' ) ) :
	/**
	 * Size the post editor like the column the post renders in.
	 *
	 * theme.json's layout (648px) is the content column beside a sidebar, and
	 * with No Sidebar. The Full Width layout and the full-width page templates
	 * render a 1038px column, so the editor gets that width for those posts.
	 *
	 * @param WP_Theme_JSON_Data $theme_json The theme's theme.json data.
	 *
	 * @return WP_Theme_JSON_Data
	 */
	function sparkling_editor_layout_sizes( $theme_json ) {
		global $pagenow;

		if ( ! is_admin() || ! in_array( $pagenow, array( 'post.php', 'post-new.php' ), true ) ) {
			return $theme_json;
		}

		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Read-only: which post the editor is open on.
		if ( 'post.php' === $pagenow && isset( $_GET['post'] ) ) {
			$post = get_post( absint( $_GET['post'] ) );
		} else {
			$post = (object) array(
				'ID'        => 0,
				'post_type' => isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( $_GET['post_type'] ) ) : 'post',
			);
		}
		// phpcs:enable

		if ( ! $post || 'full-width' !== sparkling_get_post_layout( $post ) ) {
			return $theme_json;
		}

		return $theme_json->update_with(
			array(
				'version'  => 2,
				'settings' => array(
					'layout' => array(
						'contentSize' => '1038px',
						'wideSize'    => '1038px',
					),
				),
			)
		);
	}
endif;
add_filter( 'wp_theme_json_data_theme', 'sparkling_editor_layout_sizes' );

if ( ! function_exists( 'sparkling_full_width_templates' ) ) :
	/**
	 * Page templates that render without a sidebar, in a full-width column.
	 *
	 * @return array Template file names.
	 */
	function sparkling_full_width_templates() {
		return array( 'page-fullwidth.php', 'template-blocks.php' );
	}
endif;

if ( ! function_exists( 'sparkling_register_block_pattern_category' ) ) :
	/**
	 * The category the theme's patterns (in /patterns) are filed under.
	 */
	function sparkling_register_block_pattern_category() {
		if ( function_exists( 'register_block_pattern_category' ) ) {
			register_block_pattern_category(
				'sparkling',
				array( 'label' => __( 'Sparkling', 'sparkling' ) )
			);
		}
	}
endif;
add_action( 'init', 'sparkling_register_block_pattern_category' );
