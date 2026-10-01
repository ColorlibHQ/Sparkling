<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package sparkling
 */
?>
		</div><!-- close .row -->
	</div><!-- close .container -->
</div><!-- close .site-content -->

	<div id="footer-area">
		<div class="container footer-inner">
			<div class="row">
				<?php get_sidebar( 'footer' ); ?>
			</div>
		</div>

		<footer id="colophon" class="site-footer">
			<div class="site-info container">
				<div class="row">
					<?php
					if ( of_get_option( 'footer_social' ) ) {
						sparkling_social_icons();}
					?>
					<nav class="col-md-6" aria-label="<?php esc_attr_e( 'Footer', 'sparkling' ); ?>">
						<?php sparkling_footer_links(); ?>
					</nav>
					<div class="copyright col-md-6">
						<?php echo wp_kses_post( of_get_option( 'custom_footer_text', '' ) ); ?>
						<?php sparkling_footer_info(); ?>
					</div>
				</div>
			</div><!-- .site-info -->
			<button type="button" class="scroll-to-top"><span class="fa-solid fa-angle-up" aria-hidden="true"></span><span class="screen-reader-text"><?php esc_html_e( 'Back to top', 'sparkling' ); ?></span></button><!-- .scroll-to-top -->
		</footer><!-- #colophon -->
	</div>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
