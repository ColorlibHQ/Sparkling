<?php
/**
 * The WordPress core custom header feature: a header image in place of the
 * site name, and the header text colour.
 *
 * @link https://developer.wordpress.org/themes/functionality/custom-headers/
 *
 * @package sparkling
 */

/**
 * Setup the WordPress core custom header feature.
 *
 * The admin-head and admin-preview callbacks were removed in 2.7.0: they
 * styled the pre-Customizer Appearance > Header screen with empty rules and
 * printed an unescaped style attribute.
 *
 * @uses sparkling_header_style()
 */
function sparkling_custom_header_setup() {
	add_theme_support(
		'custom-header',
		apply_filters(
			'sparkling_custom_header_args',
			array(
				'default-image'      => '',
				'default-text-color' => 'dadada',
				'width'              => 300,
				'height'             => 76,
				'flex-height'        => true,
				'flex-width'         => true,
				'wp-head-callback'   => 'sparkling_header_style',
			)
		)
	);
}
add_action( 'after_setup_theme', 'sparkling_custom_header_setup' );

if ( ! function_exists( 'sparkling_header_style' ) ) :
	/**
	 * Styles the header text: hidden, or in the chosen colour.
	 *
	 * @see sparkling_custom_header_setup().
	 */
	function sparkling_header_style() {
		$header_text_color = (string) get_header_textcolor();

		if ( 'blank' === $header_text_color ) {
			$css = '.site-name, .site-description { position: absolute; clip: rect(1px, 1px, 1px, 1px); }';
		} elseif ( preg_match( '/^[0-9a-f]{3}(?:[0-9a-f]{3})?$/i', $header_text_color ) ) {
			$css = '.navbar > .container .navbar-brand { color: #' . $header_text_color . '; }';
		} else {
			// An empty or invalid value printed "color: #;" before.
			return;
		}

		echo '<style id="sparkling-header-style">' . $css . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Fixed CSS, or a colour matched against a hex pattern.
	}
endif;
