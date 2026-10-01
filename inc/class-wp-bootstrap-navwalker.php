<?php

/**
 * Class Name: wp_bootstrap_navwalker
 * GitHub URI: https://github.com/twittem/wp-bootstrap-navwalker
 * Description: A custom WordPress nav walker class to implement the Bootstrap 3 navigation style in a custom theme using the WordPress built in menu manager.
 * Version: 2.0.4
 * Author: Edward McIntyre - @twittem
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( ! function_exists( 'sparkling_navwalker_is_icon_class' ) ) :
	/**
	 * Whether a menu item's Title Attribute is an icon class list.
	 *
	 * The walker prints such a value as an icon before the label, which is how
	 * sites add Font Awesome or glyphicon icons to menu items. Anything else
	 * typed there is a tooltip.
	 *
	 * @param string $value Title Attribute.
	 *
	 * @return bool
	 */
	function sparkling_navwalker_is_icon_class( $value ) {
		$tokens = preg_split( '/\s+/', trim( (string) $value ) );

		foreach ( $tokens as $token ) {
			if ( ! preg_match( '/^(glyphicon(-[\w-]+)?|fa[srbl]?|fa-[\w-]+)$/', $token ) ) {
				return false;
			}
		}

		return '' !== $tokens[0];
	}
endif;

class WP_Bootstrap_Navwalker extends Walker_Nav_Menu {

	/**
	 * @see Walker::start_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "\n$indent<ul role=\"menu\" class=\" dropdown-menu\">\n";
	}

	/**
	 * @see Walker::start_el()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param int $current_page Menu item ID.
	 * @param object $args
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		/**
		 * Dividers, Headers or Disabled
		 * =============================
		 * Determine whether the item is a Divider, Header, Disabled or regular
		 * menu item. To prevent errors we use the strcasecmp() function to so a
		 * comparison that is not case sensitive. The strcasecmp() function returns
		 * a 0 if the strings are equal.
		 */
		if ( strcasecmp( $item->attr_title, 'divider' ) == 0 && 1 === $depth ) {
			$output .= $indent . '<li role="presentation" class="divider">';
		} elseif ( strcasecmp( $item->title, 'divider' ) == 0 && 1 === $depth ) {
			$output .= $indent . '<li role="presentation" class="divider">';
		} elseif ( strcasecmp( $item->attr_title, 'dropdown-header' ) == 0 && 1 === $depth ) {
			$output .= $indent . '<li role="presentation" class="dropdown-header">' . esc_attr( $item->title );
		} elseif ( strcasecmp( $item->attr_title, 'disabled' ) == 0 ) {
			$output .= $indent . '<li role="presentation" class="disabled"><a href="#">' . esc_attr( $item->title ) . '</a>';
		} else {

			$class_names = '';
			$value       = '';

			$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes[] = 'menu-item-' . $item->ID;

			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );

			if ( $args->has_children ) {
				$class_names .= ' dropdown';
			}

			if ( in_array( 'current-menu-item', $classes ) ) {
				$class_names .= ' active';
			}

			$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

