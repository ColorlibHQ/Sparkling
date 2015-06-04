<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 *
 */

function optionsframework_option_name() {
	return 'sparkling';
}


/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 *
 */

function optionsframework_options() {

	// Layout options
	$site_layout = array('pull-left' => esc_html__('Right Sidebar', 'sparkling'),'pull-right' => esc_html__('Left Sidebar', 'sparkling'));

	// Test data
	$test_array = array(
		'one'   => esc_html__('One', 'options_framework_theme'),
		'two'   => esc_html__('Two', 'options_framework_theme'),
		'three' => esc_html__('Three', 'options_framework_theme'),
		'four'  => esc_html__('Four', 'options_framework_theme'),
		'five'  => esc_html__('Five', 'options_framework_theme')
	);

	// Multicheck Array
	$multicheck_array = array(
		'one'   => esc_html__('French Toast', 'options_framework_theme'),
		'two'   => esc_html__('Pancake', 'options_framework_theme'),
		'three' => esc_html__('Omelette', 'options_framework_theme'),
		'four'  => esc_html__('Crepe', 'options_framework_theme'),
		'five'  => esc_html__('Waffle', 'options_framework_theme')
	);

	// Multicheck Defaults
	$multicheck_defaults = array(
		'one'  => '1',
		'five' => '1'
	);

	// Typography Defaults
	$typography_defaults = array(
		'size'  => '14px',
		'face'  => 'Open Sans',
		'style' => 'normal',
		'color' => '#6B6B6B'
	);

	// Typography Options
	$typography_options = array(
		'sizes' => array( '6','10','12','14','15','16','18','20','24','28','32','36','42','48' ),
		'faces' => array(
			'arial'          => 'Arial',
			'verdana'        => 'Verdana, Geneva',
			'trebuchet'      => 'Trebuchet',
			'georgia'        => 'Georgia',
			'times'          => 'Times New Roman',
			'tahoma'         => 'Tahoma, Geneva',
			'Open Sans'      => 'Open Sans',
			'palatino'       => 'Palatino',
			'helvetica'      => 'Helvetica',
			'Helvetica Neue' => 'Helvetica Neue'
	),
		'styles' => array( 'normal' => 'Normal','bold' => 'Bold' ),
		'color'  => true
	);

	// $radio = array('0' => esc_html__('No', 'sparkling'),'1' => esc_html__('Yes', 'sparkling'));

	// Pull all the categories into an array
	$options_categories = array();
	$options_categories_obj = get_categories();
	foreach ($options_categories_obj as $category) {
		$options_categories[$category->cat_ID] = $category->cat_name;
	}

	// Pull all tags into an array
	$options_tags = array();
	$options_tags_obj = get_tags();
	foreach ( $options_tags_obj as $tag ) {
		$options_tags[$tag->term_id] = $tag->name;
	}

	// Pull all the pages into an array
	$options_pages = array();
	$options_pages_obj = get_pages('sort_column=post_parent,menu_order');
	$options_pages[''] = 'Select a page:';
	foreach ($options_pages_obj as $page) {
		$options_pages[$page->ID] = $page->post_title;
	}

	// If using image radio buttons, define a directory path
	$imagepath =  get_template_directory_uri() . '/images/';


	// fixed or scroll position
	$fixed_scroll = array('scroll' => 'Scroll', 'fixed' => 'Fixed');

	$options = array();

	$options[] = array(
		'name' => esc_html__('Main', 'sparkling'),
		'type' => 'heading'
	);

	$options[] = array(
		'name' => esc_html__('Do You want to display image slider on the Home Page?','sparkling'),
		'desc' => esc_html__('Check if you want to enable slider', 'sparkling'),
		'id'   => 'sparkling_slider_checkbox',
		'std'  => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'    => esc_html__('Slider Category', 'sparkling'),
		'desc'    => esc_html__('Select a category for the featured post slider', 'sparkling'),
		'id'      => 'sparkling_slide_categories',
		'type'    => 'select',
		'class'   => 'hidden',
		'options' => $options_categories
	);

	$options[] = array(
		'name'  => esc_html__('Number of slide items', 'sparkling'),
		'desc'  => esc_html__('Enter the number of slide items', 'sparkling'),
		'id'    => 'sparkling_slide_number',
		'std'   => '3',
		'class' => 'hidden',
		'type'  => 'text'
	);

	$options[] = array(
		'name'    => esc_html__('Website Layout Options', 'sparkling'),
		'desc'    => esc_html__('Choose between Left and Right sidebar options to be used as default', 'sparkling'),
		'id'      => 'site_layout',
		'std'     => 'pull-left',
		'type'    => 'select',
		'class'   => 'mini',
		'options' => $site_layout
	);

	$options[] = array(
		'name' => esc_html__('Element color', 'sparkling'),
		'desc' => esc_html__('Default used if no color is selected', 'sparkling'),
		'id'   => 'element_color',
		'std'  => '',
		'type' => 'color'
	);

	$options[] = array(
		'name' => esc_html__('Element color on hover', 'sparkling'),
		'desc' => esc_html__('Default used if no color is selected', 'sparkling'),
		'id'   => 'element_color_hover',
		'std'  => '',
		'type' => 'color'
	);

	$options[] = array(
		'name' => esc_html__('Custom Favicon', 'sparkling'),
		'desc' => esc_html__('Upload a 32px x 32px PNG/GIF image that will represent your websites favicon', 'sparkling'),
		'id'   => 'custom_favicon',
		'std'  => '',
		'type' => 'upload'
	);

	$options[] = array(
		'name' => esc_html__('Action Button', 'sparkling'),
		'type' => 'heading'
	);

	$options[] = array(
		'name' => esc_html__('Call For Action Text', 'sparkling'),
		'desc' => esc_html__('Enter the text for call for action section', 'sparkling'),
		'id'   => 'w2f_cfa_text',
		'std'  => '',
		'type' => 'textarea'
	);

	$options[] = array(
		'name' => esc_html__('Call For Action Button Title', 'sparkling'),
		'desc' => esc_html__('Enter the title for Call For Action button', 'sparkling'),
		'id'   => 'w2f_cfa_button',
		'std'  => '',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('CFA button link', 'sparkling'),
		'desc' => esc_html__('Enter the link for Call For Action button', 'sparkling'),
		'id'   => 'w2f_cfa_link',
		'std'  => '',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Call For Action Text Color', 'sparkling'),
		'desc' => esc_html__('Default used if no color is selected', 'sparkling'),
		'id'   => 'cfa_color',
		'std'  => '',
		'type' => 'color'
	);

	$options[] = array(
		'name' => esc_html__('Call For Action Background Color', 'sparkling'),
		'desc' => esc_html__('Default used if no color is selected', 'sparkling'),
		'id'   => 'cfa_bg_color',
		'std'  => '',
		'type' => 'color'
	);

	$options[] = array(
		'name' => esc_html__('Call For Action Button Border Color', 'sparkling'),
		'desc' => esc_html__('Default used if no color is selected', 'sparkling'),
		'id'   => 'cfa_btn_color',
		'std'  => '',
		'type' => 'color'
	);

	$options[] = array(
		'name' => esc_html__('Call For Action Button Text Color', 'sparkling'),
		'desc' => esc_html__('Default used if no color is selected', 'sparkling'),
		'id'   => 'cfa_btn_txt_color',
		'std'  => '',
		'type' => 'color'
	);

	$options[] = array(
		'name' => esc_html__('Typography', 'sparkling'),
		'type' => 'heading'
	);

	$options[] = array(
		'name'    => esc_html__('Main Body Text', 'sparkling'),
		'desc'    => esc_html__('Used in P tags', 'sparkling'),
		'id'      => 'main_body_typography',
		'std'     => $typography_defaults,
		'type'    => 'typography',
		'options' => $typography_options
	);

	$options[] = array(
		'name' => esc_html__('Heading Color', 'sparkling'),
		'desc' => esc_html__('Color for all headings (h1-h6)', 'sparkling'),
		'id'   => 'heading_color',
		'std'  => '',
		'type' => 'color'
	);

	$options[] = array(
		'name' => esc_html__('Link Color', 'sparkling'),
		'desc' => esc_html__('Default used if no color is selected', 'sparkling'),
		'id'   => 'link_color',
		'std'  => '',
		'type' => 'color'
	);

	$options[] = array(
		'name' => esc_html__('Link:hover Color', 'sparkling'),
		'desc' => esc_html__('Default used if no color is selected', 'sparkling'),
		'id'   => 'link_hover_color',
		'std'  => '',
		'type' => 'color'
	);

	$options[] = array(
		'name' => esc_html__('Header', 'sparkling'),
		'type' => 'heading'
	);

	$options[] = array(
		'name' => esc_html__('Top nav background color', 'sparkling'),
		'desc' => esc_html__('Default used if no color is selected', 'sparkling'),
		'id'   => 'nav_bg_color',
		'std'  => '',
		'type' => 'color'
	);

	$options[] = array(
		'name' => esc_html__('Top nav item color', 'sparkling'),
		'desc' => esc_html__('Link color', 'sparkling'),
		'id'   => 'nav_link_color',
		'std'  => '',
		'type' => 'color'
	);

	$options[] = array(
		'name' => esc_html__('Top nav item hover color', 'sparkling'),
		'desc' => esc_html__('Link:hover color', 'sparkling'),
		'id'   => 'nav_item_hover_color',
		'std'  => '',
		'type' => 'color'
	);

	$options[] = array(
		'name' => esc_html__('Top nav dropdown background color', 'sparkling'),
		'desc' => esc_html__('Background of dropdown item hover color', 'sparkling'),
		'id'   => 'nav_dropdown_bg',
		'std'  => '',
		'type' => 'color'
	);

	$options[] = array(
		'name' => esc_html__('Top nav dropdown item color', 'sparkling'),
		'desc' => esc_html__('Dropdown item color', 'sparkling'),
		'id'   => 'nav_dropdown_item',
		'std'  => '',
		'type' => 'color'
	);

	$options[] = array(
		'name' => esc_html__('Top nav dropdown item hover color', 'sparkling'),
		'desc' => esc_html__('Dropdown item hover color', 'sparkling'),
		'id'   => 'nav_dropdown_item_hover',
		'std'  => '',
		'type' => 'color'
	);

	$options[] = array(
		'name' => esc_html__('Top nav dropdown item background hover color', 'sparkling'),
		'desc' => esc_html__('Background of dropdown item hover color', 'sparkling'),
		'id'   => 'nav_dropdown_bg_hover',
		'std'  => '',
		'type' => 'color'
	);

	$options[] = array(
		'name' => esc_html__('Footer', 'sparkling'),
		'type' => 'heading'
	);

	$options[] = array(
		'name' => esc_html__('Footer widget area background color', 'sparkling'),
		'id'   => 'footer_widget_bg_color',
		'std'  => '',
		'type' => 'color'
	);

	$options[] = array(
		'name' => esc_html__('Footer background color', 'sparkling'),
		'id'   => 'footer_bg_color',
		'std'  => '',
		'type' => 'color'
	);

	$options[] = array(
		'name' => esc_html__('Footer text color', 'sparkling'),
		'id'   => 'footer_text_color',
		'std'  => '',
		'type' => 'color'
	);

	$options[] = array(
		'name' => esc_html__('Footer link color', 'sparkling'),
		'id'   => 'footer_link_color',
		'std'  => '',
		'type' => 'color'
	);

	$options[] = array(
		'name' => esc_html__('Footer information', 'sparkling'),
		'desc' => esc_html__('Copyright text in footer', 'sparkling'),
		'id'   => 'custom_footer_text',
		'std'  => '<a href="' . esc_url( home_url( '/' ) ) . '" title="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" >' . get_bloginfo( 'name', 'display' ) . '</a>  All rights reserved.',
		'type' => 'textarea'
	);

	$options[] = array(
		'name' => esc_html__('Social', 'sparkling'),
		'type' => 'heading'
	);

	$options[] = array(
		'name' => esc_html__('Social icon color', 'sparkling'),
		'desc' => esc_html__('Default used if no color is selected', 'sparkling'),
		'id'   => 'social_color',
		'std'  => '',
		'type' => 'color'
	);

	$options[] = array(
		'name' => esc_html__('Footer social icon color', 'sparkling'),
		'desc' => esc_html__('Default used if no color is selected', 'sparkling'),
		'id'   => 'social_footer_color',
		'std'  => '',
		'type' => 'color'
	);

	$options[] = array(
		'name'  => esc_html__('Add full URL for your social network profiles', 'sparkling'),
		'desc'  => esc_html__('Facebook', 'sparkling'),
		'id'    => 'social_facebook',
		'std'   => '',
		'class' => 'mini',
		'type'  => 'text'
	);

	$options[] = array(
		'id'    => 'social_twitter',
		'desc'  => esc_html__('Twitter', 'sparkling'),
		'std'   => '',
		'class' => 'mini',
		'type'  => 'text'
	);

	$options[] = array(
		'id'    => 'social_googleplus',
		'desc'  => esc_html__('Google+', 'sparkling'),
		'std'   => '',
		'class' => 'mini',
		'type'  => 'text'
	);

	$options[] = array(
		'id'    => 'social_youtube',
		'desc'  => esc_html__('Youtube', 'sparkling'),
		'std'   => '',
		'class' => 'mini',
		'type'  => 'text'
	);

	$options[] = array(
		'id'    => 'social_vimeo',
		'desc'  => esc_html__('Vimeo', 'sparkling'),
		'std'   => '',
		'class' => 'mini',
		'type'  => 'text'
	);

	$options[] = array(
		'id'    => 'social_linkedin',
		'desc'  => esc_html__('LinkedIn', 'sparkling'),
		'std'   => '',
		'class' => 'mini',
		'type'  => 'text'
	);

	$options[] = array(
		'id'    => 'social_pinterest',
		'desc'  => esc_html__('Pinterest', 'sparkling'),
		'std'   => '',
		'class' => 'mini',
		'type'  => 'text'
	);

	$options[] = array(
		'id'    => 'social_rss',
		'desc'  => esc_html__('RSS Feed', 'sparkling'),
		'std'   => '',
		'class' => 'mini',
		'type'  => 'text'
	);

	$options[] = array(
		'id'    => 'social_tumblr',
		'desc'  => esc_html__('Tumblr', 'sparkling'),
		'std'   => '',
		'class' => 'mini',
		'type'  => 'text'
	);

	$options[] = array(
		'id'    => 'social_flickr',
		'desc'  => esc_html__('Flickr', 'sparkling'),
		'std'   => '',
		'class' => 'mini',
		'type'  => 'text'
	);

	$options[] = array(
		'id'    => 'social_instagram',
		'desc'  => esc_html__('Instagram', 'sparkling'),
		'std'   => '',
		'class' => 'mini',
		'type'  => 'text'
	);

	$options[] = array(
		'id'    => 'social_dribbble',
		'desc'  => esc_html__('Dribbble', 'sparkling'),
		'std'   => '',
		'class' => 'mini',
		'type'  => 'text'
	);

	$options[] = array(
		'id'    => 'social_skype',
		'desc'  => esc_html__('Skype', 'sparkling'),
		'std'   => '',
		'class' => 'mini',
		'type'  => 'text'
	);

	$options[] = array(
		'id'    => 'social_foursquare',
		'desc'  => esc_html__('Foursquare', 'sparkling'),
		'std'   => '',
		'class' => 'mini',
		'type'  => 'text'
	);

	$options[] = array(
		'id'    => 'social_soundcloud',
		'desc'  => esc_html__('SoundCloud', 'sparkling'),
		'std'   => '',
		'class' => 'mini',
		'type'  => 'text'
	);

	$options[] = array(
		'id'    => 'social_github',
		'desc'  => esc_html__('GitHub', 'sparkling'),
		'std'   => '',
		'class' => 'mini',
		'type'  => 'text'
	);

	$options[] = array(
		'id'    => 'social_spotify',
		'desc'  => esc_html__('Spotify', 'sparkling'),
		'std'   => '',
		'class' => 'mini',
		'type'  => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Other', 'sparkling'),
		'type' => 'heading'
	);

	$options[] = array(
		'name' => esc_html__('Custom CSS', 'sparkling'),
		'desc' => esc_html__('Additional CSS', 'sparkling'),
		'id'   => 'custom_css',
		'std'  => '',
		'type' => 'textarea'
	);

	return $options;
}