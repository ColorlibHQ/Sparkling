<?php
/**
 * The template for displaying search forms in Sparkling
 *
 * @package sparkling
 */

// A page can hold several search forms (the sidebar widget, the 404 page, the
// "nothing found" message), so each gets its own id for its label.
$sparkling_search_field = wp_unique_id( 'search-field-' );
?>

<form role="search" method="get" class="form-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<div class="input-group">
		<label class="screen-reader-text" for="<?php echo esc_attr( $sparkling_search_field ); ?>"><?php esc_html_e( 'Search for:', 'sparkling' ); ?></label>
		<input type="search" id="<?php echo esc_attr( $sparkling_search_field ); ?>" class="form-control search-query" placeholder="<?php echo esc_attr_x( 'Search&hellip;', 'placeholder', 'sparkling' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" />
		<span class="input-group-btn">
			<button type="submit" class="btn btn-default" aria-label="<?php echo esc_attr_x( 'Search', 'submit button', 'sparkling' ); ?>"><span class="fa-solid fa-magnifying-glass" aria-hidden="true"></span></button>
		</span>
	</div>
</form>
