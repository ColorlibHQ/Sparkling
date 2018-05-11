<?php
/**
 * @package sparkling
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="blog-item-wrap">
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
				<?php
				if ( is_page_template( 'page-fullwidth.php' ) ) {
					the_post_thumbnail(
						'sparkling-featured-fullwidth', array(
							'class' => 'single-featured',
						)
					);
				} else {
					the_post_thumbnail(
						'sparkling-featured', array(
							'class' => 'single-featured',
						)
					);
				}
				?>
			</a>
		<div class="post-inner-content">
			<header class="entry-header page-header">

				<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

				<?php if ( 'post' == get_post_type() ) : ?>
				<div class="entry-meta">
					<?php sparkling_posted_on(); ?><?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
				<span class="comments-link"><i class="fa fa-comments"></i><?php comments_popup_link( esc_html__( 'Leave a comment', 'sparkling' ), esc_html__( '1 Comment', 'sparkling' ), esc_html__( '% Comments', 'sparkling' ) ); ?></span>
				<?php endif; ?>

				<?php if ( get_edit_post_link() ) : ?>
					<?php
						edit_post_link(
							sprintf(
								/* translators: %s: Name of current post */
								esc_html__( 'Edit %s', 'sparkling' ),
								the_title( '<span class="screen-reader-text">"', '"</span>', false )
							),
							'<i class="fa fa-edit"></i><span class="edit-link">',
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
