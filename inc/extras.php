<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package sparkling
 */

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * @param array $args Configuration arguments.
 *
 * @return array
 */
function sparkling_page_menu_args( $args ) {
	$args['show_home'] = true;

	return $args;
}

add_filter( 'wp_page_menu_args', 'sparkling_page_menu_args' );

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 *
 * @return array
 */
function sparkling_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	return $classes;
}

add_filter( 'body_class', 'sparkling_body_classes' );


// Mark Posts/Pages as Untiled when no title is used
add_filter( 'the_title', 'sparkling_title' );

function sparkling_title( $title ) {
	if ( '' === (string) $title ) {
		return __( 'Untitled', 'sparkling' );
	} else {
		return $title;
	}
}

/**
 * Password protected post form using Bootstrap classes.
 *
 * Kept under its original name so child themes that remove the filter still
 * can. It used to discard core's form entirely, which lost the redirect back
 * to the post and the "Invalid password" message WordPress 6.8 added, and it
 * pre-filled the password field with the current search query, posted to a
 * URL built from the siteurl option instead of site_url(), and reused the
 * search form's id="searchsubmit".
 *
 * @param string  $output           Core's form markup (unused).
 * @param WP_Post $post             Post being displayed. WordPress 5.8+.
 * @param string  $invalid_password Error message after a wrong password. WordPress 6.8+.
 *
 * @return string
 */
function custom_password_form( $output = '', $post = null, $invalid_password = '' ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- Public name since 1.0; child themes remove_filter() it.
	$post     = get_post( $post );
	$field_id = 'pwbox-' . ( empty( $post->ID ) ? wp_rand() : $post->ID );
	$error    = '';
	$describe = '';

	if ( '' !== (string) $invalid_password ) {
		$error    = '<div class="alert alert-danger post-password-form-invalid-password" role="alert"><p id="error-' . esc_attr( $field_id ) . '">' . esc_html( $invalid_password ) . '</p></div>';
		$describe = ' aria-describedby="error-' . esc_attr( $field_id ) . '"';
	}

	$redirect = empty( $post->ID ) ? '' : '<input type="hidden" name="redirect_to" value="' . esc_attr( get_permalink( $post->ID ) ) . '" />';

	/*
	 * One line, with every inline element already inside a block, like core's
	 * own form: this is returned in place of the post content, so the_content's
	 * wpautop() runs over it and would otherwise add <p> and <br> tags.
	 */
	return '<form class="protected-post-form post-password-form" action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post">'
		. '<div class="row"><div class="col-lg-10">'
		. $error
		. '<p>' . esc_html__( 'This post is password protected. To view it please enter your password below:', 'sparkling' ) . '</p>'
		. '<p><label for="' . esc_attr( $field_id ) . '">' . esc_html__( 'Password:', 'sparkling' ) . ' </label>' . $redirect . '</p>'
		. '<div class="input-group">'
		. '<input class="form-control" name="post_password" id="' . esc_attr( $field_id ) . '" type="password" spellcheck="false" required' . $describe . ' />'
		. '<span class="input-group-btn"><button type="submit" class="btn btn-default">' . esc_html__( 'Submit', 'sparkling' ) . '</button></span>'
		. '</div>'
		. '</div></div></form>';
}
add_filter( 'the_password_form', 'custom_password_form', 10, 3 );

// Add Bootstrap classes for table
add_filter( 'the_content', 'sparkling_add_custom_table_class' );
function sparkling_add_custom_table_class( $content ) {
	return preg_replace( '/(<table) ?(([^>]*)class="([^"]*)")?/', '$1 $3 class="$4 table table-hover" ', $content );
}


