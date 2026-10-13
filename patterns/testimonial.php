<?php
/**
 * Title: Testimonial
 * Slug: sparkling/testimonial
 * Categories: sparkling, testimonials
 * Keywords: testimonial, quote, review
 * Viewport Width: 1200
 * Description: A centred customer quote on a light background.
 *
 * @package sparkling
 */

?>
<!-- wp:group {"backgroundColor":"light","style":{"spacing":{"padding":{"top":"48px","bottom":"48px","left":"24px","right":"24px"}}},"align":"full","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-light-background-color has-background" style="padding-top:48px;padding-right:24px;padding-bottom:48px;padding-left:24px"><!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"20px"}}} -->
<p class="has-text-align-center" style="font-size:20px"><?php esc_html_e( '"Working with this team was easy from start to finish. The result was better than we hoped."', 'sparkling' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontStyle":"italic"}}} -->
<p class="has-text-align-center" style="font-style:italic"><?php esc_html_e( 'Jane Doe, Company name', 'sparkling' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
