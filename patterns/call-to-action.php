<?php
/**
 * Title: Call to action band
 * Slug: sparkling/call-to-action
 * Categories: sparkling, call-to-action
 * Keywords: call to action, cta, banner, button
 * Viewport Width: 1200
 * Description: A dark band with a line of text and a button, like the call to action under the front page slider.
 *
 * @package sparkling
 */

?>
<!-- wp:group {"align":"full","backgroundColor":"dark","textColor":"background","style":{"spacing":{"padding":{"top":"48px","bottom":"48px","left":"24px","right":"24px"}}},"layout":{"type":"constrained","contentSize":"1140px"}} -->
<div class="wp-block-group alignfull has-background-color has-dark-background-color has-text-color has-background" style="padding-top:48px;padding-right:24px;padding-bottom:48px;padding-left:24px"><!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"66.66%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:66.66%"><!-- wp:paragraph {"style":{"typography":{"fontSize":"24px"},"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
<p style="margin-top:0;margin-bottom:0;font-size:24px"><?php esc_html_e( 'Ready to start your next project? Let us know.', 'sparkling' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"33.33%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:33.33%"><!-- wp:buttons {"layout":{"type":"flex","justifyContent":"right"}} -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button"><?php esc_html_e( 'Get in touch', 'sparkling' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
