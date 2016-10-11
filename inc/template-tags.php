<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package sparkling
 */

/**
 * Customize the PageNavi HTML before it is output to support bootstrap
 */
function sparkling_wp_pagenavi_bootstrap_markup( $html ) {
	$out = '';

	//wrap a's and span's in li's
	$out = str_replace( "<div", "", $html );
	$out = str_replace( "class='wp-pagenavi'>", "", $out );
	$out = str_replace( "<a", "<li><a", $out );
	$out = str_replace( "</a>", "</a></li>", $out );
	$out = str_replace( "<span", "<li><span", $out );
	$out = str_replace( "</span>", "</span></li>", $out );
	$out = str_replace( "</div>", "", $out );

	return '<ul class="pagination pagination-centered wp-pagenavi-pagination">' . $out . '</ul>';
}
add_filter( 'wp_pagenavi', 'sparkling_wp_pagenavi_bootstrap_markup' );



if ( ! function_exists( 'sparkling_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function sparkling_posted_on() {
	$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string .= '<time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	printf( '<span class="posted-on"><i class="fa fa-calendar"></i> %1$s</span><span class="byline"> <i class="fa fa-user"></i> %2$s</span>',
		sprintf( '<a href="%1$s" rel="bookmark">%2$s</a>',
			esc_url( get_permalink() ),
			$time_string
		),
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s">%2$s</a></span>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_html( get_the_author() )
		)
	);
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function sparkling_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'sparkling_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );
		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );
		set_transient( 'sparkling_categories', $all_the_cool_cats );
	}
	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so sparkling_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so sparkling_categorized_blog should return false.
		return false;
	}
}
/**
 * Flush out the transients used in sparkling_categorized_blog.
 */
function sparkling_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'sparkling_categories' );
}
add_action( 'edit_category', 'sparkling_category_transient_flusher' );
add_action( 'save_post',     'sparkling_category_transient_flusher' );
