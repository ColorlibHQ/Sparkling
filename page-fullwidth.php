<?php
/**
 * Template Name: Full-width(no sidebar)
 *
 * This is the template that displays full width page without sidebar
 *
 * @package sparkling
 */

get_header(); ?>

  <div id="primary" class="content-area">

    <main id="main" class="site-main" role="main">

      <?php while ( have_posts() ) : the_post(); ?>

        <?php get_template_part( 'content', 'page' ); ?>

        <?php
          // If comments are open or we have at least one comment, load up the comment template
          if ( get_theme_mod( 'sparkling_page_comments', 1 ) == 1 ) :
            if ( comments_open() || '0' != get_comments_number() ) :
              comments_template();
            endif;
          endif;
        ?>

      <?php endwhile; // end of the loop. ?>

    </main><!-- #main -->

  </div><!-- #primary -->

</div><!-- close .main-content-inner -->

<?php get_footer(); ?>