if ( ! function_exists( 'sparkling_social_icons' ) ) :

	/**
	 * Display social links in footer and widgets
	 *
	 * @package sparkling
	 */
	function sparkling_social_icons() {
		static $sparkling_instance = 0;

		if ( has_nav_menu( 'social-menu' ) ) {
			++$sparkling_instance;

			/*
			 * The menu can print several times on one page -- the footer and the
			 * Sparkling Social widget, in any number of widget areas -- and every
			 * copy used to carry id="menu-social". The first keeps that id for
			 * child themes that style it; the rest are numbered. The stylesheet
			 * matches the .menu-social class at the same specificity.
			 */
			$sparkling_suffix = 1 === $sparkling_instance ? '' : '-' . $sparkling_instance;

			wp_nav_menu(
				array(
					'theme_location'       => 'social-menu',
					'container'            => 'nav',
					'container_id'         => 'menu-social' . $sparkling_suffix,
					'container_class'      => 'social-icons menu-social',
					'container_aria_label' => __( 'Social links', 'sparkling' ),
					'menu_id'              => 'menu-social-items' . $sparkling_suffix,
					'menu_class'           => 'social-menu',
					'depth'                => 1,
					'fallback_cb'          => '',
					'link_before'          => '<i class="social_icon"><span>',
					'link_after'           => '</span></i>',
				)
			);
		}
	}
endif;

if ( ! function_exists( 'sparkling_header_menu' ) ) :
	/**
	 * Header menu (should you choose to use one)
	 */
	function sparkling_header_menu() {
		// display the WordPress Custom Menu if available
		wp_nav_menu(
			array(
				'menu'            => 'primary',
				'theme_location'  => 'primary',
				'container'       => 'div',
				'container_id'    => 'sparkling-navbar',
				'container_class' => 'collapse navbar-collapse navbar-ex1-collapse',
				'menu_class'      => 'nav navbar-nav',
				'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
				'walker'          => new WP_Bootstrap_Navwalker(),
			)
		);
	} /* end header menu */
endif;

if ( ! function_exists( 'sparkling_footer_links' ) ) :
	/**
	 * Footer menu (should you choose to use one)
	 */
	function sparkling_footer_links() {
		// display the WordPress Custom Menu if available
		wp_nav_menu(
			array(
				'container'       => '',                              // remove nav container
				'container_class' => 'footer-links clearfix',   // class of container (should you choose to use it)
				'menu'            => esc_html__( 'Footer Links', 'sparkling' ),   // nav name
				'menu_class'      => 'nav footer-nav clearfix',      // adding custom nav class
				'theme_location'  => 'footer-links',             // where it's located in the theme
				'before'          => '',                                 // before the menu
				'after'           => '',                                  // after the menu
				'link_before'     => '',                            // before each link
				'link_after'      => '',                             // after each link
				'fallback_cb'     => 'sparkling_footer_links_fallback', // fallback function
			)
		);
	} /* end sparkling footer link */
endif;


if ( ! function_exists( 'sparkling_call_for_action' ) ) :
	/**
	 * Call for action text and button displayed above content
	 */
	function sparkling_call_for_action() {
		if ( is_front_page() && of_get_option( 'w2f_cfa_text' ) != '' ) {
			echo '<div class="cfa">';
			echo '<div class="container">';
			echo '<div class="col-sm-8">';
			echo '<span class="cfa-text">' . esc_html( of_get_option( 'w2f_cfa_text' ) ) . '</span>';
			echo '</div>';
			echo '<div class="col-sm-4">';
			echo '<a class="btn btn-lg cfa-button" href="' . esc_url( of_get_option( 'w2f_cfa_link' ) ) . '">' . esc_html( of_get_option( 'w2f_cfa_button' ) ) . '</a>';
			echo '</div>';
			echo '</div>';
			echo '</div>';
		}
	}
endif;

