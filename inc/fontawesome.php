<?php
/**
 * Font Awesome loading strategy.
 *
 * The theme bundles a subset of Font Awesome 7 containing only the glyphs it
 * draws itself -- roughly 10 KB over the wire against roughly 250 KB for the
 * complete library. Sites that use Font Awesome classes of their own in menus
 * need the complete library, and before 2.6.11 the only way to get it was a
 * PHP filter, so those icons silently disappeared on update.
 *
 * Three things decide which is loaded, in order of increasing precedence:
 *
 *   1. A scan of the nav menus (the `auto` mode, and the default).
 *   2. The `fontawesome_library` Customizer setting, when set to `full` or
 *      `subset`, which also covers icons the scan cannot see -- widgets, post
 *      content, page builders.
 *   3. The `sparkling_full_fontawesome` filter, which overrides both.
 *
 * The scan result is cached in an option and recomputed when a menu changes or
 * the theme version does. The icon names are read from the bundled stylesheets
 * rather than a generated list, so they cannot drift from what actually ships.
 *
 * @package sparkling
 */

/**
 * Option holding the cached menu scan.
 */
if ( ! defined( 'SPARKLING_FONTAWESOME_CACHE' ) ) {
	define( 'SPARKLING_FONTAWESOME_CACHE', 'sparkling_fontawesome_scan' );
}

/**
 * The directory the bundled Font Awesome stylesheets live in.
 *
 * @return string Absolute path, with a trailing slash.
 */
function sparkling_fontawesome_dir() {
	return get_template_directory() . '/assets/css/fontawesome/';
}

/**
 * The configured library mode.
 *
 * @return string One of `auto`, `full` or `subset`.
 */
function sparkling_fontawesome_library_mode() {
	$mode = of_get_option( 'fontawesome_library', 'auto' );

	if ( ! in_array( $mode, array( 'auto', 'full', 'subset' ), true ) ) {
		return 'auto';
	}

	return $mode;
}

/**
 * Whether the complete Font Awesome library should be enqueued.
 *
 * @return bool
 */
function sparkling_fontawesome_use_full_set() {
	$mode = sparkling_fontawesome_library_mode();

	if ( 'full' === $mode ) {
		$full = true;
	} elseif ( 'subset' === $mode ) {
		$full = false;
	} else {
		$full = sparkling_fontawesome_menus_need_full_set();
	}

	/**
	 * Filters whether the complete Font Awesome library is loaded.
	 *
	 * Overrides both the automatic scan and the Customizer setting.
	 *
	 * @param bool $full Whether to load the complete library.
	 */
	return (bool) apply_filters( 'sparkling_full_fontawesome', $full );
}

/**
 * Whether the nav menus reference an icon the bundled subset does not carry.
 *
 * Cached; see sparkling_fontawesome_clear_cache().
 *
 * @return bool
 */
function sparkling_fontawesome_menus_need_full_set() {
	$cache = get_option( SPARKLING_FONTAWESOME_CACHE );

	if ( is_array( $cache ) && isset( $cache['version'], $cache['full'] ) && SPARKLING_VERSION === $cache['version'] ) {
		return (bool) $cache['full'];
	}

	$full = sparkling_fontawesome_scan_menus();

	update_option(
		SPARKLING_FONTAWESOME_CACHE,
		array(
			'version' => SPARKLING_VERSION,
			'full'    => $full,
		),
		true
	);

	return $full;
}

/**
 * Scans the assigned nav menus for icons missing from the bundled subset.
 *
 * Only a class that is a real Font Awesome icon name counts. Sizing and style
 * classes (`fa-fw`, `fa-2x`, `fa-spin`, `fa-solid`) are not icon names and are
 * ignored, as is any unrelated class that happens to start with `fa-`.
 *
 * @return bool True if the complete library is needed.
 */
