<?php
/**
 * @package sparkling
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="blog-item-wrap">
		<?php
		/*
		 * Only when there is an image: without one this printed an empty link.
		 * The title below links to the same post, so the image link is skipped
		 * by the keyboard and hidden from screen readers rather than announced
		 * twice.
		 */
		if ( has_post_thumbnail() ) :
			?>
			<a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
				<?php
				the_post_thumbnail(
					is_page_template( 'page-fullwidth.php' ) ? 'sparkling-featured-fullwidth' : 'sparkling-featured',
					array(
						'class' => 'single-featured',
					)
				);
				?>
			</a>
		<?php endif; ?>
		<div class="post-inner-content">
			<header class="entry-header page-header">

				<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

				<?php if ( 'post' == get_post_type() ) : ?>
				<div class="entry-meta">
					<?php sparkling_posted_on(); ?><?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
				<span class="comments-link"><i class="fa-solid fa-comments" aria-hidden="true"></i><?php comments_popup_link( esc_html__( 'Leave a comment', 'sparkling' ), esc_html__( '1 Comment', 'sparkling' ), esc_html__( '% Comments', 'sparkling' ) ); ?></span>
				<?php endif; ?>

					<?php if ( get_edit_post_link() ) : ?>
						<?php
						edit_post_link(
							sprintf(
								/* translators: %s: Name of current post */
								esc_html__( 'Edit %s', 'sparkling' ),
								the_title( '<span class="screen-reader-text">"', '"</span>', false )
							),
							'<i class="fa-solid fa-pen-to-square" aria-hidden="true"></i><span class="edit-link">',
							'</span>'
						);
						?>
				<?php endif; ?>

				</div><!-- .entry-meta -->
				<?php endif; ?>
			</header><!-- .entry-header -->

			<?php if ( is_search() ) : // Only display Excerpts for Search ?>
			<div class="entry-summary">
				<?php the_excerpt(); ?>
				<p><a class="btn btn-default read-more" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read More', 'sparkling' ); ?></a></p>
			</div><!-- .entry-summary -->
			<?php else : ?>
			<div class="entry-content">

				<?php
				if ( get_theme_mod( 'sparkling_excerpts' ) == 1 ) :
					the_excerpt();
					?>
					<p><a class="btn btn-default read-more" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php esc_html_e( 'Read More', 'sparkling' ); ?></a></p>
					<?php
				else :
					the_content( esc_html__( 'Read More', 'sparkling' ) );
				endif;
				?>

				<?php
					wp_link_pages(
						array(
							'before'      => '<div class="page-links">' . esc_html__( 'Pages:', 'sparkling' ),
							'after'       => '</div>',
							'link_before' => '<span>',
							'link_after'  => '</span>',
							'pagelink'    => '%',
							'echo'        => 1,
						)
					);
				?>
			</div><!-- .entry-content -->
			<?php endif; ?>
		</div>
	</div>
</article><!-- #post-## -->