if ( ! function_exists( 'sparkling_featured_slider' ) ) :
	/**
	 * Featured image slider, displayed on front page for static page and blog
	 */
	function sparkling_featured_slider() {
		if ( is_front_page() && of_get_option( 'sparkling_slider_checkbox' ) == 1 ) {

			$count    = absint( of_get_option( 'sparkling_slide_number' ) );
			$slidecat = of_get_option( 'sparkling_slide_categories' );

			$query = new WP_Query(
				array(
					'cat'                 => $slidecat,
					'posts_per_page'      => $count ? $count : 5,
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
					'meta_query'          => array(
						array(
							'key'     => '_thumbnail_id',
							'compare' => 'EXISTS',
						),
					),
				)
			);
			/*
			 * Only open the slider markup once we know there is something to put in
			 * it. With the slider enabled but no post in the chosen category carrying
			 * a featured image, this used to print an empty
			 * <div class="flexslider"><ul class="slides"></ul></div> that FlexSlider
			 * could not initialise.
			 */
			if ( $query->have_posts() ) :
				echo '<div class="flexslider">';
				echo '<ul class="slides">';

				while ( $query->have_posts() ) :
					$query->the_post();

					// Whether each slide is wrapped in a link is an option, so track
					// it and close the same tag we opened. Previously '</a>' was
					// printed unconditionally, leaving a stray closing tag whenever
					// slide links were switched off.
					$sparkling_slide_linked = ( 1 == of_get_option( 'sparkling_slider_link_checkbox', 1 ) );

					if ( $sparkling_slide_linked ) {
						echo '<li><a href="' . esc_url( get_permalink() ) . '">';
					} else {
						echo '<li>';
					}

					if ( has_post_thumbnail() ) :
						if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'photon' ) && function_exists( 'jetpack_photon_url' ) ) {
							$feat_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );

							// wp_get_attachment_image_src() returns false for a missing
							// attachment. Indexing that raised "Trying to access array
							// offset on false" and passed null to jetpack_photon_url(),
							// emitting an <img> with an empty src.
							if ( ! empty( $feat_image_url[0] ) ) {
								$photon_url = jetpack_photon_url(
									$feat_image_url[0],
									array( 'resize' => '1920,550' )
								);
								echo '<img src="' . esc_url( $photon_url ) . '" alt="' . esc_attr( get_the_title() ) . '">';
							}
						} else {
							/*
							 * 'activello-slider' is a size from a different theme and was
							 * never registered here, so WordPress silently fell back to
							 * 'full'. Ask for 'full' explicitly rather than substituting a
							 * fixed size: the slider is full-bleed, and flexslider.css
							 * renders slide images at their natural width (a later
							 * width:auto rule overrides its own width:100%), so a fixed
							 * size narrower than the viewport is centred with gaps either
							 * side. WordPress still attaches srcset/sizes here, so browsers
							 * download an appropriately sized candidate.
							 */
							echo get_the_post_thumbnail( get_the_ID(), 'full' );
						}
					endif;

					echo '<div class="flex-caption">';
					if ( get_the_title() != '' ) {
						echo '<h2 class="entry-title">' . esc_html( get_the_title() ) . '</h2>';
					}
					if ( get_the_excerpt() != '' ) {
						echo '<div class="excerpt">' . esc_html( get_the_excerpt() ) . '</div>';
					}
					echo '</div>';
					echo $sparkling_slide_linked ? '</a></li>' : '</li>';
				endwhile;

				echo '</ul>';
				echo ' </div>';
			endif;
			wp_reset_postdata();
		}// End if().
	}
endif;

/**
 * function to show the footer info, copyright information
 */
function sparkling_footer_info() {
	/* translators: 1: Colorlib link, 2: WordPress link */
	printf( esc_html__( 'Theme by %1$s Powered by %2$s', 'sparkling' ), '<a href="https://colorlib.com/" target="_blank" rel="nofollow noopener">Colorlib</a>', '<a href="https://wordpress.org/" target="_blank" rel="noopener">WordPress</a>' );
}


if ( ! function_exists( 'sparkling_css_color' ) ) {
	/**
	 * Return a theme option as a CSS colour that is safe to print, or ''.
	 *
	 * Every value printed into the inline <style> block goes through this. Options
	 * saved before the sanitiser was tightened may still hold arbitrary text, so
	 * the value is re-validated at output time rather than trusted from storage.
	 *
	 * @param string $name Option name.
	 * @return string Validated colour, or '' when the stored value is not one.
	 */
	function sparkling_css_color( $name ) {
		$value = of_get_option( $name );

		if ( ! is_string( $value ) ) {
			return '';
		}

		$value = trim( $value );

		if ( '' === $value ) {
			return '';
		}

		// #rgb / #rrggbb
		if ( preg_match( '/^#(?:[0-9a-f]{3}|[0-9a-f]{6})$/i', $value ) ) {
			return $value;
		}

		// rgb() / rgba()
		if ( preg_match( '/^rgba?\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*(?:,\s*(?:0|1|0?\.\d+)\s*)?\)$/', $value ) ) {
			return $value;
		}

		// Bare CSS colour keyword, e.g. "transparent" or "red".
		if ( preg_match( '/^[a-z]{3,20}$/i', $value ) ) {
			return $value;
		}

		return '';
	}
}

