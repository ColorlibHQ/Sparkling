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
 * Options for Sparkling Theme Customizer.
 */
function sparkling_customizer( $wp_customize ) {

    /* Main option Settings Panel */
    $wp_customize->add_panel('sparkling_main_options', array(
        'capability' => 'edit_theme_options',
        'theme_supports' => '',
        'title' => __('Sparkling Options', 'sparkling'),
        'description' => __('Panel to update sparkling theme options', 'sparkling'), // Include html tags such as <p>.
        'priority' => 10 // Mixed with top-level-section hierarchy.
    ));

        // add "Content Options" section
        $wp_customize->add_section( 'sparkling_content_section' , array(
                'title'      => esc_html__( 'Content Options', 'sparkling' ),
                'priority'   => 50,
                'panel' => 'sparkling_main_options'
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

        /* Sparkling Main Options */
        $wp_customize->add_section('sparkling_slider_options', array(
            'title' => __('Slider options', 'sparkling'),
            'priority' => 31,
            'panel' => 'sparkling_main_options'
        ));
            $wp_customize->add_setting( 'sparkling[sparkling_slider_checkbox]', array(
                    'default' => 0,
                    'type' => 'option',
                    'sanitize_callback' => 'sparkling_sanitize_checkbox',
            ) );
            $wp_customize->add_control( 'sparkling[sparkling_slider_checkbox]', array(
                    'label'	=> esc_html__( 'Check if you want to enable slider', 'sparkling' ),
                    'section'	=> 'sparkling_slider_options',
                    'priority'	=> 5,
                    'type'      => 'checkbox',
            ) );
						$wp_customize->add_setting( 'sparkling[sparkling_slider_link_checkbox]', array(
                    'default' => 1,
                    'type' => 'option',
                    'sanitize_callback' => 'sparkling_sanitize_checkbox',
            ) );
            $wp_customize->add_control( 'sparkling[sparkling_slider_link_checkbox]', array(
                    'label'	=> esc_html__( 'Uncheck to simply show the image in the slider', 'sparkling' ),
                    'section'	=> 'sparkling_slider_options',
                    'priority'	=> 6,
                    'type'      => 'checkbox',
            ) );

            // Pull all the categories into an array
            global $options_categories;
            $wp_customize->add_setting('sparkling[sparkling_slide_categories]', array(
                'default' => '',
                'type' => 'option',
                'capability' => 'edit_theme_options',
                'sanitize_callback' => 'sparkling_sanitize_slidecat'
            ));
            $wp_customize->add_control('sparkling[sparkling_slide_categories]', array(
                'label' => __('Slider Category', 'sparkling'),
                'section' => 'sparkling_slider_options',
                'type'    => 'select',
                'description' => __('Select a category for the featured post slider', 'sparkling'),
                'choices'    => $options_categories
            ));

            $wp_customize->add_setting('sparkling[sparkling_slide_number]', array(
                'default' => 3,
                'type' => 'option',
                'sanitize_callback' => 'sparkling_sanitize_number'
            ));
            $wp_customize->add_control('sparkling[sparkling_slide_number]', array(
                'label' => __('Number of slide items', 'sparkling'),
                'section' => 'sparkling_slider_options',
                'description' => __('Enter the number of slide items', 'sparkling'),
                'type' => 'text'
            ));

        $wp_customize->add_section('sparkling_layout_options', array(
            'title' => __('Layout options', 'sparkling'),
            'priority' => 31,
            'panel' => 'sparkling_main_options'
        ));
            // Layout options
            global $site_layout;
            $wp_customize->add_setting('sparkling[site_layout]', array(
                 'default' => 'side-pull-left',
                 'type' => 'option',
                 'sanitize_callback' => 'sparkling_sanitize_layout'
            ));
            $wp_customize->add_control('sparkling[site_layout]', array(
                 'label' => __('Website Layout Options', 'sparkling'),
                 'section' => 'sparkling_layout_options',
                 'type'    => 'select',
                 'description' => __('Choose between different layout options to be used as default', 'sparkling'),
                 'choices'    => $site_layout
            ));

            if ( class_exists( 'WooCommerce' ) ) {
                $wp_customize->add_setting('sparkling[woo_site_layout]', array(
                     'default' => 'full-width',
                     'type' => 'option',
                     'sanitize_callback' => 'sparkling_sanitize_layout'
                ));
                $wp_customize->add_control('sparkling[woo_site_layout]', array(
                     'label' => __('WooCommerce Page Layout Options', 'sparkling'),
                     'section' => 'sparkling_layout_options',
                     'type'    => 'select',
                     'description' => __('Choose between different layout options to be used as default for all woocommerce pages', 'sparkling'),
                     'choices'    => $site_layout
                ));
            }

            $wp_customize->add_setting('sparkling[element_color]', array(
                'default' => '',
                'type'  => 'option',
                'sanitize_callback' => 'sparkling_sanitize_hexcolor'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sparkling[element_color]', array(
                'label' => __('Element Color', 'sparkling'),
                'description'   => __('Default used if no color is selected','sparkling'),
                'section' => 'sparkling_layout_options',
                'settings' => 'sparkling[element_color]',
            )));

            $wp_customize->add_setting('sparkling[element_color_hover]', array(
                'default' => '',
                'type'  => 'option',
                'sanitize_callback' => 'sparkling_sanitize_hexcolor'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sparkling[element_color_hover]', array(
                'label' => __('Element color on hover', 'sparkling'),
                'description'   => __('Default used if no color is selected','sparkling'),
                'section' => 'sparkling_layout_options',
                'settings' => 'sparkling[element_color_hover]',
            )));

         /* Sparkling Action Options */
        $wp_customize->add_section('sparkling_action_options', array(
            'title' => __('Action Button', 'sparkling'),
            'priority' => 31,
            'panel' => 'sparkling_main_options'
        ));
            $wp_customize->add_setting('sparkling[w2f_cfa_text]', array(
                'default' => '',
                'type' => 'option',
                'sanitize_callback' => 'sparkling_sanitize_strip_slashes'
            ));
            $wp_customize->add_control('sparkling[w2f_cfa_text]', array(
                'label' => __('Call For Action Text', 'sparkling'),
                'description' => sprintf(__('Enter the text for call for action section', 'sparkling')),
                'section' => 'sparkling_action_options',
                'type' => 'textarea'
            ));

            $wp_customize->add_setting('sparkling[w2f_cfa_button]', array(
                'default' => '',
                'type' => 'option',
                'sanitize_callback' => 'sparkling_sanitize_nohtml'
            ));
            $wp_customize->add_control('sparkling[w2f_cfa_button]', array(
                'label' => __('Call For Action Button Title', 'sparkling'),
                'section' => 'sparkling_action_options',
                'description' => __('Enter the title for Call For Action button', 'sparkling'),
                'type' => 'text'
            ));

            $wp_customize->add_setting('sparkling[w2f_cfa_link]', array(
                'default' => '',
                'type' => 'option',
                'sanitize_callback' => 'esc_url_raw'
            ));
            $wp_customize->add_control('sparkling[w2f_cfa_link]', array(
                'label' => __('CFA button link', 'sparkling'),
                'section' => 'sparkling_action_options',
                'description' => __('Enter the link for Call For Action button', 'sparkling'),
                'type' => 'text'
            ));

            $wp_customize->add_setting('sparkling[cfa_color]', array(
                'default' => '',
                'type'  => 'option',
                'sanitize_callback' => 'sparkling_sanitize_hexcolor'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sparkling[cfa_color]', array(
                'label' => __('Call For Action Text Color', 'sparkling'),
                'description'   => __('Default used if no color is selected','sparkling'),
                'section' => 'sparkling_action_options',
            )));
            $wp_customize->add_setting('sparkling[cfa_bg_color]', array(
                'default' => '',
                'type'  => 'option',
                'sanitize_callback' => 'sparkling_sanitize_hexcolor'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sparkling[cfa_bg_color]', array(
                'label' => __('Call For Action Background Color', 'sparkling'),
                'description'   => __('Default used if no color is selected','sparkling'),
                'section' => 'sparkling_action_options',
            )));
            $wp_customize->add_setting('sparkling[cfa_btn_color]', array(
                'default' => '',
                'type'  => 'option',
                'sanitize_callback' => 'sparkling_sanitize_hexcolor'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sparkling[cfa_btn_color]', array(
                'label' => __('Call For Action Button Border Color', 'sparkling'),
                'description'   => __('Default used if no color is selected','sparkling'),
                'section' => 'sparkling_action_options',
            )));
            $wp_customize->add_setting('sparkling[cfa_btn_txt_color]', array(
                'default' => '',
                'type'  => 'option',
                'sanitize_callback' => 'sparkling_sanitize_hexcolor'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sparkling[cfa_btn_txt_color]', array(
                'label' => __('Call For Action Button Text Color', 'sparkling'),
                'description'   => __('Default used if no color is selected','sparkling'),
                'section' => 'sparkling_action_options',
            )));

        /* Sparkling Typography Options */
        $wp_customize->add_section('sparkling_typography_options', array(
            'title' => __('Typography', 'sparkling'),
            'priority' => 31,
            'panel' => 'sparkling_main_options'
        ));
            // Typography Defaults
            $typography_defaults = array(
                    'size'  => '14px',
                    'face'  => 'Open Sans',
                    'style' => 'normal',
                    'color' => '#6B6B6B'
            );

            // Typography Options
            global $typography_options;
            $wp_customize->add_setting('sparkling[main_body_typography][size]', array(
                'default' => $typography_defaults['size'],
                'type' => 'option',
                'sanitize_callback' => 'sparkling_sanitize_typo_size'
            ));
            $wp_customize->add_control('sparkling[main_body_typography][size]', array(
                'label' => __('Main Body Text', 'sparkling'),
                'description' => __('Used in p tags', 'sparkling'),
                'section' => 'sparkling_typography_options',
                'type'    => 'select',
                'choices'    => $typography_options['sizes']
            ));
            $wp_customize->add_setting('sparkling[main_body_typography][face]', array(
                'default' => $typography_defaults['face'],
                'type' => 'option',
                'sanitize_callback' => 'sparkling_sanitize_typo_face'
            ));
            $wp_customize->add_control('sparkling[main_body_typography][face]', array(
                'section' => 'sparkling_typography_options',
                'type'    => 'select',
                'choices'    => $typography_options['faces']
            ));
            $wp_customize->add_setting('sparkling[main_body_typography][subset]', array(
                'default' => '',
                'type' => 'option',
                'sanitize_callback' => 'sparkling_sanitize_nohtml'
            ));
            $wp_customize->add_control('sparkling[main_body_typography][subset]', array(
                'label' => __('Font susbet', 'sparkling'),
                'section' => 'sparkling_typography_options',
                'description' => __('Enter the Google fonts subset', 'sparkling'),
                'type' => 'text'
            ));
            $wp_customize->add_setting('sparkling[main_body_typography][style]', array(
                'default' => $typography_defaults['style'],
                'type' => 'option',
                'sanitize_callback' => 'sparkling_sanitize_typo_style'
            ));
            $wp_customize->add_control('sparkling[main_body_typography][style]', array(
                'section' => 'sparkling_typography_options',
                'type'    => 'select',
                'choices'    => $typography_options['styles']
            ));
            $wp_customize->add_setting('sparkling[main_body_typography][color]', array(
                'default' => '',
                'type'  => 'option',
                'sanitize_callback' => 'sparkling_sanitize_hexcolor'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sparkling[main_body_typography][color]', array(
                'section' => 'sparkling_typography_options',
            )));

            $wp_customize->add_setting('sparkling[heading_color]', array(
                'default' => '',
                'type'  => 'option',
                'sanitize_callback' => 'sparkling_sanitize_hexcolor'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sparkling[heading_color]', array(
                'label' => __('Heading Color', 'sparkling'),
                'description'   => __('Color for all headings (h1-h6)','sparkling'),
                'section' => 'sparkling_typography_options',
            )));
            $wp_customize->add_setting('sparkling[link_color]', array(
                'default' => '',
                'type'  => 'option',
                'sanitize_callback' => 'sparkling_sanitize_hexcolor'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sparkling[link_color]', array(
                'label' => __('Link Color', 'sparkling'),
                'description'   => __('Default used if no color is selected','sparkling'),
                'section' => 'sparkling_typography_options',
            )));
            $wp_customize->add_setting('sparkling[link_hover_color]', array(
                'default' => '',
                'type'  => 'option',
                'sanitize_callback' => 'sparkling_sanitize_hexcolor'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sparkling[link_hover_color]', array(
                'label' => __('Link:hover Color', 'sparkling'),
                'description'   => __('Default used if no color is selected','sparkling'),
                'section' => 'sparkling_typography_options',
            )));

        /* Sparkling Header Options */
        $wp_customize->add_section('sparkling_header_options', array(
            'title' => __('Header', 'sparkling'),
            'priority' => 31,
            'panel' => 'sparkling_main_options'
        ));

            $wp_customize->add_setting('sparkling[sticky_header]', array(
                'default' => 0,
                'type' => 'option',
                'sanitize_callback' => 'sparkling_sanitize_checkbox'
            ));
            $wp_customize->add_control('sparkling[sticky_header]', array(
                'label' => __('Sticky Header', 'sparkling'),
                'description' => sprintf(__('Check to show fixed header', 'sparkling')),
                'section' => 'sparkling_header_options',
                'type' => 'checkbox',
            ));

            $wp_customize->add_setting('sparkling[nav_bg_color]', array(
                'default' => '',
                'type'  => 'option',
                'sanitize_callback' => 'sparkling_sanitize_hexcolor'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sparkling[nav_bg_color]', array(
                'label' => __('Top nav background color', 'sparkling'),
                'description'   => __('Default used if no color is selected','sparkling'),
                'section' => 'sparkling_header_options',
            )));
            $wp_customize->add_setting('sparkling[nav_link_color]', array(
                'default' => '',
                'type'  => 'option',
                'sanitize_callback' => 'sparkling_sanitize_hexcolor'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sparkling[nav_link_color]', array(
                'label' => __('Top nav item color', 'sparkling'),
                'description'   => __('Link color','sparkling'),
                'section' => 'sparkling_header_options',
            )));

            $wp_customize->add_setting('sparkling[nav_item_hover_color]', array(
                'default' => '',
                'type'  => 'option',
                'sanitize_callback' => 'sparkling_sanitize_hexcolor'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sparkling[nav_item_hover_color]', array(
                'label' => __('Top nav item hover color', 'sparkling'),
                'description'   => __('Link:hover color','sparkling'),
                'section' => 'sparkling_header_options',
            )));

            $wp_customize->add_setting('sparkling[nav_dropdown_bg]', array(
                'default' => '',
                'type'  => 'option',
                'sanitize_callback' => 'sparkling_sanitize_hexcolor'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sparkling[nav_dropdown_bg]', array(
                'label' => __('Top nav dropdown background color', 'sparkling'),
                'description'   => __('Background of dropdown item hover color','sparkling'),
                'section' => 'sparkling_header_options',
            )));

            $wp_customize->add_setting('sparkling[nav_dropdown_item]', array(
                'default' => '',
                'type'  => 'option',
                'sanitize_callback' => 'sparkling_sanitize_hexcolor'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sparkling[nav_dropdown_item]', array(
                'label' => __('Top nav dropdown item color', 'sparkling'),
                'description'   => __('Dropdown item color','sparkling'),
                'section' => 'sparkling_header_options',
            )));

            $wp_customize->add_setting('sparkling[nav_dropdown_item_hover]', array(
                'default' => '',
                'type'  => 'option',
                'sanitize_callback' => 'sparkling_sanitize_hexcolor'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sparkling[nav_dropdown_item_hover]', array(
                'label' => __('Top nav dropdown item hover color', 'sparkling'),
                'description'   => __('Dropdown item hover color','sparkling'),
                'section' => 'sparkling_header_options',
            )));

            $wp_customize->add_setting('sparkling[nav_dropdown_bg_hover]', array(
                'default' => '',
                'type'  => 'option',
                'sanitize_callback' => 'sparkling_sanitize_hexcolor'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sparkling[nav_dropdown_bg_hover]', array(
                'label' => __('Top nav dropdown item background hover color', 'sparkling'),
                'description'   => __('Background of dropdown item hover color','sparkling'),
                'section' => 'sparkling_header_options',
            )));

        /* Sparkling Footer Options */
        $wp_customize->add_section('sparkling_footer_options', array(
            'title' => __('Footer', 'sparkling'),
            'priority' => 31,
            'panel' => 'sparkling_main_options'
        ));
            $wp_customize->add_setting('sparkling[footer_widget_bg_color]', array(
                'default' => '',
                'type'  => 'option',
                'sanitize_callback' => 'sparkling_sanitize_hexcolor'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sparkling[footer_widget_bg_color]', array(
                'label' => __('Footer widget area background color', 'sparkling'),
                'section' => 'sparkling_footer_options',
            )));

            $wp_customize->add_setting('sparkling[footer_bg_color]', array(
                'default' => '',
                'type'  => 'option',
                'sanitize_callback' => 'sparkling_sanitize_hexcolor'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sparkling[footer_bg_color]', array(
                'label' => __('Footer background color', 'sparkling'),
                'section' => 'sparkling_footer_options',
            )));

            $wp_customize->add_setting('sparkling[footer_text_color]', array(
                'default' => '',
                'type'  => 'option',
                'sanitize_callback' => 'sparkling_sanitize_hexcolor'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sparkling[footer_text_color]', array(
                'label' => __('Footer text color', 'sparkling'),
                'section' => 'sparkling_footer_options',
            )));

            $wp_customize->add_setting('sparkling[footer_link_color]', array(
                'default' => '',
                'type'  => 'option',
                'sanitize_callback' => 'sparkling_sanitize_hexcolor'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sparkling[footer_link_color]', array(
                'label' => __('Footer link color', 'sparkling'),
                'section' => 'sparkling_footer_options',
            )));

            $wp_customize->add_setting('sparkling[custom_footer_text]', array(
                'default' => '',
                'type' => 'option',
                'sanitize_callback' => 'sparkling_sanitize_strip_slashes'
            ));
            $wp_customize->add_control('sparkling[custom_footer_text]', array(
                'label' => __('Footer information', 'sparkling'),
                'description' => sprintf(__('Copyright text in footer', 'sparkling')),
                'section' => 'sparkling_footer_options',
                'type' => 'textarea'
            ));

        /* Sparkling Social Options */
        $wp_customize->add_section('sparkling_social_options', array(
            'title' => __('Social', 'sparkling'),
            'priority' => 31,
            'panel' => 'sparkling_main_options'
        ));
            $wp_customize->add_setting('sparkling[social_color]', array(
                'default' => '',
                'type'  => 'option',
                'sanitize_callback' => 'sparkling_sanitize_hexcolor'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sparkling[social_color]', array(
                'label' => __('Social icon color', 'sparkling'),
                'description' => sprintf(__('Default used if no color is selected', 'sparkling')),
                'section' => 'sparkling_social_options',
            )));

            $wp_customize->add_setting('sparkling[social_footer_color]', array(
                'default' => '',
                'type'  => 'option',
                'sanitize_callback' => 'sparkling_sanitize_hexcolor'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sparkling[social_footer_color]', array(
                'label' => __('Footer social icon color', 'sparkling'),
                'description' => sprintf(__('Default used if no color is selected', 'sparkling')),
                'section' => 'sparkling_social_options',
            )));

            $wp_customize->add_setting('sparkling[footer_social]', array(
                'default' => 0,
                'type' => 'option',
                'sanitize_callback' => 'sparkling_sanitize_checkbox'
            ));
            $wp_customize->add_control('sparkling[footer_social]', array(
                'label' => __('Footer Social Icons', 'sparkling'),
                'description' => sprintf(__('Check to show social icons in footer', 'sparkling')),
                'section' => 'sparkling_social_options',
                'type' => 'checkbox',
            ));

        /* Sparkling Other Options */
        $wp_customize->add_section('sparkling_other_options', array(
            'title' => __('Other', 'sparkling'),
            'priority' => 31,
            'panel' => 'sparkling_main_options'
        ));
            $wp_customize->add_setting('sparkling[custom_css]', array(
                'default' => '',
                'type' => 'option',
                'sanitize_callback' => 'sparkling_sanitize_textarea'
            ));
            $wp_customize->add_control('sparkling[custom_css]', array(
                'label' => __('Custom CSS', 'sparkling'),
                'description' => sprintf(__('Additional CSS', 'sparkling')),
                'section' => 'sparkling_other_options',
                'type' => 'textarea'
            ));

        $wp_customize->add_section('sparkling_important_links', array(
            'priority' => 5,
            'title' => __('Support and Documentation', 'sparkling')
        ));
            $wp_customize->add_setting('sparkling[imp_links]', array(
              'sanitize_callback' => 'esc_url_raw'
            ));
            $wp_customize->add_control(
            new Sparkling_Important_Links(
            $wp_customize,
                'sparkling[imp_links]', array(
                'section' => 'sparkling_important_links',
                'type' => 'sparkling-important-links'
            )));

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
 * Adds sanitization callback function: colors
 * @package Sparkling
 */
