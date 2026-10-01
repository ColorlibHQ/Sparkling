<?php
/**
 * Bootstrap 3 nav walker for the primary menu.
 *
 * Based on wp_bootstrap_navwalker 2.0.4 by Edward McIntyre (@twittem),
 * https://github.com/twittem/wp-bootstrap-navwalker, GPL-2.0+.
 *
 * @package sparkling
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Sparkling_Bootstrap_Navwalker' ) ) :

	/**
	 * Builds the primary menu in Bootstrap 3's navbar markup.
	 *
	 * This was declared as WP_Bootstrap_Navwalker until 2.7.0. That is also the
	 * class name of the walker many plugins bundle, so with such a plugin active
	 * the theme's unconditional declaration was a fatal "Cannot declare class"
	 * error. WP_Bootstrap_Navwalker remains an alias, below.
	 */
	class Sparkling_Bootstrap_Navwalker extends Walker_Nav_Menu {

		/**
		 * Starts a submenu.
		 *
		 * @see Walker::start_lvl()
		 *
		 * @param string   $output Passed by reference. Used to append additional content.
		 * @param int      $depth  Depth of menu item.
		 * @param stdClass $args   wp_nav_menu() arguments.
		 */
		public function start_lvl( &$output, $depth = 0, $args = null ) {
			$indent = str_repeat( "\t", $depth );
			// No role="menu": that promises arrow-key menu behaviour a list of links does not have.
			$output .= "\n$indent<ul class=\"dropdown-menu\">\n";
		}

		/**
		 * Starts a menu item.
		 *
		 * @see Walker::start_el()
		 *
		 * @param string   $output Passed by reference. Used to append additional content.
		 * @param WP_Post  $item   Menu item data object.
		 * @param int      $depth  Depth of menu item.
		 * @param stdClass $args   wp_nav_menu() arguments.
		 * @param int      $id     Current item ID.
		 */
		public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
			$indent     = ( $depth ) ? str_repeat( "\t", $depth ) : '';
			$attr_title = isset( $item->attr_title ) ? (string) $item->attr_title : '';
			$item_title = isset( $item->title ) ? (string) $item->title : '';

			/*
			 * Dividers, headers and disabled items are chosen by typing "divider",
			 * "dropdown-header" or "disabled" into the Title Attribute.
			 */
			if ( 1 === $depth && ( 0 === strcasecmp( $attr_title, 'divider' ) || 0 === strcasecmp( $item_title, 'divider' ) ) ) {
				$output .= $indent . '<li role="separator" class="divider">';
				return;
			}

			if ( 1 === $depth && 0 === strcasecmp( $attr_title, 'dropdown-header' ) ) {
				$output .= $indent . '<li class="dropdown-header">' . esc_html( $item_title );
				return;
			}

			if ( 0 === strcasecmp( $attr_title, 'disabled' ) ) {
				$output .= $indent . '<li class="disabled"><a aria-disabled="true">' . esc_html( $item_title ) . '</a>';
				return;
			}

			$has_children = is_object( $args ) && ! empty( $args->has_children );

			$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes[] = 'menu-item-' . $item->ID;

			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core's walker filter.
			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );

			if ( $has_children ) {
				$class_names .= ' dropdown';
			}

			if ( in_array( 'current-menu-item', $classes, true ) ) {
				$class_names .= ' active';
			}

			$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core's walker filter.
			$li_id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
			$li_id = $li_id ? ' id="' . esc_attr( $li_id ) . '"' : '';

			$output .= $indent . '<li' . $li_id . $class_names . '>';

			$is_icon = '' !== $attr_title && sparkling_navwalker_is_icon_class( $attr_title );

			$atts = array(
				// A title only when the site owner entered one: an icon class list in
				// the Title Attribute used to be copied back as a title repeating the
				// link text, so screen readers announced the item twice.
				'title'        => '' !== $attr_title && ! $is_icon ? $attr_title : '',
				'target'       => ! empty( $item->target ) ? $item->target : '',
				'rel'          => ! empty( $item->xfn ) ? $item->xfn : '',
				'href'         => ! empty( $item->url ) ? $item->url : '',
				'aria-current' => ! empty( $item->current ) ? 'page' : '',
			);

			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core's walker filter.
			$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( ! empty( $value ) ) {
					$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}

			$before      = is_object( $args ) && isset( $args->before ) ? $args->before : '';
			$after       = is_object( $args ) && isset( $args->after ) ? $args->after : '';
			$link_before = is_object( $args ) && isset( $args->link_before ) ? $args->link_before : '';
			$link_after  = is_object( $args ) && isset( $args->link_after ) ? $args->link_after : '';

			$item_output = $before . '<a' . $attributes . '>';

			/*
			 * A Title Attribute that is a list of icon classes ("fa-solid fa-star",
			 * "glyphicon glyphicon-home") is printed as an icon before the label.
			 */
			if ( $is_icon ) {
				$icon_class   = false !== strpos( $attr_title, 'glyphicon-' ) && ! preg_match( '/(^|\s)glyphicon(\s|$)/', $attr_title ) ? 'glyphicon ' . $attr_title : $attr_title;
				$item_output .= '<span class="' . esc_attr( $icon_class ) . '" aria-hidden="true"></span>&nbsp;';
			}

			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core filter.
			$title = apply_filters( 'the_title', $item_title, $item->ID );
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core's filter; menu icon and badge plugins hook it.
			$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

			$item_output .= $link_before . $title . $link_after . '</a>';

			if ( $has_children ) {
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

			$item_output .= $after;

			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core's walker filter.
			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}

		/**
		 * Traverse elements to create list from elements, flagging items with children.
		 *
		 * @see Walker::display_element()
		 *
		 * @param object $element           Data object.
		 * @param array  $children_elements List of elements to continue traversing (passed by reference).
		 * @param int    $max_depth         Max depth to traverse.
		 * @param int    $depth             Depth of current element.
		 * @param array  $args              An array of arguments.
		 * @param string $output            Used to append additional content (passed by reference).
		 */
		public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
			if ( ! $element ) {
				return;
			}

			$id_field = $this->db_fields['id'];

			if ( isset( $args[0] ) && is_object( $args[0] ) ) {
				$args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );
			}

			parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
		}

		/**
		 * Menu fallback: a link to Appearance > Menus for users who can make one,
		 * nothing for anyone else.
		 *
		 * @param array $args Arguments passed from wp_nav_menu().
		 */
		public static function fallback( $args ) {
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				return;
			}

			$container = ! empty( $args['container'] ) ? tag_escape( $args['container'] ) : '';
			$fb_output = '';

			if ( '' !== $container ) {
				$fb_output .= '<' . $container;

				if ( ! empty( $args['container_id'] ) ) {
					$fb_output .= ' id="' . esc_attr( $args['container_id'] ) . '"';
				}

				if ( ! empty( $args['container_class'] ) ) {
					$fb_output .= ' class="' . esc_attr( $args['container_class'] ) . '"';
				}

				$fb_output .= '>';
			}

			$fb_output .= '<ul';

			if ( ! empty( $args['menu_id'] ) ) {
				$fb_output .= ' id="' . esc_attr( $args['menu_id'] ) . '"';
			}

			if ( ! empty( $args['menu_class'] ) ) {
				$fb_output .= ' class="' . esc_attr( $args['menu_class'] ) . '"';
			}

			$fb_output .= '>';
			$fb_output .= '<li><a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">' . esc_html__( 'Add a menu', 'sparkling' ) . '</a></li>';
			$fb_output .= '</ul>';

			if ( '' !== $container ) {
				$fb_output .= '</' . $container . '>';
			}

			echo $fb_output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Every part escaped above.
		}
	}

endif;

/*
 * The name the class had until 2.7.0, for child themes that pass
 * `new WP_Bootstrap_Navwalker()` to wp_nav_menu(). Left alone if something else
 * -- a plugin bundling its own walker of that name -- got there first.
 */
if ( ! class_exists( 'WP_Bootstrap_Navwalker', false ) ) {
	class_alias( 'Sparkling_Bootstrap_Navwalker', 'WP_Bootstrap_Navwalker' );
}