if ( ! function_exists( 'get_sparkling_theme_options' ) ) {
	/**
	 * Get information from Theme Options and add it into wp_head
	 */
	function get_sparkling_theme_options() {

		echo '<style type="text/css">';

		if ( sparkling_css_color( 'link_color' ) ) {
			echo 'a, #infinite-handle span, #secondary .widget .post-content a, .entry-meta a {color:' . sparkling_css_color( 'link_color' ) . '}';
		}
		if ( sparkling_css_color( 'link_hover_color' ) ) {
			echo 'a:hover, a:focus, a:active, #secondary .widget .post-content a:hover, #secondary .widget .post-content a:focus, .woocommerce nav.woocommerce-pagination ul li a:focus, .woocommerce nav.woocommerce-pagination ul li a:hover, .woocommerce nav.woocommerce-pagination ul li span.current, #secondary .widget a:hover, #secondary .widget a:focus {color: ' . sparkling_css_color( 'link_hover_color' ) . ';}';
		}
		if ( sparkling_css_color( 'element_color' ) ) {
			echo '.btn-default, .label-default, .flex-caption h2, .btn.btn-default.read-more,button,
              .navigation .wp-pagenavi-pagination span.current,.navigation .wp-pagenavi-pagination a:hover,
              .woocommerce a.button, .woocommerce button.button,
              .woocommerce input.button, .woocommerce #respond input#submit.alt,
              .woocommerce a.button, .woocommerce button.button,
              .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt { background-color: ' . sparkling_css_color( 'element_color' ) . '; border-color: ' . sparkling_css_color( 'element_color' ) . ';}';

			echo '.site-main [class*="navigation"] a, .more-link, .pagination>li>a, .pagination>li>span, .cfa-button { color: ' . sparkling_css_color( 'element_color' ) . '}';
			echo '.cfa-button {border-color: ' . sparkling_css_color( 'element_color' ) . ';}';
		}

		if ( sparkling_css_color( 'element_color_hover' ) ) {
			echo '.btn-default:hover, .btn-default:focus,.label-default[href]:hover, .label-default[href]:focus, .tagcloud a:hover, .tagcloud a:focus, button, .main-content [class*="navigation"] a:hover, .main-content [class*="navigation"] a:focus, #infinite-handle span:hover, #infinite-handle span:focus-within, .btn.btn-default.read-more:hover, .btn.btn-default.read-more:focus, .btn-default:hover, .btn-default:focus, .scroll-to-top:hover, .scroll-to-top:focus, .btn-default:active, .btn-default.active, .site-main [class*="navigation"] a:hover, .site-main [class*="navigation"] a:focus, .more-link:hover, .more-link:focus, #image-navigation .nav-previous a:hover, #image-navigation .nav-previous a:focus, #image-navigation .nav-next a:hover, #image-navigation .nav-next a:focus, .cfa-button:hover, .cfa-button:focus, .woocommerce a.button:hover, .woocommerce a.button:focus, .woocommerce button.button:hover, .woocommerce button.button:focus, .woocommerce input.button:hover, .woocommerce input.button:focus, .woocommerce #respond input#submit.alt:hover, .woocommerce #respond input#submit.alt:focus, .woocommerce a.button:hover, .woocommerce a.button:focus, .woocommerce button.button:hover, .woocommerce button.button:focus, .woocommerce input.button:hover, .woocommerce input.button:focus, .woocommerce a.button.alt:hover, .woocommerce a.button.alt:focus, .woocommerce button.button.alt:hover, .woocommerce button.button.alt:focus, .woocommerce input.button.alt:hover, .woocommerce input.button.alt:focus, a:hover .flex-caption h2 { background-color: ' . sparkling_css_color( 'element_color_hover' ) . '; border-color: ' . sparkling_css_color( 'element_color_hover' ) . '; }';
		}
		if ( sparkling_css_color( 'element_color_hover' ) ) {
			echo '.pagination>li>a:focus, .pagination>li>a:hover, .pagination>li>span:focus-within, .pagination>li>span:hover {color: ' . sparkling_css_color( 'element_color_hover' ) . ';}';
		}
		if ( sparkling_css_color( 'cfa_bg_color' ) ) {
			echo '.cfa { background-color: ' . sparkling_css_color( 'cfa_bg_color' ) . '; } .cfa-button:hover a {color: ' . sparkling_css_color( 'cfa_bg_color' ) . ';}';
		}
		if ( sparkling_css_color( 'cfa_color' ) ) {
			echo '.cfa-text { color: ' . sparkling_css_color( 'cfa_color' ) . ';}';
		}
		// Each declaration only when its colour is set: with one of the two empty
		// this printed "border-color: ;", an invalid declaration.
		if ( sparkling_css_color( 'cfa_btn_color' ) ) {
			echo '.cfa-button {border-color: ' . sparkling_css_color( 'cfa_btn_color' ) . ';}';
		}
		if ( sparkling_css_color( 'cfa_btn_txt_color' ) ) {
			echo '.cfa-button {color: ' . sparkling_css_color( 'cfa_btn_txt_color' ) . ';}';
		}
		if ( sparkling_css_color( 'heading_color' ) ) {
			echo 'h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6, .entry-title, .entry-title a {color: ' . sparkling_css_color( 'heading_color' ) . ';}';
		}
		if ( sparkling_css_color( 'nav_bg_color' ) ) {
			echo '.navbar.navbar-default, .navbar-default .navbar-nav .open .dropdown-menu > li > a {background-color: ' . sparkling_css_color( 'nav_bg_color' ) . ';}';
		}
		if ( sparkling_css_color( 'nav_link_color' ) ) {
			echo '.navbar-default .navbar-nav > li > a, .navbar-default .navbar-nav.sparkling-mobile-menu > li:hover > a, .navbar-default .navbar-nav.sparkling-mobile-menu > li:hover > .caret, .navbar-default .navbar-nav > li, .navbar-default .navbar-nav > .open > a, .navbar-default .navbar-nav > .open > a:hover, .navbar-default .navbar-nav > .open > a:focus { color: ' . sparkling_css_color( 'nav_link_color' ) . ';}';
			echo '@media (max-width: 767px){ .navbar-default .navbar-nav > li:hover > a, .navbar-default .navbar-nav > li:hover > .caret{ color: ' . sparkling_css_color( 'nav_link_color' ) . '!important ;} }';
		}
		if ( sparkling_css_color( 'nav_item_hover_color' ) ) {
			echo '.navbar-default .navbar-nav > li:hover > a, .navbar-default .navbar-nav > li:focus-within > a, .navbar-nav > li:hover > .caret, .navbar-nav > li:focus-within > .caret, .navbar-default .navbar-nav.sparkling-mobile-menu > li.open > a, .navbar-default .navbar-nav.sparkling-mobile-menu > li.open > .caret, .navbar-default .navbar-nav > li:hover, .navbar-default .navbar-nav > li:focus-within, .navbar-default .navbar-nav > .active > a, .navbar-default .navbar-nav > .active > .caret, .navbar-default .navbar-nav > .active > a:hover, .navbar-default .navbar-nav > .active > a:focus, .navbar-default .navbar-nav > li > a:hover, .navbar-default .navbar-nav > li > a:focus, .navbar-default .navbar-nav > .open > a, .navbar-default .navbar-nav > .open > a:hover, .navbar-default .navbar-nav > .open > a:focus {color: ' . sparkling_css_color( 'nav_item_hover_color' ) . ';}';
			echo '@media (max-width: 767px){ .navbar-default .navbar-nav > li.open > a, .navbar-default .navbar-nav > li.open > .caret { color: ' . sparkling_css_color( 'nav_item_hover_color' ) . ' !important; } }';
		}
		if ( sparkling_css_color( 'nav_dropdown_bg' ) ) {
			echo '.dropdown-menu {background-color: ' . sparkling_css_color( 'nav_dropdown_bg' ) . ';}';
		}
		if ( sparkling_css_color( 'nav_dropdown_item' ) ) {
			echo '.navbar-default .navbar-nav .open .dropdown-menu > li > a, .dropdown-menu > li > a, .dropdown-menu > li > .caret { color: ' . sparkling_css_color( 'nav_dropdown_item' ) . ';}';
		}

		if ( sparkling_css_color( 'nav_dropdown_bg_hover' ) ) {
			echo '.navbar-default .navbar-nav .dropdown-menu > li:hover, .navbar-default .navbar-nav .dropdown-menu > li:focus-within, .dropdown-menu > .active {background-color: ' . sparkling_css_color( 'nav_dropdown_bg_hover' ) . ';}';
			echo '@media (max-width: 767px) {.navbar-default .navbar-nav .dropdown-menu > li:hover, .navbar-default .navbar-nav .dropdown-menu > li:focus, .dropdown-menu > .active {background: transparent;} }';
		}
		if ( sparkling_css_color( 'nav_dropdown_item_hover' ) ) {
			echo '.dropdown-menu>.active>a, .dropdown-menu>.active>a:focus, .dropdown-menu>.active>a:hover, .dropdown-menu>.active>.caret, .dropdown-menu>li>a:focus, .dropdown-menu>li>a:hover, .dropdown-menu>li:hover>a, .dropdown-menu>li:hover>.caret {color:' . sparkling_css_color( 'nav_dropdown_item_hover' ) . ';}';
			echo '@media (max-width: 767px) {.navbar-default .navbar-nav .open .dropdown-menu > .active > a, .navbar-default .navbar-nav .dropdown-menu > li.active > .caret, .navbar-default .navbar-nav .dropdown-menu > li.open > a, .navbar-default .navbar-nav li.open > a, .navbar-default .navbar-nav li.open > .caret {color:' . sparkling_css_color( 'nav_dropdown_item_hover' ) . ';} }';
		}

		if ( sparkling_css_color( 'nav_dropdown_item_hover' ) ) {
			echo '.navbar-default .navbar-nav .current-menu-ancestor a.dropdown-toggle { color: ' . sparkling_css_color( 'nav_dropdown_item_hover' ) . ';}';
		}
		if ( sparkling_css_color( 'footer_bg_color' ) ) {
			echo '#colophon {background-color: ' . sparkling_css_color( 'footer_bg_color' ) . ';}';
		}
		if ( sparkling_css_color( 'footer_text_color' ) ) {
			echo '#footer-area, .site-info, .site-info caption, #footer-area caption {color: ' . sparkling_css_color( 'footer_text_color' ) . ';}';
		}
		if ( sparkling_css_color( 'footer_widget_bg_color' ) ) {
			echo '#footer-area {background-color: ' . sparkling_css_color( 'footer_widget_bg_color' ) . ';}';
		}
		if ( sparkling_css_color( 'footer_link_color' ) ) {
			echo '.site-info a, #footer-area a {color: ' . sparkling_css_color( 'footer_link_color' ) . ';}';
		}
		if ( sparkling_css_color( 'social_color' ) ) {
			echo '.social-icons li a {background-color: ' . sparkling_css_color( 'social_color' ) . ' !important ;}';
		}
		if ( sparkling_css_color( 'social_footer_color' ) ) {
			echo '#footer-area .social-icons li a {background-color: ' . sparkling_css_color( 'social_footer_color' ) . ' !important ;}';
		}
		global $typography_options;
		$typography = of_get_option( 'main_body_typography' );
		if ( is_array( $typography ) ) {
			// Each value is checked against the whitelist it was chosen from, so a
			// hand-edited option cannot inject arbitrary text into the stylesheet.
			if ( isset( $typography['color'] ) && preg_match( '/^#(?:[0-9a-f]{3}|[0-9a-f]{6})$/i', trim( $typography['color'] ) ) ) {
				echo 'body, .entry-content {color:' . esc_attr( trim( $typography['color'] ) ) . '}';
			}
			if ( isset( $typography['face'] ) && isset( $typography_options['faces'][ $typography['face'] ] ) ) {
				echo '.entry-content {font-family: ' . esc_attr( $typography_options['faces'][ $typography['face'] ] ) . ';}';
			}
			if ( isset( $typography['size'] ) && isset( $typography_options['sizes'][ $typography['size'] ] ) ) {
				echo '.entry-content {font-size:' . esc_attr( $typography_options['sizes'][ $typography['size'] ] ) . '}';
			}
			if ( isset( $typography['style'] ) && isset( $typography_options['styles'][ $typography['style'] ] ) ) {
				echo '.entry-content {font-weight:' . esc_attr( $typography['style'] ) . '}';
			}
		}
		$sparkling_custom_css = of_get_option( 'custom_css' );
		if ( $sparkling_custom_css ) {
			/*
			 * Legacy Options Framework CSS. sparkling_setup() migrates this into
			 * core's Custom CSS and unsets it, so this only runs on sites that have
			 * not loaded an admin page since upgrading.
			 *
			 * html_entity_decode() used to run here, which turned an escaped
			 * "</style><script>" back into live markup. Strip tags instead so the
			 * value cannot break out of the <style> element.
			 */
			echo wp_strip_all_tags( $sparkling_custom_css );
		}
		echo '</style>';
	}
}// End if().
add_action( 'wp_head', 'get_sparkling_theme_options', 10 );