function sparkling_sanitize_hexcolor($color) {
    if ($unhashed = sanitize_hex_color_no_hash($color))
        return '#' . $unhashed;
    return $color;
}

/**
 * Adds sanitization callback function: Nohtml
 * @package Sparkling
 */
function sparkling_sanitize_nohtml($input) {
    return wp_filter_nohtml_kses($input);
}

/**
 * Adds sanitization callback function: Number
 * @package Sparkling
 */
function sparkling_sanitize_number($input) {
    if ( isset( $input ) && is_numeric( $input ) ) {
        return $input;
    }
}

/**
 * Adds sanitization callback function: Strip Slashes
 * @package Sparkling
 */
function sparkling_sanitize_strip_slashes($input) {
    return wp_kses_stripslashes($input);
}

/**
 * Adds sanitization callback function: Sanitize Text area
 * @package Sparkling
 */
function sparkling_sanitize_textarea($input) {
    return sanitize_text_field($input);
}

/**
 * Adds sanitization callback function: Slider Category
 * @package Sparkling
 */
function sparkling_sanitize_slidecat( $input ) {
    global $options_categories;
    if ( array_key_exists( $input, $options_categories ) ) {
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
    if ( array_key_exists( $input, $site_layout ) ) {
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
    global $typography_options, $typography_defaults;
    if ( array_key_exists( $input, $typography_options['sizes'] ) ) {
        return $input;
    } else {
        return $typography_defaults['size'];
    }
}
/**
 * Adds sanitization callback function: Typography Face
 * @package Sparkling
 */
function sparkling_sanitize_typo_face( $input ) {
    global $typography_options, $typography_defaults;
    if ( array_key_exists( $input, $typography_options['faces'] ) ) {
        return $input;
    } else {
        return $typography_defaults['face'];
    }
}
/**
 * Adds sanitization callback function: Typography Style
 * @package Sparkling
 */
function sparkling_sanitize_typo_style( $input ) {
    global $typography_options, $typography_defaults;
    if ( array_key_exists( $input, $typography_options['styles'] ) ) {
        return $input;
    } else {
        return $typography_defaults['style'];
    }
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function sparkling_customize_preview_js() {
	wp_enqueue_script( 'sparkling_customizer', get_template_directory_uri() . '/inc/js/customizer.js', array( 'customize-preview' ), '20140317', true );
}
add_action( 'customize_preview_init', 'sparkling_customize_preview_js' );

/**
 * Add CSS for custom controls
 */
function sparkling_customizer_custom_control_css() {
	?>
    <style>
        #customize-control-sparkling-main_body_typography-size select, #customize-control-sparkling-main_body_typography-face select,#customize-control-sparkling-main_body_typography-style select { width: 60%; }
    </style><?php
}
add_action( 'customize_controls_print_styles', 'sparkling_customizer_custom_control_css' );

if ( ! class_exists( 'WP_Customize_Control' ) )
    return NULL;
/**
 * Class to create a Sparkling important links
 */
class Sparkling_Important_Links extends WP_Customize_Control {

   public $type = "sparkling-important-links";

   public function render_content() {?>
         <!-- Twitter -->
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>

        <!-- Facebook -->
        <div id="fb-root"></div>
        <script>
            (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=328285627269392";
            fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>

        <div class="inside">
            <div id="social-share">
              <div class="fb-like" data-href="<?php echo esc_url( 'https://www.facebook.com/colorlib' ); ?>" data-send="false" data-layout="button_count" data-width="90" data-show-faces="true"></div>
              <div class="tw-follow" ><a href="https://twitter.com/colorlib" class="twitter-follow-button" data-show-count="false">Follow @colorlib</a></div>
            </div>
            <p><b><a href="<?php echo esc_url( 'http://colorlib.com/wp/support/sparkling' ); ?>"><?php esc_html_e('Sparkling Documentation','sparkling'); ?></a></b></p>
            <p><?php _e('The best way to contact us with <b>support questions</b> and <b>bug reports</b> is via','sparkling') ?> <a href="<?php echo esc_url( 'http://colorlib.com/wp/forums' ); ?>"><?php esc_html_e('Colorlib support forum','sparkling') ?></a>.</p>
            <p><?php esc_html_e('If you like this theme, I\'d appreciate any of the following:','sparkling') ?></p>
            <ul>
              <li><a class="button" href="<?php echo esc_url( 'http://wordpress.org/support/view/theme-reviews/sparkling?filter=5' ); ?>" title="<?php esc_attr_e('Rate this Theme', 'sparkling'); ?>" target="_blank"><?php printf(esc_html__('Rate this Theme','sparkling')); ?></a></li>
              <li><a class="button" href="<?php echo esc_url( 'http://www.facebook.com/colorlib' ); ?>" title="Like Colorlib on Facebook" target="_blank"><?php printf(esc_html__('Like on Facebook','sparkling')); ?></a></li>
              <li><a class="button" href="<?php echo esc_url( 'http://twitter.com/colorlib/' ); ?>" title="Follow Colrolib on Twitter" target="_blank"><?php printf(esc_html__('Follow on Twitter','sparkling')); ?></a></li>
            </ul>
        </div><?php
   }

}

/*
 * Custom Scripts
 */
add_action( 'customize_controls_print_footer_scripts', 'customizer_custom_scripts' );

function customizer_custom_scripts() { ?>
<script type="text/javascript">
    jQuery(document).ready(function() {
        /* This one shows/hides the an option when a checkbox is clicked. */
        jQuery('#customize-control-sparkling-sparkling_slide_categories, #customize-control-sparkling-sparkling_slide_number').hide();
        jQuery('#customize-control-sparkling-sparkling_slider_checkbox input').click(function() {
            jQuery('#customize-control-sparkling-sparkling_slide_categories, #customize-control-sparkling-sparkling_slide_number').fadeToggle(400);
        });

        if (jQuery('#customize-control-sparkling-sparkling_slider_checkbox input:checked').val() !== undefined) {
            jQuery('#customize-control-sparkling-sparkling_slide_categories, #customize-control-sparkling-sparkling_slide_number').show();
        }
    });
</script>
<style>
    li#accordion-section-sparkling_important_links h3.accordion-section-title, li#accordion-section-sparkling_important_links h3.accordion-section-title:focus { background-color: #00cc00 !important; color: #fff !important; }
    li#accordion-section-sparkling_important_links h3.accordion-section-title:hover { background-color: #00b200 !important; color: #fff !important; }
    li#accordion-section-sparkling_important_links h3.accordion-section-title:after { color: #fff !important; }
</style>
<?php
}
