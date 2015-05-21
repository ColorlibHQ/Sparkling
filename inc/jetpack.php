<?php
/**
 * Jetpack Compatibility File
 * See: https://jetpack.me/
 *
 * @package sparkling
 */

/**
 * Add theme support for Infinite Scroll.
 * See: https://jetpack.me/support/infinite-scroll/
 */
function sparkling_jetpack_setup() {
	add_theme_support( 'infinite-scroll', array(
		'type'      => 'click',
		'container' => 'main',
		'footer'    => 'page',
	) );
}
add_action( 'after_setup_theme', 'sparkling_jetpack_setup' );
