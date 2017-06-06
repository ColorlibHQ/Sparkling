<?php
/**
 * @package sparkling
 */
?>
<?php
$permalink = get_the_permalink();
$url = get_post_meta( get_the_ID(), 'sparkling_companion_portfolio_link', true );
if ( $url ) {
	$permalink = $url;
}
$project_types = wp_get_post_terms( get_the_ID(), 'sparkling_portfolio_type', array( "fields" => "names" ) );
$project_types_slug = wp_get_post_terms( get_the_ID(), 'sparkling_portfolio_type', array( "fields" => "slugs" ) );

$thumbnail_size = 'sparkling-portfolio';
$layout = of_get_option( 'portfolio_layout', 'side-pull-left' );

if ( $layout == 'full-width' ) {
	$thumbnail_size = 'sparkling-portfolio-big';
}

?>
<div class="col-md-4 col-sm-6 portfolio-item <?php echo implode( ' ', $project_types_slug ) ?> p0">
	<div class="image-tile inner-title hover-reveal text-center">
		<a href="<?php echo esc_url($permalink); ?>" title="<?php the_title_attribute(); ?>">
			<?php the_post_thumbnail( $thumbnail_size ); ?>
			<div class="title"><?php
				the_title( '<h5 class="mb0">', '</h5>' );

				
				if ( ! empty( $project_types ) ) {
					echo '<span>' . implode( ' / ', $project_types ) . '</span>';
				} ?>
			</div>
		</a>
	</div>
</div>