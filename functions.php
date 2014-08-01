<?php
/**
 * Sparkling functions and definitions
 *
 * @package sparkling
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 648; /* pixels */
}

/**
 * Set the content width for full width pages with no sidebar.
 */
function sparkling_content_width() {
  if ( is_page_template( 'page-fullwidth.php' ) || is_page_template( 'front-page.php' ) ) {
    global $content_width;
    $content_width = 1008; /* pixels */
  }
}
add_action( 'template_redirect', 'sparkling_content_width' );

if ( ! function_exists( 'sparkling_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function sparkling_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 */
	load_theme_textdomain( 'sparkling', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

  add_image_size( 'sparkling-featured', 750, 410, true );
	add_image_size( 'tab-small', 60, 60 , true); // Small Thumbnail

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'sparkling' ),
		'footer-links' => __( 'Footer Links', 'sparkling' ) // secondary nav in footer
	) );

	// Enable support for Post Formats.
	add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ) );

	// Setup the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'sparkling_custom_background_args', array(
		'default-color' => 'F2F2F2',
		'default-image' => '',
	) ) );

  // Enable support for HTML5 markup.
  add_theme_support( 'html5', array(
    'comment-list',
    'search-form',
    'comment-form',
    'gallery',
    'caption',
  ) );
}
endif; // sparkling_setup
add_action( 'after_setup_theme', 'sparkling_setup' );

/**
 * Register widgetized area and update sidebar with default widgets.
 */
function sparkling_widgets_init() {
  	register_sidebar( array(
  		'name'          => __( 'Sidebar', 'sparkling' ),
  		'id'            => 'sidebar-1',
  		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
  		'after_widget'  => '</aside>',
  		'before_title'  => '<h3 class="widget-title">',
  		'after_title'   => '</h3>',
  	) );

    register_sidebar(array(
    	'id' => 'home-widget-1',
    	'name' => __( 'Homepage Widget 1', 'sparkling' ),
    	'description' => __( 'Displays on the Home Page', 'sparkling' ),
    	'before_widget' => '<div id="%1$s" class="widget %2$s">',
    	'after_widget' => '</div>',
    	'before_title' => '<h3 class="widgettitle">',
    	'after_title' => '</h3>',
    ));

    register_sidebar(array(
      'id' => 'home-widget-2',
      'name' =>  __( 'Homepage Widget 2', 'sparkling' ),
      'description' => __( 'Displays on the Home Page', 'sparkling' ),
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<h3 class="widgettitle">',
      'after_title' => '</h3>',
    ));

    register_sidebar(array(
      'id' => 'home-widget-3',
      'name' =>  __( 'Homepage Widget 3', 'sparkling' ),
      'description' =>  __( 'Displays on the Home Page', 'sparkling' ),
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<h3 class="widgettitle">',
      'after_title' => '</h3>',
    ));

    register_sidebar(array(
    	'id' => 'footer-widget-1',
    	'name' =>  __( 'Footer Widget 1', 'sparkling' ),
    	'description' =>  __( 'Used for footer widget area', 'sparkling' ),
    	'before_widget' => '<div id="%1$s" class="widget %2$s">',
    	'after_widget' => '</div>',
    	'before_title' => '<h3 class="widgettitle">',
    	'after_title' => '</h3>',
    ));

    register_sidebar(array(
      'id' => 'footer-widget-2',
      'name' =>  __( 'Footer Widget 2', 'sparkling' ),
      'description' =>  __( 'Used for footer widget area', 'sparkling' ),
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<h3 class="widgettitle">',
      'after_title' => '</h3>',
    ));

    register_sidebar(array(
      'id' => 'footer-widget-3',
      'name' =>  __( 'Footer Widget 3', 'sparkling' ),
      'description' =>  __( 'Used for footer widget area', 'sparkling' ),
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<h3 class="widgettitle">',
      'after_title' => '</h3>',
    ));

    register_widget( 'sparkling_social_widget' );
    register_widget( 'sparkling_popular_posts' );
    register_widget( 'sparkling_categories' );

}
add_action( 'widgets_init', 'sparkling_widgets_init' );



