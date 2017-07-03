<?php
/**
 * The template for displaying image attachments.
 *
 * @package sparkling
 */

get_header();
?>

			<?php while ( have_posts() ) : the_post(); ?>
				<div class="post-inner-content">
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<header class="entry-header">
							<h1 class="entry-title"><?php the_title(); ?></h1>

							<div class="entry-meta">
								<?php sparkling_posted_on(); ?>
							</div><!-- .entry-meta -->

							<nav role="navigation" id="image-navigation" class="navigation-image nav-links">
								<div class="nav-previous"><?php previous_image_link( false, __( '<i class="fa fa-chevron-left"></i> Previous', 'sparkling' ) ); ?></div>
								<div class="nav-next"><?php next_image_link( false, __( 'Next <i class="fa fa-chevron-right"></i>', 'sparkling' ) ); ?></div>
							</nav><!-- #image-navigation -->
						</header><!-- .entry-header -->

						<div class="entry-content">

							<div class="entry-attachment">
								<div class="attachment">
									<?php
										/**
										 * Grab the IDs of all the image attachments in a gallery so we can get the URL of the next adjacent image in a gallery,
										 * or the first image (if we're looking at the last image in a gallery), or, in a gallery of one, just the link to that image file
										 */
										$attachments = array_values( get_children( array(
											'post_parent'    => $post->post_parent,
											'post_status'    => 'inherit',
											'post_type'      => 'attachment',
											'post_mime_type' => 'image',
											'order'          => 'ASC',
											'orderby'        => 'menu_order ID',
										) ) );
									foreach ( $attachments as $k => $attachment ) {
										if ( $attachment->ID == $post->ID ) {
											break;
										}
									}
										$k++;
										// If there is more than 1 attachment in a gallery
									if ( count( $attachments ) > 1 ) {
										if ( isset( $attachments[ $k ] ) ) {
											// get the URL of the next image attachment
											$next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
										} else {                                        // or get the URL of the first image attachment
											$next_attachment_url = get_attachment_link( $attachments[0]->ID );
										}
									} else {
										// or, if there's only 1 image, get the URL of the image
										$next_attachment_url = wp_get_attachment_url();
									}
									?>

									<a href="<?php echo $next_attachment_url; ?>" title="<?php the_title_attribute(); ?>" rel="attachment"><?php
										$attachment_size = apply_filters( 'sparkling_attachment_size', array( 1200, 1200 ) ); // Filterable image size.
										echo wp_get_attachment_image( $post->ID, $attachment_size );
									?></a>
								</div><!-- .attachment -->

								<?php if ( ! empty( $post->post_excerpt ) ) : ?>
								<div class="entry-caption">
									<?php the_excerpt(); ?>
								</div><!-- .entry-caption -->
								<?php endif; ?>
							</div><!-- .entry-attachment -->

							<?php the_content(); ?>
							<?php
								wp_link_pages( array(
									'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'sparkling' ),
									'after'  => '</div>',
								) );
							?>

						</div><!-- .entry-content -->

						<footer class="entry-meta">
						</footer><!-- .entry-meta -->
					</article><!-- #post-<?php the_ID(); ?> -->
				</div>
				<?php
					// If comments are open or we have at least one comment, load up the comment template
				if ( get_theme_mod( 'sparkling_page_comments', 1 ) == 1 ) :
					if ( comments_open() || '0' != get_comments_number() ) :
						comments_template();
						endif;
					endif;
				?>

			<?php endwhile; // end of the loop. ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
