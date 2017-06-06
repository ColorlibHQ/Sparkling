<?php
/**
 * The template for displaying Archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package sparkling
 */

 get_header(); ?>

 	<div id="primary" class="content-area">
 		<main id="main" class="site-main" role="main">

 		<?php
 		if ( have_posts() ) : ?>

 			<header class="page-header portfolio-archive">
 				<?php
 					$portfolio_title = of_get_option( 'portfolio_title', '' );
 					if ( $portfolio_title ) {
 						echo '<h1 class="page-title">'.$portfolio_title.'</h1>';
 					}else{
 						echo '<h1 class="page-title">'.post_type_archive_title( '', false ).'</h1>';
 					}
 					

 					$portfolio_categories = get_terms( array(
					    'taxonomy' => 'sparkling_portfolio_type',
					) );

					if ( !empty( $portfolio_categories ) ) {
						echo '<ul id="portfolio-filters" class="portfolio-categories">';
							echo '<li data-slug="*" class="active">'.__( 'All', 'sparkling' ).'</li>';
						foreach ( $portfolio_categories as $portfolio_category ) {
							echo '<li data-slug=".'.$portfolio_category->slug.'">'.$portfolio_category->name.'</li>';
						}
						echo '</ul>';
					}

 				?>
 			</header><!-- .page-header -->
 			<section id="portfolio-section">
 			<?php
 			/* Start the Loop */
 			while ( have_posts() ) : the_post();

 				/*
 				 * Include the Post-Format-specific template for the content.
 				 * If you want to override this in a child theme, then include a file
 				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
 				 */
 				get_template_part( 'template-parts/content', 'portfolio' );

 			endwhile;

 			echo '</section>';

 		else :

 			get_template_part( 'template-parts/content', 'none' );

 		endif; ?>

 		</main><!-- #main -->
 	</div><!-- #primary -->

 <?php
 get_sidebar( 'portfolio' );
 get_footer();