/* --------------------------------------------------------------
       Theme Widgets
-------------------------------------------------------------- */
require_once(get_template_directory() . '/inc/widgets/widget-categories.php');
require_once(get_template_directory() . '/inc/widgets/widget-social.php');
require_once(get_template_directory() . '/inc/widgets/widget-popular-posts.php');

/**
 * adding the sparkling search form (created in extra.php)
 */

add_filter( 'get_search_form', 'sparkling_wpsearch' );

/**
 * This function removes inline styles set by WordPress gallery.
 */
function sparkling_remove_gallery_css( $css ) {
  return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
}

add_filter( 'gallery_style', 'sparkling_remove_gallery_css' );

/**
 * Enqueue scripts and styles.
 */
function sparkling_scripts() {

  // Add Bootstrap default CSS
  wp_enqueue_style( 'sparkling-bootstrap', get_template_directory_uri() . '/inc/css/bootstrap.min.css' );

  // Add Font Awesome stylesheet
  wp_enqueue_style( 'sparkling-icons', get_template_directory_uri().'/inc/css/font-awesome.min.css' );

  // Add Google Fonts
  wp_register_style( 'sparkling-fonts', '//fonts.googleapis.com/css?family=Open+Sans:400italic,400,600,700&subset=latin,latin-ext|Roboto+Slab:400,300,700&subset=latin,latin-ext');

  wp_enqueue_style( 'sparkling-fonts' );

  // Add slider CSS only if is front page ans slider is enabled
  if( ( is_home() || is_front_page() ) && of_get_option('sparkling_slider_checkbox') == 1 ) {
		wp_enqueue_style( 'flexslider-css', get_template_directory_uri().'/inc/css/flexslider.css' );
  }

  // Add main theme stylesheet
	wp_enqueue_style( 'sparkling-style', get_stylesheet_uri() );

  // Add Modernizr for better HTML5 and CSS3 support
  wp_enqueue_script('sparkling-modernizr', get_template_directory_uri().'/inc/js/modernizr.min.js', array('jquery') );

  // Add Bootstrap default JS
	wp_enqueue_script('sparkling-bootstrapjs', get_template_directory_uri().'/inc/js/bootstrap.min.js', array('jquery') );

  // Add slider JS only if is front page ans slider is enabled
	if( ( is_home() || is_front_page() ) && of_get_option('sparkling_slider_checkbox') == 1 ) {
		wp_enqueue_script( 'flexslider-js', get_template_directory_uri() . '/inc/js/flexslider.min.js', array('jquery'), '20140222', true );
	}

  // Flexslider customization
  if( ( is_home() || is_front_page() ) && of_get_option('sparkling_slider_checkbox') == 1 ) {
    wp_enqueue_script( 'flexslider-customization', get_template_directory_uri() . '/inc/js/flexslider-custom.js', array('jquery', 'flexslider-js'), '20140716', true );
  }

  // Main theme related functions
	wp_enqueue_script( 'sparkling-functions', get_template_directory_uri() . '/inc/js/functions.min.js', array('jquery') );

	// This one is for accessibility
  wp_enqueue_script( 'sparkling-skip-link-focus-fix', get_template_directory_uri() . '/inc/js/skip-link-focus-fix.js', array(), '20140222', true );

  // Treaded comments
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'sparkling_scripts' );

/*
 * Loads the Options Panel
 *
 * If you're loading from a child theme use stylesheet_directory
 * instead of template_directory
 */

define('OPTIONS_FRAMEWORK_URL', get_template_directory() . '/inc/admin/');
define('OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/inc/admin/');
require_once (OPTIONS_FRAMEWORK_URL . 'options-framework.php');

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Load custom nav walker
 */
require get_template_directory() . '/inc/navwalker.php';
