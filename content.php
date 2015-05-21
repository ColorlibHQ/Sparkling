<?php
/**
 * @package sparkling
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="blog-item-wrap">
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
			 	<?php the_post_thumbnail( 'sparkling-featured', array( 'class' => 'single-featured' )); ?>
			</a>
		<div class="post-inner-content">
			<header class="entry-header page-header">

				<h1 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>

				<?php if ( 'post' == get_post_type() ) : ?>
				<div class="entry-meta">
					<?php sparkling_posted_on(); ?><?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
				<span class="comments-link"><i class="fa fa-comment-o"></i><?php comments_popup_link( __( 'Leave a comment', 'sparkling' ), __( '1 Comment', 'sparkling' ), __( '% Comments', 'sparkling' ) ); ?></span>
				<?php endif; ?>

				<?php edit_post_link( __( 'Edit', 'sparkling' ), '<i class="fa fa-pencil-square-o"></i><span class="edit-link">', '</span>' ); ?>

				</div><!-- .entry-meta -->
				<?php endif; ?>
			</header><!-- .entry-header -->

			<?php if ( is_search() ) : // Only display Excerpts for Search ?>
			<div class="entry-summary">
				<?php the_excerpt(); ?>
				<p><a class="btn btn-default read-more" href="<?php the_permalink(); ?>"><?php _e( 'Read More', 'sparkling' ); ?></a></p>
			</div><!-- .entry-summary -->
			<?php else : ?>
			<div class="entry-content">

					<?php the_content(); ?>

				<p><a class="btn btn-default read-more" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php _e( 'Read More', 'sparkling' ); ?></a></p>

				<?php
					wp_link_pages( array(
						'before'            => '<div class="page-links">'.__( 'Pages:', 'sparkling' ),
						'after'             => '</div>',
						'link_before'       => '<span>',
						'link_after'        => '</span>',
						'pagelink'          => '%',
						'echo'              => 1
		       		) );
		    	?>
			</div><!-- .entry-content -->
			<?php endif; ?>
		</div>
	</div>
</article><!-- #post-## -->
