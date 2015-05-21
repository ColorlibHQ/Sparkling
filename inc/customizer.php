<?php
/**
 * Sparkling Theme Customizer
 *
 * @package sparkling
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function sparkling_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
}
add_action( 'customize_register', 'sparkling_customize_register' );

/**
 * Ootions for WordPress Theme Customizer.
 */
function sparkling_customizer( $wp_customize ) {

	// add "Content Options" section
	$wp_customize->add_section( 'sparkling_content_section' , array(
		'title'      => esc_html__( 'Content Options', 'sparkling' ),
		'priority'   => 50,
	) );

	// add setting for excerpts/full posts toggle
	$wp_customize->add_setting( 'sparkling_excerpts', array(
		'default'           => 1,
		'sanitize_callback' => 'sparkling_sanitize_checkbox',
	) );

	// add checkbox control for excerpts/full posts toggle
	$wp_customize->add_control( 'sparkling_excerpts', array(
		'label'     => esc_html__( 'Show post excerpts?', 'sparkling' ),
		'section'   => 'sparkling_content_section',
		'priority'  => 10,
		'type'      => 'checkbox'
	) );

	$wp_customize->add_setting( 'sparkling_page_comments', array(
		'default' => 1,
		'sanitize_callback' => 'sparkling_sanitize_checkbox',
	) );

	$wp_customize->add_control( 'sparkling_page_comments', array(
	'label'		=> esc_html__( 'Display Comments on Static Pages?', 'sparkling' ),
	'section'	=> 'sparkling_content_section',
	'priority'	=> 20,
	'type'      => 'checkbox',
) );
}
add_action( 'customize_register', 'sparkling_customizer' );



/**
 * Sanitzie checkbox for WordPress customizer
 */
function sparkling_sanitize_checkbox( $input ) {
    if ( $input == 1 ) {
        return 1;
    } else {
        return '';
    }
}
/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function sparkling_customize_preview_js() {
	wp_enqueue_script( 'sparkling_customizer', get_template_directory_uri() . '/inc/js/customizer.js', array( 'customize-preview' ), '20140317', true );
}
add_action( 'customize_preview_init', 'sparkling_customize_preview_js' );