/**
 * Add Bootstrap thumbnail styling to images with captions
 * Use <figure> and <figcaption>
 *
 * @link https://justintadlock.com/archives/2011/07/01/captions-in-wordpress
 */
function sparkling_caption( $output, $attr, $content ) {
	if ( is_feed() ) {
		return $output;
	}

	$defaults = array(
		'id'      => '',
		'align'   => 'alignnone',
		'width'   => '',
		'caption' => '',
	);

	$attr = shortcode_atts( $defaults, $attr );

	$width = absint( $attr['width'] );

	// If the width is less than 1 or there is no caption, return the content wrapped between the [caption] tags
	if ( $width < 1 || empty( $attr['caption'] ) ) {
		return $content;
	}

	/*
	 * absint(): a non-numeric width -- "300px", say -- was added to 10 as a
	 * string, a fatal TypeError on PHP 8. The caption may hold the markup core
	 * allows in captions, and nothing else.
	 */
	$attributes  = ( ! empty( $attr['id'] ) ? ' id="' . esc_attr( $attr['id'] ) . '"' : '' );
	$attributes .= ' class="thumbnail wp-caption ' . esc_attr( $attr['align'] ) . '"';
	$attributes .= ' style="width: ' . ( $width + 10 ) . 'px"';

	$output  = '<figure' . $attributes . '>';
	$output .= do_shortcode( $content );
	$output .= '<figcaption class="caption wp-caption-text">' . wp_kses_post( $attr['caption'] ) . '</figcaption>';
	$output .= '</figure>';

	return $output;
}

