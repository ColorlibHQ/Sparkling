<?php
/**
 * Title: About with image
 * Slug: sparkling/about
 * Categories: sparkling, about
 * Keywords: about, intro, media, text
 * Viewport Width: 1200
 * Description: An image beside a heading, a paragraph and a button.
 *
 * @package sparkling
 */

?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"48px","bottom":"48px"}}},"layout":{"type":"constrained","contentSize":"1140px"}} -->
<div class="wp-block-group" style="padding-top:48px;padding-bottom:48px"><!-- wp:media-text {"mediaType":"image","verticalAlignment":"center"} -->
<div class="wp-block-media-text is-stacked-on-mobile is-vertically-aligned-center"><figure class="wp-block-media-text__media"></figure><div class="wp-block-media-text__content"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e( 'About us', 'sparkling' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e( 'Tell visitors who you are, what you do and why it matters. Replace the empty image area with a photo of your team or your work.', 'sparkling' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button"><?php esc_html_e( 'Read our story', 'sparkling' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div></div>
<!-- /wp:media-text --></div>
<!-- /wp:group -->
