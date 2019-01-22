<?php

add_action( 'customize_register', 'sparkling_welcome_customize_register' );

function sparkling_welcome_customize_register( $wp_customize ) {

	global $sparkling_required_actions, $sparkling_recommended_plugins;
	$theme_slug                     = 'sparkling';
	$customizer_recommended_plugins = array();
	if ( is_array( $sparkling_recommended_plugins ) ) {
		foreach ( $sparkling_recommended_plugins as $k => $s ) {
			if ( $s['recommended'] ) {
				$customizer_recommended_plugins[ $k ] = $s;
			}
		}
	}

	$wp_customize->add_section(
		new Epsilon_Section_Recommended_Actions(
			$wp_customize,
			'epsilon_recomended_section',
			array(
				'title'                        => esc_html__( 'Recommended Actions', 'sparkling' ),
				'social_text'                  => esc_html__( 'We are social :', 'sparkling' ),
				'plugin_text'                  => esc_html__( 'Recommended Plugins :', 'sparkling' ),
				'actions'                      => $sparkling_required_actions,
				'plugins'                      => $customizer_recommended_plugins,
				'theme_specific_option'        => $theme_slug . '_show_required_actions',
				'theme_specific_plugin_option' => $theme_slug . '_show_recommended_plugins',
				'facebook'                     => 'https://www.facebook.com/colorlib',
				'twitter'                      => 'https://twitter.com/colorlib',
				'wp_review'                    => true,
				'priority'                     => 0,
			)
		)
	);

	$wp_customize->add_section(
		new Epsilon_Section_Pro(
			$wp_customize,
			'epsilon-section-pro',
			array(
				'title'       => esc_html__( 'Sparkling', 'sparkling' ),
				'button_text' => esc_html__( 'Documentation', 'sparkling' ),
				'button_url'  => esc_url_raw( 'https://colorlib.com/wp/support/sparkling/' ),
				'priority'    => 0,
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
}// End if().
