=== Sparkling ===

Contributors: colorlib
Requires at least: 6.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 2.7.1
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

Bootstrap Glyphicons Halflings (bundled with Bootstrap 3.4.1)
    Copyright Jan Kovarik
    License: MIT, as part of Bootstrap
    Source: https://glyphicons.com/

FlexSlider v2.7.2
    Copyright 2012 WooThemes
    License: GNU General Public License v2
    Source: https://github.com/woocommerce/FlexSlider

Font Awesome Free v7.3.1 (complete set, version 4 shim, and the theme's subset)
    Copyright Fonticons, Inc.
    License: Icons CC BY 4.0, Fonts SIL OFL 1.1, Code MIT
    Source: https://fontawesome.com/license/free

Open Sans
    Copyright 2020 The Open Sans Project Authors
    License: SIL Open Font License 1.1
    Source: https://fonts.google.com/specimen/Open+Sans

Roboto Slab
    Copyright 2018 The Roboto Slab Project Authors
    License: Apache License 2.0
    Source: https://fonts.google.com/specimen/Roboto+Slab

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

= 2.7.1 =
* Fixed keyboard focus reaching invisible slider links. Inactive slides are hidden from assistive technologies and their original tab order is restored when they become active.
* Added a slideshow pause/play button, stopped automatic rotation on keyboard focus and hover, and honoured reduced motion. Slider controls support the Space key, stay clickable with mixed-height slides, and announce their purpose and current slide.
* Fixed Escape leaving desktop submenus visible while their buttons reported them collapsed. Hover, keyboard focus and buttons share the same menu state. Nested submenu buttons support Space and Escape closes only the nearest submenu.
* Darkened the default link hover/focus colour to meet text contrast requirements on light backgrounds. Saved custom colours are retained.
* Content-hashed the subset icon fonts so a cached font from an earlier release cannot hide newly added icons.
* Added frontend regression tests on jQuery 3.7 and 4.0 and restricted theme archives to runtime files.

= 2.7.0 =
* Fixed two fatal errors on PHP 8: an Archive Pages headline containing a % other than %s ("Tagged %s at 100%") took the archive page down, and so did a classic [caption] with a non-numeric width.
* Fixed the mobile menu and the front page slider on jQuery 4 (Bootstrap 3 refused to run, and the slider started through a jQuery API removed in 3.0). The theme works on jQuery 3.7 and on jQuery 4.0 without Migrate.
* Fixed video posts showing an empty box on the blog, archive and search pages: the embed was stripped by the escaping that ran over it.
* Fixed 404 pages and empty searches losing their sidebar to the WooCommerce layout when WooCommerce is active.
* Fixed a fatal "Cannot declare class WP_Bootstrap_Navwalker" with plugins that bundle a nav walker of that name. The theme's walker is Sparkling_Bootstrap_Navwalker; the old name remains an alias.
* Fixed the password form: it lost WordPress's redirect and "Invalid password" message, pre-filled the field with the search query and was broken up by automatic paragraphs.
* Fixed a JavaScript error on every in-page link (the obsolete skip-link focus script), and the sticky header sitting under the admin bar, going stale when the menu wrapped, and hiding anchor targets.
* Fixed the Customizer: the slider category and slide count disagreed with the site, invalid typography values were saved as nothing, the slider's settings fell out of step with its switch, the switches could not be used from the keyboard, the link sections were unstyled, and the theme hid the Customizer's "Change" theme button.
* Fixed the About page: FancyBox and other plugins with differently named files always showed "Install", install buttons were offered to users who could not install plugins, a hidden tab was a fatal error, and the changelog needed FTP credentials on some hosts.
* Fixed the Sparkling Categories widget breaking on category names with parentheses and ignoring "Enable Posts Count", empty image links in Popular Posts and post listings, duplicate ids from the social menu and search forms, and the dropdown colours and nested submenu arrow from 768px up.
* Added theme.json: the block editor gets the theme's palette, which follows your Customizer colours, block buttons use the theme's button colours, and the editor is as wide as the column the post renders in.
* Added four block patterns (call to action, features, about, testimonial) and a "Blocks (full width, no title)" page template to build pages from them.
* Added icons for X, Bluesky, Threads, Mastodon, TikTok, WhatsApp, Discord, Reddit, Twitch and Medium to the social menu, and a generic icon for any other link.
* Accessibility: text meets WCAG AA contrast (the theme red is now #D83B4B, a shade darker; saved colours are kept), links in text are underlined, every page has one h1, landmarks and icon links are named, submenu toggles and the back-to-top control are real buttons.
* Performance: the slider's files load only on the front page, an empty style block is no longer printed, and only the Font Awesome icons the theme uses load unless your content needs more.
* Tested up to WordPress 7.1 and PHP 8.5. Development tooling moved from Grunt to npm scripts with PHPCS, ESLint and Stylelint at zero warnings in CI.

= 2.6.11 =
* Font Awesome icons added to a menu by hand disappeared on update to 2.6.7 or later, which subsetted the bundled Font Awesome to the glyphs the theme draws itself. The theme now detects the icons used in your menus and loads the complete library when one of them needs it, so those menus recover on their own.
* Added "Font Awesome icons" under Content Options in the Customizer, for icons the menu scan cannot see -- a widget, post content, a page builder. The sparkling_full_fontawesome filter still overrides it.

= 2.6.10 =
* Fixed the Customizer failing to open with "Class Epsilon_Section_Recommended_Actions not found". The 2.6.x packages shipped an outdated inc/welcome-screen/welcome-page-setup.php that still used classes removed with the Epsilon framework.
* Restored Appearance > About Sparkling, which the same outdated file never registered.
* The About page now shows the Sparkling version, not the child theme's, and the header text colour is escaped again.
* The bundled Font Awesome subset was missing the rules that bind an icon class to a font face, so .fa-brands resolved to a face containing no brand glyphs and .fa-regular drew the solid variant.

= 2.6.6 =
* Replaced Font Awesome 5.0.11 with a self-hosted Font Awesome 7.3.1, woff2 only and with no v4/v5 shim. Bundled icon fonts drop from 1,828 KB to 332 KB.
* The social menu named the Font Awesome 5 families, which Font Awesome 7 does not declare; every one of those icons would have disappeared. All 26 rules now name the Font Awesome 7 families.
* The search button no longer uses a Bootstrap glyphicon, so Bootstrap's icon font is not requested.

= 2.6.5 =
* Security: Bootstrap's JavaScript was still 3.3.7 while the theme declared 3.4.1, so CVE-2019-8331 (cross-site scripting through the data-template attribute of tooltips and popovers) was never actually fixed -- only the stylesheet had been replaced. Both Bootstrap files are now stock 3.4.1.
* Security: FlexSlider moves to 2.7.2. 2.7.0 built its thumbnail navigation by concatenating data-thumb-alt, which comes from the attachment's alt text, into an HTML string.

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
