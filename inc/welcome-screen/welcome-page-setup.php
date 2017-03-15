<?php

add_action( 'customize_register', 'a_customize_register' );

function a_customize_register($wp_customize){

	require_once get_template_directory() . '/inc/welcome-screen/custom-recommend-action-section.php';
		$wp_customize->register_section_type( 'Sparkling_Customize_Section_Recommend' );

		// Recomended Actions
		$wp_customize->add_section(
			new Sparkling_Customize_Section_Recommend(
				$wp_customize,
				'sparkling_recomended-section',
				array(
					'title'    => esc_html__( 'Recomended Actions', 'sparkling' ),
					'social_text'	=> esc_html__( 'We are social :', 'sparkling' ),
					'plugin_text'	=> esc_html__( 'Recomended Plugins :', 'sparkling' ),
					'facebook' => 'https://www.facebook.com/sparklings',
					'twitter' => 'https://twitter.com/sparklings',
					'wp_review' => true,
					'priority' => 0
				)
			)
		);

}

add_action( 'customize_controls_enqueue_scripts', 'sparkling_welcome_scripts_for_customizer', 0 );

function sparkling_welcome_scripts_for_customizer(){
	wp_enqueue_style( 'sparkling-welcome-screen-customizer-css', get_template_directory_uri() . '/inc/welcome-screen/css/welcome_customizer.css' );
	wp_enqueue_style( 'plugin-install' );
	wp_enqueue_script( 'plugin-install' );
	wp_enqueue_script( 'updates' );
	wp_add_inline_script( 'plugin-install', 'var pagenow = "customizer";' );
	wp_enqueue_script( 'sparkling-welcome-screen-customizer-js', get_template_directory_uri() . '/inc/welcome-screen/js/welcome_customizer.js', array( 'customize-controls' ), '1.0', true );

	wp_localize_script( 'sparkling-welcome-screen-customizer-js', 'sparklingWelcomeScreenObject', array(
		'ajaxurl'                  => admin_url( 'admin-ajax.php' ),
		'template_directory'       => get_template_directory_uri(),
	) );

}

// Load the system checks ( used for notifications )
require get_template_directory() . '/inc/welcome-screen/notify-system-checks.php';

// Welcome screen
if ( is_admin() ) {
	global $sparkling_required_actions, $sparkling_recommended_plugins;
	$sparkling_recommended_plugins = array(
		'fancybox-for-wordpress' 	=> array( 'recommended' => true ),
		'simple-custom-post-order' 	=> array( 'recommended' => true ),
	);
	/*
	 * id - unique id; required
	 * title
	 * description
	 * check - check for plugins (if installed)
	 * plugin_slug - the plugin's slug (used for installing the plugin)
	 *
	 */


	$sparkling_required_actions = array(
		array(
			"id"          => 'sparkling-req-ac-install-wp-import-plugin',
			"title"       => MT_Notify_System::wordpress_importer_title(),
			"description" => MT_Notify_System::wordpress_importer_description(),
			"check"       => MT_Notify_System::has_import_plugin( 'wordpress-importer' ),
			"plugin_slug" => 'wordpress-importer'
		),
		array(
			"id"          => 'sparkling-req-ac-install-wp-import-widget-plugin',
			"title"       => MT_Notify_System::widget_importer_exporter_title(),
			'description' => MT_Notify_System::widget_importer_exporter_description(),
			"check"       => MT_Notify_System::has_import_plugin( 'widget-importer-exporter' ),
			"plugin_slug" => 'widget-importer-exporter'
		),
		array(
			"id"          => 'sparkling-req-ac-download-data',
			"title"       => esc_html__( 'Download theme sample data', 'sparkling' ),
			"description" => esc_html__( 'Head over to our website and download the sample content data.', 'sparkling' ),
			"help"        => '<a target="_blank"  href="https://www.sparklings.com/sample-data/sparkling-lite-posts.xml">' . __( 'Posts', 'sparkling' ) . '</a>, 
							   <a target="_blank"  href="https://www.sparklings.com/sample-data/sparkling-widgets.wie">' . __( 'Widgets', 'sparkling' ) . '</a>',
			"check"       => MT_Notify_System::has_content(),
		),
		array(
			"id"    => 'sparkling-req-ac-install-data',
			"title" => esc_html__( 'Import Sample Data', 'sparkling' ),
			"help"  => '<a class="button button-primary" target="_blank"  href="' . self_admin_url( 'admin.php?import=wordpress' ) . '">' . __( 'Import Posts', 'sparkling' ) . '</a> 
									   <a class="button button-primary" target="_blank"  href="' . self_admin_url( 'tools.php?page=widget-importer-exporter' ) . '">' . __( 'Import Widgets', 'sparkling' ) . '</a>',
			"check" => MT_Notify_System::has_import_content(),
		),
	);
	require get_template_directory() . '/inc/welcome-screen/welcome-screen.php';
}