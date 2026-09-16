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
				'title'    => esc_html__( 'Recommended Actions & Plugins', 'sparkling' ),
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

add_action( 'customize_controls_enqueue_scripts', 'sparkling_welcome_scripts_for_customizer', 0 );

function sparkling_welcome_scripts_for_customizer() {
    wp_enqueue_style( 'sparkling-welcome-screen-customizer-css', get_template_directory_uri() . '/inc/welcome-screen/css/welcome_customizer.css' );
    wp_enqueue_style( 'plugin-install' );
    wp_enqueue_script( 'plugin-install' );
    wp_enqueue_script( 'updates' );
    wp_add_inline_script( 'plugin-install', 'var pagenow = "customizer";' );
    wp_enqueue_script( 'sparkling-welcome-screen-customizer-js', get_template_directory_uri() . '/inc/welcome-screen/js/welcome_customizer.js', array( 'customize-controls' ), '1.0', true );

    wp_localize_script(
        'sparkling-welcome-screen-customizer-js', 'sparklingWelcomeScreenObject', array(
            'ajaxurl'            => admin_url( 'admin-ajax.php' ),
            'template_directory' => get_template_directory_uri(),
        )
    );

}

// Load the system checks ( used for notifications )
require get_template_directory() . '/inc/welcome-screen/class-sparkling-notify-system.php';

// Welcome screen
if ( is_admin() ) {
    global $sparkling_required_actions, $sparkling_recommended_plugins;
    $sparkling_recommended_plugins = array(
        'kali-forms' => array(
            'recommended' => true,
        ),
        'colorlib-login-customizer' => array(
            'recommended' => true,
        ),
        'fancybox-for-wordpress'    => array(
            'recommended' => false,
        ),
        'simple-custom-post-order'  => array(
            'recommended' => true,
        ),
        'colorlib-404-customizer' => array(
            'recommended' => true,
        ),
        'colorlib-coming-soon-maintenance' => array(
            'recommended' => true,
        ),

    );
    /*
     * id - unique id; required
     * title
     * description
     * check - check for plugins (if installed)
     * plugin_slug - the plugin's slug (used for installing the plugin)
     *
     */


    $sparkling_required_actions = array();
    require get_template_directory() . '/inc/welcome-screen/class-sparkling-welcome.php';

    /*
     * Registers Appearance > About Sparkling. This line was lost between 2.6.4
     * and 2.6.9, and the class is otherwise only instantiated inside the About
     * page's own tab templates -- which can never run if the page is never
     * registered. So the page had quietly disappeared, and its URL returned
     * "Sorry, you are not allowed to access this page".
     */
    new Sparkling_Welcome();
}