add_filter( 'img_caption_shortcode', 'sparkling_caption', 10, 3 );

/**
 * Skype URI support for social media icons
 */
function sparkling_allow_skype_protocol( $protocols ) {
	$protocols[] = 'skype';

	return $protocols;
}

add_filter( 'kses_allowed_protocols', 'sparkling_allow_skype_protocol' );

/**
 * Fallback option for the old Social Icons.
 */
function sparkling_social() {
	if ( of_get_option( 'footer_social' ) ) {
		sparkling_social_icons();
	}
}

/**
 * Fallback for removed sparkling_post_nav function
 */
if ( ! function_exists( 'sparkling_post_nav' ) ) {
	function sparkling_post_nav() {
		the_post_navigation(
			array(
				'next_text'    => '<span class="post-title">%title <i class="fa-solid fa-chevron-right" aria-hidden="true"></i></span>',
				'prev_text'    => '<i class="fa-solid fa-chevron-left" aria-hidden="true"></i> <span class="post-title">%title</span>',
				'in_same_term' => true,
			)
		);
		//
	}
}

/**
 * Fallback for removed sparkling_paging_nav function
 */
if ( ! function_exists( 'sparkling_paging_nav' ) ) {
	function sparkling_paging_nav() {
		the_posts_pagination(
			array(
				'prev_text' => '<i class="fa-solid fa-chevron-left" aria-hidden="true"></i> ' . __( 'Newer posts', 'sparkling' ),
				'next_text' => __( 'Older posts', 'sparkling' ) . ' <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>',
			)
		);
	}
}

/*
 * sparkling_add_top_level_menu_url() and sparkling_make_top_level_menu_clickable()
 * were removed in 2.7.0. The walker already prints every item's URL, and the
 * footer script re-ran each top-level click through window.location -- with
 * jQuery's deprecated .click(), only on desktop user agents (wp_is_mobile(),
 * which also defeats page caching), and sending "#" items to "#".
 */

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function sparkling_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}

add_action( 'wp_head', 'sparkling_pingback_header' );
