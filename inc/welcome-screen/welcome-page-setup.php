<?php

add_action( 'customize_register', 'sparkling_welcome_customize_register' );

/**
 * Register the theme's own Customizer sections.
 *
 * These used to be Epsilon_Section_Recommended_Actions and Epsilon_Section_Pro.
 * Both classes went with the Epsilon framework in 2.6.0 while this file kept
 * constructing them, so from that release opening the Customizer raised
 * "Class Epsilon_Section_Recommended_Actions not found" and the screen died.
 *
 * What they showed -- the recommended actions, the recommended plugins and a
 * link to the documentation -- all still exists on Appearance > About Sparkling,
 * which is where these now point. The plugin installer is not rebuilt inside the
 * Customizer: it belongs on the About page, and WordPress.org asks themes to
 * keep that kind of thing out of the Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 *
 * @return void
 */
function sparkling_welcome_customize_register( $wp_customize ) {

	require_once get_template_directory() . '/inc/class-sparkling-customize-link-section.php';

	if ( ! class_exists( 'Sparkling_Customize_Link_Section' ) ) {
		return;
	}

	/* Without this the section's template is never printed and it does not appear. */
	$wp_customize->register_section_type( 'Sparkling_Customize_Link_Section' );

	$wp_customize->add_section(
		new Sparkling_Customize_Link_Section(
			$wp_customize,
			'sparkling_recommended_actions',
			array(
				'title'    => esc_html__( 'Recommended Plugins', 'sparkling' ),
				'url'      => admin_url( 'themes.php?page=sparkling-welcome&tab=recommended_plugins' ),
				'priority' => 0,
			)
		)
	);

	$wp_customize->add_section(
		new Sparkling_Customize_Link_Section(
			$wp_customize,
			'sparkling_documentation',
			array(
				'title'    => esc_html__( 'Sparkling Documentation', 'sparkling' ),
				'url'      => 'https://colorlib.com/wp/support/sparkling/',
				'priority' => 0,
			)
		)
	);
}

/*
 * sparkling_welcome_scripts_for_customizer() was removed in 2.7.0. It loaded
 * core's plugin installer scripts into the Customizer for an installer that
 * no longer lives there, and a stylesheet whose one live rule hid the
 * Customizer's "Change" theme button. The link sections' script and styles
 * are in assets/js/customizer-controls.js and assets/css/customizer.css.
 */

// Welcome screen
if ( is_admin() ) {
	/*
	 * The plugins the About page recommends. Only the slugs are used now; the
	 * 'recommended' flag fed the removed "recommended actions" tab.
	 */
	global $sparkling_recommended_plugins, $sparkling_welcome;
	$sparkling_recommended_plugins = array(
		'kali-forms'                       => array(
			'recommended' => true,
		),
		'colorlib-login-customizer'        => array(
			'recommended' => true,
		),
		'fancybox-for-wordpress'           => array(
			'recommended' => false,
		),
		'simple-custom-post-order'         => array(
			'recommended' => true,
		),
		'colorlib-404-customizer'          => array(
			'recommended' => true,
		),
		'colorlib-coming-soon-maintenance' => array(
			'recommended' => true,
		),
	);

	require get_template_directory() . '/inc/welcome-screen/class-sparkling-welcome.php';

	/*
	 * Registers Appearance > About Sparkling. The tab templates use this one
	 * instance; they used to construct their own, which hooked every handler a
	 * second time.
	 */
	$sparkling_welcome = new Sparkling_Welcome();
}
