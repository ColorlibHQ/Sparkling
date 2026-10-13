<?php
/**
 * Title: Three features
 * Slug: sparkling/features
 * Categories: sparkling, features
 * Keywords: features, services, columns
 * Viewport Width: 1200
 * Description: A heading over three columns, each with a title, a short text and a link.
 *
 * @package sparkling
 */

?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"48px","bottom":"48px"}}},"layout":{"type":"constrained","contentSize":"1140px"}} -->
<div class="wp-block-group" style="padding-top:48px;padding-bottom:48px"><!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center"><?php esc_html_e( 'What we do', 'sparkling' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><?php esc_html_e( 'Say in a sentence what sets your work apart.', 'sparkling' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:columns {"style":{"spacing":{"margin":{"top":"32px"}}}} -->
<div class="wp-block-columns" style="margin-top:32px"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Design', 'sparkling' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e( 'A short description of this service, what it includes and who it is for.', 'sparkling' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Development', 'sparkling' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e( 'A short description of this service, what it includes and who it is for.', 'sparkling' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Support', 'sparkling' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e( 'A short description of this service, what it includes and who it is for.', 'sparkling' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
