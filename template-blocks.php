<?php
/**
 * Template Name: Blocks (full width, no title)
 * Template Post Type: page
 *
 * A canvas for block patterns: no title band, no featured image and no white
 * box, in a full-width column. Full-width blocks reach the edges of the
 * window. The title is still the page's h1, for screen readers.
 *
 * @package sparkling
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main sparkling-blocks-canvas">

			<?php
			while ( have_posts() ) :
				the_post();
				?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h1 class="screen-reader-text"><?php the_title(); ?></h1>

					<div class="entry-content">
						<?php
						the_content();

						wp_link_pages(
							array(
								'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'sparkling' ),
								'after'  => '</div>',
							)
						);

						if ( is_front_page() ) {
							get_sidebar( 'home' );
						}
						?>
					</div><!-- .entry-content -->
				</article><!-- #post-## -->

			<?php endwhile; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

</div><!-- close .main-content-inner -->

<?php
get_footer();
