<?php
/**
 * About Sparkling: Support tab.
 *
 * @package sparkling
 */

?>

<div class="feature-section three-col has-3-columns is-fullwidth">

	<div class="col column">
		<h3><i class="dashicons dashicons-sos" aria-hidden="true"></i><?php esc_html_e( 'Contact Support', 'sparkling' ); ?></h3>
		<p>
			<i><?php esc_html_e( 'Ask your question in the Sparkling support forum on WordPress.org, where the Colorlib team and other users answer.', 'sparkling' ); ?></i>
		</p>
		<p><a target="_blank" rel="noopener" class="button button-primary" href="<?php echo esc_url( 'https://wordpress.org/support/theme/sparkling/' ); ?>"><?php esc_html_e( 'Contact Support', 'sparkling' ); ?><span class="screen-reader-text"> <?php esc_html_e( '(opens in a new tab)', 'sparkling' ); ?></span></a>
		</p>
	</div><!--/.col-->

	<div class="col column">
		<h3><i class="dashicons dashicons-book-alt" aria-hidden="true"></i><?php esc_html_e( 'Documentation', 'sparkling' ); ?></h3>
		<p>
			<i><?php esc_html_e( 'This is the place to go to reference different aspects of the theme. Our online documentation is an incredible resource for learning the ins and outs of using Sparkling.', 'sparkling' ); ?></i>
		</p>
		<p>
			<a target="_blank" rel="noopener" href="<?php echo esc_url( 'https://colorlib.com/wp/support/sparkling/' ); ?>"><?php esc_html_e( 'See our full documentation', 'sparkling' ); ?><span class="screen-reader-text"> <?php esc_html_e( '(opens in a new tab)', 'sparkling' ); ?></span></a>
		</p>
	</div><!--/.col-->

	<div class="col column">
		<h3><i class="dashicons dashicons-portfolio" aria-hidden="true"></i><?php esc_html_e( 'Changelog', 'sparkling' ); ?></h3>
		<p>
			<i><?php esc_html_e( 'Want to get the gist on the latest theme changes? Just consult our changelog below to get a taste of the recent fixes and features implemented.', 'sparkling' ); ?></i>
		</p>
		<p>
			<a href="<?php echo esc_url( admin_url( 'themes.php?page=sparkling-welcome&tab=changelog' ) ); ?>"><?php esc_html_e( 'See changelog', 'sparkling' ); ?></a>
		</p>
	</div><!--/.col-->

</div>
