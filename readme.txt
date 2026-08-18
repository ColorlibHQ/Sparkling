=== Sparkling ===

Contributors: colorlib
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 2.6.4
License: GNU General Public License v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Tags: blog, news, e-commerce, one-column, two-columns, left-sidebar, right-sidebar, custom-background, custom-colors, custom-header, editor-style, featured-images, footer-widgets, full-width-template, post-formats, sticky-post, theme-options, block-styles, wide-blocks

Clean, minimal and fully responsive WordPress theme for blogs, news, business, portfolio and shop sites.

== Description ==

Sparkling is a clean, minimal and fully responsive WordPress theme suited to blogs, news, magazines, business,
portfolio and shop sites.

It ships a full-width front page slider, a call-to-action bar, a social icon menu, an author bio box, and popular
posts and enhanced category widgets. Four sidebar layouts (right, left, none, full width) can be set globally in
the Customizer or overridden per post and per page. Dozens of Customizer options cover colours, typography,
header, footer and layout, all with a live preview.

Built on Bootstrap 3, the theme's own front-end JavaScript uses plain DOM APIs and declares no jQuery dependency.
Sparkling is translation ready and ships with over twenty translations, is WooCommerce ready, and works with
bbPress, Contact Form 7 and Jetpack.

== Installation ==

1. In your WordPress admin, go to Appearance > Themes and click Add New.
2. Click Upload Theme, choose the theme zip file, and click Install Now.
3. Click Activate.
4. Go to Appearance > Customize to configure the theme, or Appearance > About Sparkling for a guided setup.

== Frequently Asked Questions ==

= How do I enable the front page slider? =

Go to Appearance > Customize > Sparkling Options > Slider Options, switch the slider on and choose a category.
The slider shows posts from that category that have a featured image set. If no post in the chosen category has a
featured image, the slider is not rendered.

= How do I change the sidebar position? =

Appearance > Customize > Sparkling Options > Layout Options sets the site-wide default. Individual posts, pages
and WooCommerce products can override it from the layout box in the editor sidebar.

= Does the theme require jQuery? =

The theme's own scripts do not. Bootstrap's bundled JavaScript still uses jQuery, which WordPress includes.

== Copyright ==

Sparkling WordPress Theme, Copyright 2014-2026 Colorlib
Sparkling is distributed under the terms of the GNU General Public License v2 or later.

Sparkling bundles the following third-party resources:

Underscores
    Copyright 2012-2015 Automattic, Inc.
    License: GNU General Public License v2 or later
    Source: https://underscores.me/

Bootstrap v3.4.1
    Copyright 2011-2019 Twitter, Inc.
    License: MIT
    Source: https://getbootstrap.com/

FlexSlider v2.7.0
    Copyright 2012 WooThemes
    License: GNU General Public License v2
    Source: https://github.com/woocommerce/FlexSlider

Font Awesome Free v5.1.1
    Copyright Fonticons, Inc.
    License: Icons CC BY 4.0, Fonts SIL OFL 1.1, Code MIT
    Source: https://fontawesome.com/license

Academicons v1.8.6
    Copyright James Walsh
    License: Fonts SIL OFL 1.1, Code MIT
    Source: https://jpswalsh.github.io/academicons/

WP Bootstrap Navwalker
    Copyright Edward McIntyre
    License: GNU General Public License v2 or later
    Source: https://github.com/wp-bootstrap/wp-bootstrap-navwalker

Screenshot image
    Copyright Colorlib, distributed under the same license as the theme.

== Changelog ==

= 2.6.4 =
* Fixed the featured image failing with a 403 Forbidden on the post edit screen (issue #272). The Popular Posts widget enqueued its media picker on admin_init, which fires on every admin screen. wp_enqueue_media() only runs once per request -- core guards it with did_action() -- so that call won the race against the post editor's own wp_enqueue_media( array( 'post' => $post->ID ) ), leaving wp.media with no post context and therefore no update-post_{id} nonce. The enqueue now runs on admin_enqueue_scripts, scoped to the widgets screen and the Customizer, which are the only places this widget's form is rendered.

* Fixed the sidebar dropping below the content at exactly 992px, and the slider caption disappearing at exactly 768px (issue #267). Three media queries used Bootstrap 3's breakpoint values as max-width instead of one pixel below them, so at exactly 768px and 992px both the mobile rule and Bootstrap's grid rule applied. The theme's rule won on specificity and unfloated the content, dropping the sidebar. Boundaries are now 767/768 and 991/992 with no overlap and no gap, in both style.css and assets/css/flexslider.css.

= 2.6.3 =
* Welcome screen: Recommended Plugins now renders WordPress core's own plugin-card markup, so its grid, buttons and
  responsive columns come from core instead of ~80 lines of theme CSS. Fixed a collapsed container and oversized
  cards caused by nesting that markup inside the About page wrapper.
* Restored the original comments-title translation string.

= 2.6.0 =
* Removed the bundled Epsilon framework (38 files). The theme used one checkbox control from it, now replaced by a
  self-contained Customizer control. Saved settings are unaffected. The framework's AJAX layer had no nonce or
  capability checks; that surface is gone.
* Fixed a fatal that occurred if another plugin unregistered a Customizer setting the theme adjusts.

= 2.5.0 =
* Security: fixed an unauthenticated change to the front page setting, CSRF-able plugin activation handlers, two
  AJAX handlers that echoed unescaped input, and a colour sanitizer that returned invalid input unchanged. Escaped
  tag names, author names, author bios and post titles in the templates.
* PHP 8.5: replaced deprecated case statements and get_page_by_title(), guarded undefined array keys and null
  dereferences. PHPCompatibility reports no errors at PHP 8.5.
* WordPress 7.0: fixed the _load_textdomain_just_in_time notice that fired on every request, removed a term query
  that ran on every request, and added support for selective refresh widgets, responsive embeds, block styles,
  wide blocks and an editor stylesheet.
* Bootstrap: the theme shipped Bootstrap 3.3.7 CSS with Bootstrap 4.0.0 JavaScript. Both are now 3.4.1, which also
  addresses CVE-2019-8331.
* Fixed a stray closing tag in the slider, a slider image size that was never registered, widget fields that
  rendered no id attribute, and the Popular Posts widget losing its fallback image on save.
* Performance: theme scripts moved to the footer and no longer depend on jQuery, assets are versioned from the
  theme version, and Google Fonts load over https with display=swap and preconnect hints.

= 2.4.11 =
* Tested up to WordPress 6.8.