			$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args );
			$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

			$output .= $indent . '<li' . $id . $value . $class_names . '>';

			$atts = array();
			// A title only when the site owner entered one: an icon class list in
			// the Title Attribute used to be copied back as a title repeating the
			// link text, so screen readers announced the item twice.
			$atts['title']        = ! empty( $item->attr_title ) && ! sparkling_navwalker_is_icon_class( $item->attr_title ) ? $item->attr_title : '';
			$atts['target']       = ! empty( $item->target ) ? $item->target : '';
			$atts['rel']          = ! empty( $item->xfn ) ? $item->xfn : '';
			$atts['href']         = ! empty( $item->url ) ? $item->url : '';
			$atts['aria-current'] = ! empty( $item->current ) ? 'page' : '';

			$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( ! empty( $value ) ) {
					$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}

			$item_output = $args->before;

			/*
			 * Glyphicons and Font Awesome
			 * ===========================
			 * A Title Attribute that is a list of icon classes ("fa-solid fa-star",
			 * "glyphicon glyphicon-home") is printed as an icon before the label.
			 */
			$item_output .= '<a' . $attributes . '>';

			if ( ! empty( $item->attr_title ) && sparkling_navwalker_is_icon_class( $item->attr_title ) ) {
				$icon_class    = false !== strpos( $item->attr_title, 'glyphicon-' ) && false === strpos( $item->attr_title, 'glyphicon ' ) ? 'glyphicon ' . $item->attr_title : $item->attr_title;
				$item_output .= '<span class="' . esc_attr( $icon_class ) . '" aria-hidden="true"></span>&nbsp;';
			}

			$title = apply_filters( 'the_title', $item->title, $item->ID );
			// Core's filter; menu icon and badge plugins hook it.
			$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

			$item_output .= $args->link_before . $title . $args->link_after . '</a>';

			if ( $args->has_children ) {
				/*
				 * A real button. This was an empty <a href="#">: it had no name,
				 * did not say whether the submenu was open, and following it put
				 * "#" in the address bar and jumped to the top of the page. Below
				 * 1024px it is the only way to open a submenu.
				 */
				$item_output .= sprintf(
					'<button type="button" class="sparkling-dropdown caret" aria-expanded="false"><span class="screen-reader-text">%s</span></button>',
					/* translators: %s: menu item title */
					esc_html( sprintf( __( 'Show submenu for %s', 'sparkling' ), wp_strip_all_tags( $title ) ) )
				);
			}

			$item_output .= $args->after;

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}// End if().
	}

	/**
	 * Traverse elements to create list from elements.
	 *
	 * Display one element if the element doesn't have any children otherwise,
	 * display the element and its children. Will only traverse up to the max
	 * depth and no ignore elements under that depth.
	 *
	 * This method shouldn't be called directly, use the walk() method instead.
	 *
	 * @see Walker::start_el()
	 * @since 2.5.0
	 *
	 * @param object $element Data object
	 * @param array $children_elements List of elements to continue traversing.
	 * @param int $max_depth Max depth to traverse.
	 * @param int $depth Depth of current element.
	 * @param array $args
	 * @param string $output Passed by reference. Used to append additional content.
	 *
	 * @return null Null on failure with no changes to parameters.
	 */
	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
		if ( ! $element ) {
			return;
		}

		$id_field = $this->db_fields['id'];

		// Display this element.
		if ( is_object( $args[0] ) ) {
			$args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );
		}

		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}

	/**
	 * Menu Fallback
	 * =============
	 * If this function is assigned to the wp_nav_menu's fallback_cb variable
	 * and a manu has not been assigned to the theme location in the WordPress
	 * menu manager the function with display nothing to a non-logged in user,
	 * and will add a link to the WordPress menu manager if logged in as an admin.
	 *
	 * @param array $args passed from the wp_nav_menu function.
	 *
	 */
	public static function fallback( $args ) {
		if ( current_user_can( 'manage_options' ) ) {

			$fb_output = null;

			if ( $args['container'] ) {
				$fb_output = '<' . $args['container'];

				if ( $args['container_id'] ) {
					$fb_output .= ' id="' . $args['container_id'] . '"';
				}

				if ( $args['container_class'] ) {
					$fb_output .= ' class="' . $args['container_class'] . '"';
				}

				$fb_output .= '>';
			}

			$fb_output .= '<ul';

			if ( $args['menu_id'] ) {
				$fb_output .= ' id="' . $args['menu_id'] . '"';
			}

			if ( $args['menu_class'] ) {
				$fb_output .= ' class="' . $args['menu_class'] . '"';
			}

			$fb_output .= '>';
			$fb_output .= '<li><a href="' . admin_url( 'nav-menus.php' ) . '">' . __( 'Add a menu', 'sparkling' ) . '</a></li>';
			$fb_output .= '</ul>';

			if ( $args['container'] ) {
				$fb_output .= '</' . $args['container'] . '>';
			}

			echo $fb_output;
		}// End if().
	}
}
