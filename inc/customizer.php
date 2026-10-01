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
	/*
	 * get_setting() returns null when a setting is not registered -- for example if
	 * a plugin has removed it. Assigning to ->transport on null is a fatal on PHP 8,
	 * so check each one before touching it.
	 */
	foreach ( array( 'blogname', 'blogdescription', 'header_textcolor' ) as $sparkling_setting_id ) {
		$sparkling_setting = $wp_customize->get_setting( $sparkling_setting_id );

		if ( $sparkling_setting instanceof WP_Customize_Setting ) {
			$sparkling_setting->transport = 'postMessage';
		}
	}
}

add_action( 'customize_register', 'sparkling_customize_register' );

/**
 * Options for Sparkling Theme Customizer.
 */
function sparkling_customizer( $wp_customize ) {

	/* Main option Settings Panel */
	$wp_customize->add_panel(
		'sparkling_main_options',
		array(
			'capability'     => 'edit_theme_options',
			'theme_supports' => '',
			'title'          => __( 'Sparkling Options', 'sparkling' ),
			'description'    => __( 'Panel to update sparkling theme options', 'sparkling' ),
			// Include html tags such as <p>.
			'priority'       => 10,
		// Mixed with top-level-section hierarchy.
		)
	);

	// add "Content Options" section
	$wp_customize->add_section(
		'sparkling_content_section',
		array(
			'title'    => esc_html__( 'Content Options', 'sparkling' ),
			'priority' => 50,
			'panel'    => 'sparkling_main_options',
		)
	);
	// add setting for excerpts/full posts toggle
	$wp_customize->add_setting(
		'sparkling_excerpts',
		array(
			'default'           => 0,
			'sanitize_callback' => 'sparkling_sanitize_checkbox',
		)
	);
	// add checkbox control for excerpts/full posts toggle
	$wp_customize->add_control(
		new Sparkling_Customize_Toggle_Control(
			$wp_customize,
			'sparkling_excerpts',
			array(
				'label'    => esc_html__( 'Show post excerpts?', 'sparkling' ),
				'section'  => 'sparkling_content_section',
				'priority' => 10,
				'type'     => 'sparkling-toggle',
			)
		)
	);

	$wp_customize->add_setting(
		'sparkling_page_comments',
		array(
			'default'           => 1,
			'sanitize_callback' => 'sparkling_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		new Sparkling_Customize_Toggle_Control(
			$wp_customize,
			'sparkling_page_comments',
			array(
				'label'    => esc_html__( 'Display Comments on Static Pages?', 'sparkling' ),
				'section'  => 'sparkling_content_section',
				'priority' => 20,
				'type'     => 'sparkling-toggle',
			)
		)
	);

	// Which Font Awesome library to load; see inc/fontawesome.php.
	$wp_customize->add_setting(
		'sparkling[fontawesome_library]',
		array(
			'default'           => 'auto',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sparkling_sanitize_fontawesome_library',
		)
	);
	$wp_customize->add_control(
		'sparkling[fontawesome_library]',
		array(
			'label'       => esc_html__( 'Font Awesome icons', 'sparkling' ),
			'section'     => 'sparkling_content_section',
			'priority'    => 30,
			'type'        => 'select',
			'description' => esc_html__( 'The theme bundles only the icons it displays itself, which keeps pages small. Icons you add to your menus are detected automatically. Choose "Always load the complete library" if you use Font Awesome icons in a widget, in post content or in a page builder and they are not showing.', 'sparkling' ),
			'choices'     => array(
				'auto'   => esc_html__( 'Automatic (recommended)', 'sparkling' ),
				'full'   => esc_html__( 'Always load the complete library', 'sparkling' ),
				'subset' => esc_html__( 'Only the icons the theme uses', 'sparkling' ),
			),
		)
	);

	/* Sparkling Main Options */
	$wp_customize->add_section(
		'sparkling_slider_options',
		array(
			'title'    => __( 'Slider Options', 'sparkling' ),
			'priority' => 31,
			'panel'    => 'sparkling_main_options',
		)
	);
	$wp_customize->add_setting(
		'sparkling[sparkling_slider_checkbox]',
		array(
			'default'           => 0,
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		new Sparkling_Customize_Toggle_Control(
			$wp_customize,
			'sparkling[sparkling_slider_checkbox]',
			array(
				'label'    => esc_html__( 'Check if you want to enable slider', 'sparkling' ),
				'section'  => 'sparkling_slider_options',
				'priority' => 5,
				'type'     => 'sparkling-toggle',
			)
		)
	);
	$wp_customize->add_setting(
		'sparkling[sparkling_slider_link_checkbox]',
		array(
			'default'           => 1,
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		new Sparkling_Customize_Toggle_Control(
			$wp_customize,
			'sparkling[sparkling_slider_link_checkbox]',
			array(
				'label'    => esc_html__( 'Uncheck this option to remove the link from the slides', 'sparkling' ),
				'section'  => 'sparkling_slider_options',
				'priority' => 6,
				'type'     => 'sparkling-toggle',
			)
		)
	);

	// Pull all the categories into an array
	global $options_categories;
	$wp_customize->add_setting(
		'sparkling[sparkling_slide_categories]',
		array(
			'default'           => '',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sparkling_sanitize_slidecat',
		)
	);
	$wp_customize->add_control(
		'sparkling[sparkling_slide_categories]',
		array(
			'label'       => __( 'Slider Category', 'sparkling' ),
			'section'     => 'sparkling_slider_options',
			'type'        => 'select',
			'description' => __( 'Select a category for the featured post slider', 'sparkling' ),
			/*
			 * The default, '', is "every category" -- which is what the slider
			 * shows -- but it was not one of the choices, so the control showed
			 * the first category as selected while the site used all of them.
			 */
			'choices'     => array( '' => __( 'All categories', 'sparkling' ) ) + (array) $options_categories,
		)
	);

	$wp_customize->add_setting(
		'sparkling[sparkling_slide_number]',
		array(
			// The slider shows five slides until this is saved, so the control
			// must say five; it said three.
			'default'           => 5,
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_number',
		)
	);
	$wp_customize->add_control(
		'sparkling[sparkling_slide_number]',
		array(
			'label'       => __( 'Number of slide items', 'sparkling' ),
			'section'     => 'sparkling_slider_options',
			'description' => __( 'Enter the number of slide items', 'sparkling' ),
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 1,
				'max'  => 20,
				'step' => 1,
			),
		)
	);

	$wp_customize->add_section(
		'sparkling_layout_options',
		array(
			'title'    => __( 'Layout Options', 'sparkling' ),
			'priority' => 31,
			'panel'    => 'sparkling_main_options',
		)
	);
	// Layout options
	global $site_layout;
	$wp_customize->add_setting(
		'sparkling[site_layout]',
		array(
			'default'           => 'side-pull-left',
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_layout',
		)
	);
	$wp_customize->add_control(
		'sparkling[site_layout]',
		array(
			'label'       => __( 'Website Layout Options', 'sparkling' ),
			'section'     => 'sparkling_layout_options',
			'type'        => 'select',
			'description' => __( 'Choose between different layout options to be used as default', 'sparkling' ),
			'choices'     => $site_layout,
		)
	);

	if ( class_exists( 'WooCommerce' ) ) {
		$wp_customize->add_setting(
			'sparkling[woo_site_layout]',
			array(
				'default'           => 'full-width',
				'type'              => 'option',
				'sanitize_callback' => 'sparkling_sanitize_layout',
			)
		);
		$wp_customize->add_control(
			'sparkling[woo_site_layout]',
			array(
				'label'       => __( 'WooCommerce Page Layout Options', 'sparkling' ),
				'section'     => 'sparkling_layout_options',
				'type'        => 'select',
				'description' => __( 'Choose between different layout options to be used as default for all woocommerce pages', 'sparkling' ),
				'choices'     => $site_layout,
			)
		);
	}

	$wp_customize->add_setting(
		'sparkling[element_color]',
		array(
			'default'           => sanitize_hex_color( '#DA4453' ),
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_hexcolor',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'sparkling[element_color]',
			array(
				'label'       => __( 'Element Color', 'sparkling' ),
				'description' => __( 'Default used if no color is selected', 'sparkling' ),
				'section'     => 'sparkling_layout_options',
				'settings'    => 'sparkling[element_color]',
			)
		)
	);

	$wp_customize->add_setting(
		'sparkling[element_color_hover]',
		array(
			'default'           => sanitize_hex_color( '#363636' ),
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_hexcolor',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'sparkling[element_color_hover]',
			array(
				'label'       => __( 'Element color on hover', 'sparkling' ),
				'description' => __( 'Default used if no color is selected', 'sparkling' ),
				'section'     => 'sparkling_layout_options',
				'settings'    => 'sparkling[element_color_hover]',
			)
		)
	);

	/* Sparkling Action Options */
	$wp_customize->add_section(
		'sparkling_action_options',
		array(
			'title'    => __( 'Action Button', 'sparkling' ),
			'priority' => 31,
			'panel'    => 'sparkling_main_options',
		)
	);
	$wp_customize->add_setting(
		'sparkling[w2f_cfa_text]',
		array(
			'default'           => '',
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_strip_slashes',
		)
	);
	$wp_customize->add_control(
		'sparkling[w2f_cfa_text]',
		array(
			'label'       => __( 'Call For Action Text', 'sparkling' ),
			'description' => sprintf( __( 'Enter the text for call for action section', 'sparkling' ) ),
			'section'     => 'sparkling_action_options',
			'type'        => 'textarea',
		)
	);

	$wp_customize->add_setting(
		'sparkling[w2f_cfa_button]',
		array(
			'default'           => '',
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_nohtml',
		)
	);
	$wp_customize->add_control(
		'sparkling[w2f_cfa_button]',
		array(
			'label'       => __( 'Call For Action Button Title', 'sparkling' ),
			'section'     => 'sparkling_action_options',
			'description' => __( 'Enter the title for Call For Action button', 'sparkling' ),
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting(
		'sparkling[w2f_cfa_link]',
		array(
			'default'           => '',
			'type'              => 'option',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'sparkling[w2f_cfa_link]',
		array(
			'type'        => 'url',
			'label'       => __( 'CFA button link', 'sparkling' ),
			'section'     => 'sparkling_action_options',
			'description' => __( 'Enter the link for Call For Action button', 'sparkling' ),
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting(
		'sparkling[cfa_color]',
		array(
			'default'           => sanitize_hex_color( '#DA4453' ),
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_hexcolor',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'sparkling[cfa_color]',
			array(
				'label'       => __( 'Call For Action Text Color', 'sparkling' ),
				'description' => __( 'Default used if no color is selected', 'sparkling' ),
				'section'     => 'sparkling_action_options',
			)
		)
	);
	$wp_customize->add_setting(
		'sparkling[cfa_bg_color]',
		array(
			'default'           => sanitize_hex_color( '#FFF' ),
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_hexcolor',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'sparkling[cfa_bg_color]',
			array(
				'label'       => __( 'Call For Action Background Color', 'sparkling' ),
				'description' => __( 'Default used if no color is selected', 'sparkling' ),
				'section'     => 'sparkling_action_options',
			)
		)
	);
	$wp_customize->add_setting(
		'sparkling[cfa_btn_color]',
		array(
			'default'           => sanitize_hex_color( '#DA4453' ),
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_hexcolor',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'sparkling[cfa_btn_color]',
			array(
				'label'       => __( 'Call For Action Button Border Color', 'sparkling' ),
				'description' => __( 'Default used if no color is selected', 'sparkling' ),
				'section'     => 'sparkling_action_options',
			)
		)
	);
	$wp_customize->add_setting(
		'sparkling[cfa_btn_txt_color]',
		array(
			'default'           => sanitize_hex_color( '#DA4453' ),
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_hexcolor',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'sparkling[cfa_btn_txt_color]',
			array(
				'label'       => __( 'Call For Action Button Text Color', 'sparkling' ),
				'description' => __( 'Default used if no color is selected', 'sparkling' ),
				'section'     => 'sparkling_action_options',
			)
		)
	);

	/* Sparkling Typography Options */
	$wp_customize->add_section(
		'sparkling_typography_options',
		array(
			'title'    => __( 'Typography', 'sparkling' ),
			'priority' => 31,
			'panel'    => 'sparkling_main_options',
		)
	);
	// Typography Defaults
	$typography_defaults = sparkling_typography_defaults();

	// Typography Options
	global $typography_options;
	$wp_customize->add_setting(
		'sparkling[main_body_typography][size]',
		array(
			'default'           => $typography_defaults['size'],
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_typo_size',
		)
	);
	$wp_customize->add_control(
		'sparkling[main_body_typography][size]',
		array(
			'label'       => __( 'Main Body Text', 'sparkling' ),
			'description' => __( 'Used in p tags', 'sparkling' ),
			'section'     => 'sparkling_typography_options',
			'type'        => 'select',
			'choices'     => $typography_options['sizes'],
		)
	);
	$wp_customize->add_setting(
		'sparkling[main_body_typography][face]',
		array(
			'default'           => $typography_defaults['face'],
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_typo_face',
		)
	);
	$wp_customize->add_control(
		'sparkling[main_body_typography][face]',
		array(
			'section' => 'sparkling_typography_options',
			'type'    => 'select',
			'choices' => $typography_options['faces'],
		)
	);
	$wp_customize->add_setting(
		'sparkling[main_body_typography][style]',
		array(
			'default'           => $typography_defaults['style'],
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_typo_style',
		)
	);
	$wp_customize->add_control(
		'sparkling[main_body_typography][style]',
		array(
			'section' => 'sparkling_typography_options',
			'type'    => 'select',
			'choices' => $typography_options['styles'],
		)
	);
	$wp_customize->add_setting(
		'sparkling[main_body_typography][color]',
		array(
			'default'           => $typography_defaults['color'],
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_hexcolor',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'sparkling[main_body_typography][color]',
			array(
				'section' => 'sparkling_typography_options',
			)
		)
	);
	/*
	 * The "Font Subset" field is gone: it asked for a Google Fonts subset, and
	 * since 2.6.11 the fonts are bundled with the theme rather than requested
	 * from Google, so the value was never used.
	 */

	$wp_customize->add_setting(
		'sparkling[heading_color]',
		array(
			'default'           => sanitize_hex_color( '#444' ),
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_hexcolor',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'sparkling[heading_color]',
			array(
				'label'       => __( 'Heading Color', 'sparkling' ),
				'description' => __( 'Color for all headings (h1-h6)', 'sparkling' ),
				'section'     => 'sparkling_typography_options',
			)
		)
	);
	$wp_customize->add_setting(
		'sparkling[link_color]',
		array(
			'default'           => sanitize_hex_color( '#DA4453' ),
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_hexcolor',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'sparkling[link_color]',
			array(
				'label'       => __( 'Link Color', 'sparkling' ),
				'description' => __( 'Default used if no color is selected', 'sparkling' ),
				'section'     => 'sparkling_typography_options',
			)
		)
	);
	$wp_customize->add_setting(
		'sparkling[link_hover_color]',
		array(
			'default'           => sanitize_hex_color( '#DADADA' ),
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_hexcolor',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'sparkling[link_hover_color]',
			array(
				'label'       => __( 'Link:hover Color', 'sparkling' ),
				'description' => __( 'Default used if no color is selected', 'sparkling' ),
				'section'     => 'sparkling_typography_options',
			)
		)
	);

	/* Sparkling Header Options */
	$wp_customize->add_section(
		'sparkling_header_options',
		array(
			'title'    => __( 'Header', 'sparkling' ),
			'priority' => 31,
			'panel'    => 'sparkling_main_options',
		)
	);

	$wp_customize->add_setting(
		'sparkling[sticky_header]',
		array(
			'default'           => 0,
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		new Sparkling_Customize_Toggle_Control(
			$wp_customize,
			'sparkling[sticky_header]',
			array(
				'label'       => __( 'Sticky Header', 'sparkling' ),
				'description' => sprintf( __( 'Check to show fixed header', 'sparkling' ) ),
				'section'     => 'sparkling_header_options',
				'type'        => 'sparkling-toggle',
			)
		)
	);

	$wp_customize->add_setting(
		'sparkling[nav_bg_color]',
		array(
			'default'           => sanitize_hex_color( '#363636' ),
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_hexcolor',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'sparkling[nav_bg_color]',
			array(
				'label'       => __( 'Top nav background color', 'sparkling' ),
				'description' => __( 'Default used if no color is selected', 'sparkling' ),
				'section'     => 'sparkling_header_options',
			)
		)
	);
	$wp_customize->add_setting(
		'sparkling[nav_link_color]',
		array(
			'default'           => sanitize_hex_color( '#F5F7FA' ),
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_hexcolor',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'sparkling[nav_link_color]',
			array(
				'label'       => __( 'Top nav item color', 'sparkling' ),
				'description' => __( 'Link color', 'sparkling' ),
				'section'     => 'sparkling_header_options',
			)
		)
	);

	$wp_customize->add_setting(
		'sparkling[nav_item_hover_color]',
		array(
			'default'           => sanitize_hex_color( '#DA4453' ),
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_hexcolor',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'sparkling[nav_item_hover_color]',
			array(
				'label'       => __( 'Top nav item hover color', 'sparkling' ),
				'description' => __( 'Link:hover color', 'sparkling' ),
				'section'     => 'sparkling_header_options',
			)
		)
	);

	$wp_customize->add_setting(
		'sparkling[nav_dropdown_bg]',
		array(
			'default'           => sanitize_hex_color( '#da4453' ),
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_hexcolor',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'sparkling[nav_dropdown_bg]',
			array(
				'label'       => __( 'Top nav dropdown background color', 'sparkling' ),
				'description' => __( 'Background of dropdown item hover color', 'sparkling' ),
				'section'     => 'sparkling_header_options',
			)
		)
	);

	$wp_customize->add_setting(
		'sparkling[nav_dropdown_item]',
		array(
			'default'           => sanitize_hex_color( '#636467' ),
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_hexcolor',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'sparkling[nav_dropdown_item]',
			array(
				'label'       => __( 'Top nav dropdown item color', 'sparkling' ),
				'description' => __( 'Dropdown item color', 'sparkling' ),
				'section'     => 'sparkling_header_options',
			)
		)
	);

	$wp_customize->add_setting(
		'sparkling[nav_dropdown_item_hover]',
		array(
			'default'           => sanitize_hex_color( '#FFF' ),
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_hexcolor',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'sparkling[nav_dropdown_item_hover]',
			array(
				'label'       => __( 'Top nav dropdown item hover color', 'sparkling' ),
				'description' => __( 'Dropdown item hover color', 'sparkling' ),
				'section'     => 'sparkling_header_options',
			)
		)
	);

	$wp_customize->add_setting(
		'sparkling[nav_dropdown_bg_hover]',
		array(
			'default'           => sanitize_hex_color( '#DA4453' ),
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_hexcolor',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'sparkling[nav_dropdown_bg_hover]',
			array(
				'label'       => __( 'Top nav dropdown item background hover color', 'sparkling' ),
				'description' => __( 'Background of dropdown item hover color', 'sparkling' ),
				'section'     => 'sparkling_header_options',
			)
		)
	);

	/* Sparkling Footer Options */
	$wp_customize->add_section(
		'sparkling_footer_options',
		array(
			'title'    => __( 'Footer', 'sparkling' ),
			'priority' => 31,
			'panel'    => 'sparkling_main_options',
		)
	);
	$wp_customize->add_setting(
		'sparkling[footer_widget_bg_color]',
		array(
			'default'           => sanitize_hex_color( '#313233' ),
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_hexcolor',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'sparkling[footer_widget_bg_color]',
			array(
				'label'   => __( 'Footer widget area background color', 'sparkling' ),
				'section' => 'sparkling_footer_options',
			)
		)
	);

	$wp_customize->add_setting(
		'sparkling[footer_bg_color]',
		array(
			'default'           => sanitize_hex_color( '#1F1F1F' ),
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_hexcolor',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'sparkling[footer_bg_color]',
			array(
				'label'   => __( 'Footer background color', 'sparkling' ),
				'section' => 'sparkling_footer_options',
			)
		)
	);

	$wp_customize->add_setting(
		'sparkling[footer_text_color]',
		array(
			'default'           => sanitize_hex_color( '#999' ),
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_hexcolor',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'sparkling[footer_text_color]',
			array(
				'label'   => __( 'Footer text color', 'sparkling' ),
				'section' => 'sparkling_footer_options',
			)
		)
	);

	$wp_customize->add_setting(
		'sparkling[footer_link_color]',
		array(
			'default'           => sanitize_hex_color( '#DADADA' ),
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_hexcolor',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'sparkling[footer_link_color]',
			array(
				'label'   => __( 'Footer link color', 'sparkling' ),
				'section' => 'sparkling_footer_options',
			)
		)
	);

	$wp_customize->add_setting(
		'sparkling[custom_footer_text]',
		array(
			'default'           => '',
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_strip_slashes',
		)
	);
	$wp_customize->add_control(
		'sparkling[custom_footer_text]',
		array(
			'label'       => __( 'Footer information', 'sparkling' ),
			'description' => sprintf( __( 'Copyright text in footer', 'sparkling' ) ),
			'section'     => 'sparkling_footer_options',
			'type'        => 'textarea',
		)
	);

	/* Sparkling Social Options */
	$wp_customize->add_section(
		'sparkling_social_options',
		array(
			'title'    => __( 'Social', 'sparkling' ),
			'priority' => 31,
			'panel'    => 'sparkling_main_options',
		)
	);
	$wp_customize->add_setting(
		'sparkling[social_color]',
		array(
			'default'           => sanitize_hex_color( '#DADADA' ),
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_hexcolor',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'sparkling[social_color]',
			array(
				'label'       => __( 'Social icon color', 'sparkling' ),
				'description' => sprintf( __( 'Default used if no color is selected', 'sparkling' ) ),
				'section'     => 'sparkling_social_options',
			)
		)
	);

	$wp_customize->add_setting(
		'sparkling[social_footer_color]',
		array(
			'default'           => sanitize_hex_color( '#363636' ),
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_hexcolor',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'sparkling[social_footer_color]',
			array(
				'label'       => __( 'Footer social icon color', 'sparkling' ),
				'description' => sprintf( __( 'Default used if no color is selected', 'sparkling' ) ),
				'section'     => 'sparkling_social_options',
			)
		)
	);

	$wp_customize->add_setting(
		'sparkling[footer_social]',
		array(
			'default'           => 0,
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		new Sparkling_Customize_Toggle_Control(
			$wp_customize,
			'sparkling[footer_social]',
			array(
				'label'       => __( 'Footer Social Icons', 'sparkling' ),
				'description' => sprintf( __( 'Check to show social icons in footer', 'sparkling' ) ),
				'section'     => 'sparkling_social_options',
				'type'        => 'sparkling-toggle',
			)
		)
	);

	/* Academicons */
	$wp_customize->add_setting(
		'sparkling[academicons]',
		array(
			'default'           => 0,
			'type'              => 'option',
			'sanitize_callback' => 'sparkling_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		new Sparkling_Customize_Toggle_Control(
			$wp_customize,
			'sparkling[academicons]',
			array(
				'label'       => __( 'Enable Academicons', 'sparkling' ),
				'description' => sprintf( __( 'Toggle this to ON to enable the usage of Academicons', 'sparkling' ) ),
				'section'     => 'sparkling_social_options',
				'type'        => 'sparkling-toggle',
			)
		)
	);

	/* Archive pages settings */
	$wp_customize->add_section(
		'sparkling_archive_section',
		array(
			'title'    => esc_html__( 'Archive Pages', 'sparkling' ),
			'priority' => 50,
			'panel'    => 'sparkling_main_options',
		)
	);

	$wp_customize->add_setting(
		'sparkling[tag_title]',
		array(
			'default'           => '',
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'sparkling[tag_title]',
		array(
			'label'       => __( 'Tag Page Title', 'sparkling' ),
			'section'     => 'sparkling_archive_section',
			/* translators: %s: tag name */
			'description' => __( 'The headline for your tag pages. You can use %s as a placeholder for the tag. Leave empty for default.', 'sparkling' ),
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting(
		'sparkling[category_title]',
		array(
			'default'           => '',
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'sparkling[category_title]',
		array(
			'label'       => __( 'Category Page Title', 'sparkling' ),
			'section'     => 'sparkling_archive_section',
			/* translators: %s: category name */
			'description' => __( 'The headline for your category pages. You can use %s as a placeholder for the category. Leave empty for default.', 'sparkling' ),
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting(
		'sparkling[author_title]',
		array(
			'default'           => '',
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'sparkling[author_title]',
		array(
			'label'       => __( 'Author Page Title', 'sparkling' ),
			'section'     => 'sparkling_archive_section',
			/* translators: %s: author name */
			'description' => __( 'The headline for your author pages. You can use %s as a placeholder for the author\'s name. Leave empty for default.', 'sparkling' ),
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting(
		'sparkling[year_title]',
		array(
			'default'           => '',
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'sparkling[year_title]',
		array(
			'label'       => __( 'Year Page Title', 'sparkling' ),
			'section'     => 'sparkling_archive_section',
			/* translators: %s: year */
			'description' => __( 'The headline for your year pages. You can use %s as a placeholder for the year. Leave empty for default.', 'sparkling' ),
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting(
		'sparkling[month_title]',
		array(
			'default'           => '',
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'sparkling[month_title]',
		array(
			'label'       => __( 'Month Page Title', 'sparkling' ),
			'section'     => 'sparkling_archive_section',
			/* translators: %s: month */
			'description' => __( 'The headline for your month pages. You can use %s as a placeholder for the month. Leave empty for default.', 'sparkling' ),
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting(
		'sparkling[day_title]',
		array(
			'default'           => '',
			'type'              => 'option',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'sparkling[day_title]',
		array(
			'label'       => __( 'Day Page Title', 'sparkling' ),
			'section'     => 'sparkling_archive_section',
			/* translators: %s: day */
			'description' => __( 'The headline for your day pages. You can use %s as a placeholder for the day. Leave empty for default.', 'sparkling' ),
			'type'        => 'text',
		)
	);
}

add_action( 'customize_register', 'sparkling_customizer' );


if ( ! function_exists( 'sparkling_typography_defaults' ) ) :
	/**
	 * Defaults for the body typography settings: what style.css renders.
	 *
	 * @return array
	 */
	function sparkling_typography_defaults() {
		return array(
			'size'  => '14px',
			'face'  => 'Open Sans',
			'style' => 'normal',
			'color' => '#6B6B6B',
		);
	}
endif;

/**
 * Sanitize checkbox for WordPress customizer
 */
function sparkling_sanitize_checkbox( $input ) {
	if ( 1 == $input ) {
		return 1;
	} else {
		return '';
	}
}

/**
 * Adds sanitization callback function: Font Awesome library choice
 * @package Sparkling
 */
function sparkling_sanitize_fontawesome_library( $input ) {
	if ( in_array( $input, array( 'auto', 'full', 'subset' ), true ) ) {
		return $input;
	}

	return 'auto';
}

/**
 * Adds sanitization callback function: colors
 * @package Sparkling
 */
function sparkling_sanitize_hexcolor( $color ) {
	$unhashed = sanitize_hex_color_no_hash( $color );
	if ( $unhashed ) {
		return '#' . $unhashed;
	}

	/*
	 * Previously this returned $color unchanged when validation failed, which
	 * meant arbitrary text could be stored and later echoed straight into the
	 * <style> block emitted by get_sparkling_theme_options(). Reject instead.
	 */
	return '';
}

/**
 * Adds sanitization callback function: Nohtml
 * @package Sparkling
 */
function sparkling_sanitize_nohtml( $input ) {
	return wp_filter_nohtml_kses( $input );
}

/**
 * Adds sanitization callback function: Number
 *
 * A whole number of at least 1. This returned nothing at all for anything
 * non-numeric and let "2.5" or "-3" through.
 *
 * @param mixed $input Value from the Customizer.
 * @return int|string
 */
function sparkling_sanitize_number( $input ) {
	$number = absint( $input );

	return $number ? $number : '';
}

/**
 * Adds sanitization callback function: Strip Slashes
 * @package Sparkling
 */
function sparkling_sanitize_strip_slashes( $input ) {
	return wp_kses_stripslashes( $input );
}

/**
 * Adds sanitization callback function: Sanitize Text area
 * @package Sparkling
 */
function sparkling_sanitize_textarea( $input ) {
	return sanitize_text_field( $input );
}

/**
 * Adds sanitization callback function: Slider Category
 * @package Sparkling
 */
function sparkling_sanitize_slidecat( $input ) {
	global $options_categories;

	// '' is "All categories", the default.
	if ( '' === $input || null === $input ) {
		return '';
	}

	if ( is_array( $options_categories ) && array_key_exists( absint( $input ), $options_categories ) ) {
		return $input;
	} else {
		return '';
	}
}

/**
 * Adds sanitization callback function: Sidebar Layout
 * @package Sparkling
 */
function sparkling_sanitize_layout( $input ) {
	global $site_layout;
	if ( is_string( $input ) && is_array( $site_layout ) && array_key_exists( $input, $site_layout ) ) {
		return $input;
	} else {
		return '';
	}
}

/**
 * Adds sanitization callback function: Typography Size
 * @package Sparkling
 */
function sparkling_sanitize_typo_size( $input ) {
	global $typography_options;

	// $typography_defaults was never a global, so an unknown value came back
	// as null with an "array offset on null" warning instead of the default.
	if ( is_string( $input ) && array_key_exists( $input, $typography_options['sizes'] ) ) {
		return $input;
	}

	$defaults = sparkling_typography_defaults();

	return $defaults['size'];
}

/**
 * Adds sanitization callback function: Typography Face
 * @package Sparkling
 */
function sparkling_sanitize_typo_face( $input ) {
	global $typography_options;

	// $typography_defaults was never a global, so an unknown value came back
	// as null with an "array offset on null" warning instead of the default.
	if ( is_string( $input ) && array_key_exists( $input, $typography_options['faces'] ) ) {
		return $input;
	}

	$defaults = sparkling_typography_defaults();

	return $defaults['face'];
}

/**
 * Adds sanitization callback function: Typography Style
 * @package Sparkling
 */
function sparkling_sanitize_typo_style( $input ) {
	global $typography_options;

	// $typography_defaults was never a global, so an unknown value came back
	// as null with an "array offset on null" warning instead of the default.
	if ( is_string( $input ) && array_key_exists( $input, $typography_options['styles'] ) ) {
		return $input;
	}

	$defaults = sparkling_typography_defaults();

	return $defaults['style'];
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function sparkling_customize_preview_js() {
	wp_enqueue_script( 'sparkling_customizer', get_template_directory_uri() . '/assets/js/customizer.js', array( 'customize-preview' ), SPARKLING_VERSION, true );
}

add_action( 'customize_preview_init', 'sparkling_customize_preview_js' );

/**
 * Styles for the theme's own Customizer controls.
 *
 * Carries the on/off switch and tooltip rules that used to come from the bundled
 * Epsilon framework's stylesheet, so the controls look unchanged after its removal.
 */
function sparkling_customize_controls_css() {
	wp_enqueue_style( 'sparkling-customizer-controls', get_template_directory_uri() . '/assets/css/customizer.css', array(), SPARKLING_VERSION );
}

add_action( 'customize_controls_enqueue_scripts', 'sparkling_customize_controls_css' );

/**
 * Add CSS for custom controls
 */
function sparkling_customizer_custom_control_css() {
	?>
	<style>
		#customize-control-sparkling-main_body_typography-size select, #customize-control-sparkling-main_body_typography-face select, #customize-control-sparkling-main_body_typography-style select {
			width: 60%;
		}
	</style>
	<?php
}

add_action( 'customize_controls_print_styles', 'sparkling_customizer_custom_control_css' );

/*
 * Custom Scripts
 */
add_action( 'customize_controls_print_footer_scripts', 'customizer_custom_scripts' );

function customizer_custom_scripts() {
	?>
	<script type="text/javascript">
		jQuery(document).ready(function () {
		/* This one shows/hides the an option when a checkbox is clicked. */
		jQuery('#customize-control-sparkling-sparkling_slide_categories, #customize-control-sparkling-sparkling_slide_number').hide()
		jQuery('#customize-control-sparkling-sparkling_slider_checkbox input').click(function () {
			jQuery('#customize-control-sparkling-sparkling_slide_categories, #customize-control-sparkling-sparkling_slide_number').fadeToggle(400)
		})

		if (jQuery('#customize-control-sparkling-sparkling_slider_checkbox input:checked').val() !== undefined) {
			jQuery('#customize-control-sparkling-sparkling_slide_categories, #customize-control-sparkling-sparkling_slide_number').show()
		}
		})
	</script>
	<style>
		li#accordion-section-sparkling_important_links h3.accordion-section-title, li#accordion-section-sparkling_important_links h3.accordion-section-title:focus {
			background-color: #00cc00 !important;
			color: #fff !important;
		}

		li#accordion-section-sparkling_important_links h3.accordion-section-title:hover {
			background-color: #00b200 !important;
			color: #fff !important;
		}

		li#accordion-section-sparkling_important_links h3.accordion-section-title:after {
			color: #fff !important;
		}
	</style>
	<?php
}