function sparkling_fontawesome_scan_menus() {
	$classes = sparkling_fontawesome_menu_icon_classes();

	if ( empty( $classes ) ) {
		return false;
	}

	// Anything the subset already carries needs no further work.
	$subset  = sparkling_fontawesome_icon_names( array( 'subset/fontawesome-subset.min.css' ) );
	$missing = array_diff( $classes, array_keys( $subset ) );

	if ( empty( $missing ) ) {
		return false;
	}

	// Only now is it worth reading the complete library to tell an icon name
	// from a utility or unrelated class.
	$complete = sparkling_fontawesome_icon_names( array( 'fontawesome.min.css', 'brands.min.css' ) );

	foreach ( $missing as $class ) {
		if ( isset( $complete[ $class ] ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Collects every `fa-` class used in the assigned nav menus.
 *
 * Reads both the Navigation Label and the CSS Classes field, the two places a
 * menu item can carry an icon.
 *
 * @return array List of lowercased class names.
 */
function sparkling_fontawesome_menu_icon_classes() {
	$locations = get_nav_menu_locations();
	$found     = array();

	foreach ( array_keys( get_registered_nav_menus() ) as $location ) {
		if ( empty( $locations[ $location ] ) ) {
			continue;
		}

		$items = wp_get_nav_menu_items( $locations[ $location ] );

		if ( ! is_array( $items ) ) {
			continue;
		}

		foreach ( $items as $item ) {
			$haystack = isset( $item->title ) ? $item->title : '';

			if ( ! empty( $item->classes ) && is_array( $item->classes ) ) {
				$haystack .= ' ' . implode( ' ', $item->classes );
			}

			if ( preg_match_all( '/\bfa-[a-z0-9]+(?:-[a-z0-9]+)*/', strtolower( $haystack ), $matches ) ) {
				$found = array_merge( $found, $matches[0] );
			}
		}
	}

	return array_values( array_unique( $found ) );
}

/**
 * Reads icon names out of the bundled Font Awesome stylesheets.
 *
 * An icon is a rule that declares the `--fa` custom property holding its
 * glyph; utility classes such as `.fa-fw` declare other properties and are
 * skipped. Selectors are read as a group so aliases -- `.fa-edit` alongside
 * `.fa-pen-to-square` -- are all collected.
 *
 * @param array $files Stylesheet paths relative to the Font Awesome directory.
 * @return array Map of icon name to true, for O(1) lookup.
 */
function sparkling_fontawesome_icon_names( $files ) {
	$names = array();

	foreach ( $files as $file ) {
		$path = sparkling_fontawesome_dir() . $file;

		if ( ! is_readable( $path ) ) {
			continue;
		}

		// A theme-owned file on local disk, read once and cached thereafter.
		$css = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		if ( ! $css ) {
			continue;
		}

		$css = preg_replace( '#/\*.*?\*/#s', '', $css );

		if ( ! preg_match_all( '/([^{}]+)\{([^{}]*)\}/', $css, $rules, PREG_SET_ORDER ) ) {
			continue;
		}

		foreach ( $rules as $rule ) {
			if ( ! preg_match( '/--fa\s*:/', $rule[2] ) ) {
				continue;
			}

			foreach ( explode( ',', $rule[1] ) as $selector ) {
				if ( preg_match( '/^\s*\.(fa-[a-z0-9-]+)\s*$/', $selector, $name ) ) {
					$names[ $name[1] ] = true;
				}
			}
		}
	}

	return $names;
}

/**
 * Drops the cached scan so the next front-end request recomputes it.
 */
function sparkling_fontawesome_clear_cache() {
	delete_option( SPARKLING_FONTAWESOME_CACHE );
}

add_action( 'wp_update_nav_menu', 'sparkling_fontawesome_clear_cache' );
add_action( 'wp_delete_nav_menu', 'sparkling_fontawesome_clear_cache' );
add_action( 'customize_save_after', 'sparkling_fontawesome_clear_cache' );
add_action( 'after_switch_theme', 'sparkling_fontawesome_clear_cache' );
