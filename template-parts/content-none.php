<?php
/**
* The template part for displaying a message that posts cannot be found.
*
* Learn more: http://codex.wordpress.org/Template_Hierarchy
*
* @package sparkling
*/
?>
<section class="no-results not-found">
	<header class="page-header">
		<?php
		// The latest-posts front page already has the site name as its h1.
		$sparkling_heading = is_front_page() && is_home() ? 'h2' : 'h1';
		?>
		<<?php echo esc_html( $sparkling_heading ); ?> class="page-title"><?php esc_html_e( 'Nothing Found', 'sparkling' ); ?></<?php echo esc_html( $sparkling_heading ); ?>>
	</header><!-- .page-header -->

	<div class="page-content">
		<?php
		if ( is_home() && current_user_can( 'publish_posts' ) ) :
			?>

			<p>
			<?php
				$sparkling_wp_kses_args = array(
					'a' => array(
						'href' => array(),
					),
				);
				/* translators: %1$s: URL of the new post screen */
				printf( wp_kses( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'sparkling' ), $sparkling_wp_kses_args ), esc_url( admin_url( 'post-new.php' ) ) );

				?>
			</p>

		<?php elseif ( is_search() ) : ?>

			<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'sparkling' ); ?></p>
			<?php
				get_search_form();
		else :
			?>

			<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'sparkling' ); ?></p>
			<?php
				get_search_form();
		endif;
		?>
	</div><!-- .page-content -->
</section><!-- .no-results -->
