<?php
/**
 * The template for displaying image attachments.
 *
 * @package sparkling
 */

get_header();
?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">

			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<div class="post-inner-content">
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<header class="entry-header">
							<h1 class="entry-title"><?php the_title(); ?></h1>

							<div class="entry-meta">
								<?php sparkling_posted_on(); ?>
							</div><!-- .entry-meta -->

							<nav id="image-navigation" class="navigation-image nav-links" aria-label="<?php esc_attr_e( 'Image navigation', 'sparkling' ); ?>">
								<div class="nav-previous"><?php previous_image_link( false, __( '<i class="fa-solid fa-chevron-left"></i> Previous', 'sparkling' ) ); ?></div>
								<div class="nav-next"><?php next_image_link( false, __( 'Next <i class="fa-solid fa-chevron-right"></i>', 'sparkling' ) ); ?></div>
							</nav><!-- #image-navigation -->
						</header><!-- .entry-header -->

						<div class="entry-content">

							<div class="entry-attachment">
								<div class="attachment">
									<?php
									/*
									 * Link the image to the next image in its gallery, or back to the
									 * first after the last one; an image on its own -- or one not
									 * attached to any post -- links to its file. An unattached image
									 * used to be treated as part of a "gallery" of every unattached
									 * image in the library, which queried them all.
									 */
									$sparkling_next_url = wp_get_attachment_url();
									$sparkling_parent   = wp_get_post_parent_id();

									if ( $sparkling_parent ) {
										$sparkling_gallery = array_values(
											get_children(
												array(
													'post_parent'    => $sparkling_parent,
													'post_status'    => 'inherit',
													'post_type'      => 'attachment',
													'post_mime_type' => 'image',
													'order'          => 'ASC',
													'orderby'        => 'menu_order ID',
													'fields'         => 'ids',
												)
											)
										);

										$sparkling_index = array_search( get_the_ID(), $sparkling_gallery, true );

										if ( count( $sparkling_gallery ) > 1 && false !== $sparkling_index ) {
											$sparkling_next_url = get_attachment_link( isset( $sparkling_gallery[ $sparkling_index + 1 ] ) ? $sparkling_gallery[ $sparkling_index + 1 ] : $sparkling_gallery[0] );
										}
									}
									?>

									<a href="<?php echo esc_url( $sparkling_next_url ); ?>" rel="attachment">
										<?php
										$sparkling_attachment_size = apply_filters( 'sparkling_attachment_size', array( 1200, 1200 ) ); // Filterable image size.
										// The image is the link's only content, so it needs a text
										// alternative even when none was entered in the library.
										$sparkling_alt = get_post_meta( get_the_ID(), '_wp_attachment_image_alt', true );
										echo wp_get_attachment_image( get_the_ID(), $sparkling_attachment_size, false, ( '' === trim( (string) $sparkling_alt ) ) ? array( 'alt' => get_the_title() ) : array() );
										?>
									</a>
								</div><!-- .attachment -->

								<?php if ( has_excerpt() ) : ?>
								<div class="entry-caption">
									<?php the_excerpt(); ?>
								</div><!-- .entry-caption -->
								<?php endif; ?>
							</div><!-- .entry-attachment -->

							<?php the_content(); ?>
							<?php
								wp_link_pages(
									array(
										'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'sparkling' ),
										'after'  => '</div>',
									)
								);
							?>

						</div><!-- .entry-content -->
					</article><!-- #post-<?php the_ID(); ?> -->
				</div>
				<?php
				// If comments are open or we have at least one comment, load up the comment template.
				if ( get_theme_mod( 'sparkling_page_comments', 1 ) && ( comments_open() || get_comments_number() ) ) :
					comments_template();
				endif;
				?>

			<?php endwhile; // end of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
