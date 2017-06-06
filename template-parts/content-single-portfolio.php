<?php
/**
 * @package sparkling
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php 
	if ( is_page_template( 'page-fullwidth.php' ) )
		the_post_thumbnail( 'sparkling-featured-fullwidth', array( 'class' => 'single-featured' ));
	else
		the_post_thumbnail( 'sparkling-featured', array( 'class' => 'single-featured' ));
	?>
	<div class="post-inner-content">
		<header class="entry-header page-header">

			<h1 class="entry-title "><?php the_title(); ?></h1>

			<div class="entry-meta">
				<?php sparkling_posted_on(); ?>

				<?php
					/* translators: used between list items, there is a space after the comma */
					$types = get_the_term_list( get_the_ID(), 'sparkling_portfolio_type', '', esc_html__( ', ', 'sparkling' ) );
					if ( $types ) :
				?>
				<span class="cat-links"><i class="fa fa-folder-open-o"></i>
					<?php printf( esc_html__( ' %1$s', 'sparkling' ), $types ); ?>
				</span>
				<?php endif; // End if categories ?>
				<?php if ( get_edit_post_link() ) : ?>
					<?php
						edit_post_link(
							sprintf(
								/* translators: %s: Name of current post */
								esc_html__( 'Edit %s', 'sparkling' ),
								the_title( '<span class="screen-reader-text">"', '"</span>', false )
							),
							'<i class="fa fa-pencil-square-o"></i><span class="edit-link">',
							'</span>'
						);
					?>
				<?php endif; ?>

			</div><!-- .entry-meta -->
		</header><!-- .entry-header -->

		<div class="entry-content">
			<?php the_content(); ?>
			<?php
				wp_link_pages( array(
					'before'            => '<div class="page-links">'.esc_html__( 'Pages:', 'sparkling' ),
					'after'             => '</div>',
					'link_before'       => '<span>',
					'link_after'        => '</span>',
					'pagelink'          => '%',
					'echo'              => 1
	       		) );
	    	?>
		</div><!-- .entry-content -->
	</div>

</article><!-- #post-## -->
