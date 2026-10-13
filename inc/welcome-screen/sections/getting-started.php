<?php
/**
 * About Sparkling: Getting Started tab.
 *
 * @package sparkling
 */

?>

<div class="feature-section three-col has-3-columns is-fullwidth">
	<div class="col column">
		<h3><?php esc_html_e( 'Step 1 - Install the recommended plugins', 'sparkling' ); ?></h3>
		<p><?php esc_html_e( 'Free plugins from the Sparkling team that add contact forms, a custom login page, a maintenance mode and more. None of them is required.', 'sparkling' ); ?></p>
		<p><a href="<?php echo esc_url( admin_url( 'themes.php?page=sparkling-welcome&tab=recommended_plugins' ) ); ?>"><?php esc_html_e( 'See the recommended plugins', 'sparkling' ); ?></a></p>
	</div><!--/.col-->

	<div class="col column">
		<h3><?php esc_html_e( 'Step 2 - Check our documentation', 'sparkling' ); ?></h3>
		<p><?php esc_html_e( 'Even if you\'re a long-time WordPress user, we still believe you should give our documentation a very quick Read.', 'sparkling' ); ?></p>
		<p>
			<a target="_blank" rel="noopener" href="<?php echo esc_url( 'https://colorlib.com/wp/support/sparkling/' ); ?>"><?php esc_html_e( 'Full documentation', 'sparkling' ); ?><span class="screen-reader-text"> <?php esc_html_e( '(opens in a new tab)', 'sparkling' ); ?></span></a>
		</p>
	</div><!--/.col-->

	<div class="col column">
		<h3><?php esc_html_e( 'Step 3 - Customize everything', 'sparkling' ); ?></h3>
		<p><?php esc_html_e( 'Using the WordPress Customizer you can easily customize every aspect of the theme.', 'sparkling' ); ?></p>
		<p><a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Go to Customizer', 'sparkling' ); ?></a></p>
	</div><!--/.col-->
</div><!--/.feature-section-->
