# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

Sparkling — a free classic (non-block) **WordPress theme** by Colorlib, distributed on WordPress.org (slug `sparkling`) and licensed GPLv2+. It is a PHP theme derived from Underscores (`_s`), built on **Bootstrap 3.4.1** (CSS and JS) with Font Awesome 7.3.1 (a subset by default). `style.css` is hand-edited; there is **no SCSS pipeline**. Text domain: `sparkling`.

Do not migrate it to Bootstrap 5: users run plugins that emit Bootstrap 3 markup and rely on the theme's Bootstrap 3 (c8908ab reverted a Bootstrap 5 swap for that reason).

## Commands

```bash
npm ci                # install the pinned JS/CSS toolchain
composer install      # install the pinned PHPCS, WPCS and PHPCompatibilityWP

npm run build         # produce sparkling.zip (runs i18n:check and version:check first)
npm run verify        # lint everything + text domain + version check — the pre-commit gate
npm run lint          # eslint + stylelint + php -l + phpcs, in parallel
npm run lint:fix      # auto-fix what eslint/stylelint/phpcbf can
npm run i18n          # regenerate languages/sparkling.pot (WP-CLI on PATH, or WP_CLI=/path/to/wp)
npm run i18n:check    # verify every gettext call uses the 'sparkling' text domain
npm run version:check # style.css, readme.txt (Stable tag + changelog), changelog.txt, package.json agree
npm run images        # report optimisable PNG/JPEG; add -- --write to apply

composer lint         # vendor/bin/phpcs — the whole theme, zero errors and warnings
composer fix          # vendor/bin/phpcbf
python3 .github/build-icon-subset.py . assets sparkling   # rebuild the Font Awesome subset (needs fontTools)
```

Grunt, JSCS, JSHint and Travis were removed in 2.7.0. The scripts in `tools/` are plain Node ESM. PHPCS runs with `phpcs.xml.dist` (WordPress-Extra, the `sparkling` prefix and text domain, PHP 7.4+ compatibility).

Every linter is held at **zero** warnings: ESLint and Stylelint run with `--max-warnings 0` and CI fails on any PHPCS finding. A deliberate exception is annotated in place with its reason (`phpcs:ignore … -- reason` / `stylelint-disable-next-line … -- reason`), never by loosening a config.

CI is GitHub Actions (`.github/workflows/ci.yml`): `php -l` on PHP 7.4–8.5, PHPCS, ESLint, Stylelint, the text domain check, and a build that uploads `sparkling.zip` as an artifact.

There is no test suite. Development needs a real WordPress install — symlink this directory as `wp-content/themes/sparkling`. Release 2.7.0 was verified on **WordPress 7.1.2 / PHP 8.5** with `WP_DEBUG_LOG` clean, in a browser at 375/768/1280px, on jQuery 3.7.1 and on **jQuery 4.0.0 without Migrate**, with WooCommerce 11 active, and with axe-core for accessibility. Static analysis alone kept missing real defects here (a fatal on a `%` in an archive title, video posts rendering empty, the mobile menu dead on jQuery 4, 404s laid out as shop pages): load the pages and read the browser console and the debug log.

## Versioning

The version lives in **five** places, enforced by `npm run version:check` (and so by `npm run build`): `style.css` header (authoritative for WordPress), `readme.txt` Stable tag, the first `= x.y.z =` entry in `readme.txt`'s `== Changelog ==`, the first `### x.y.z` heading of `changelog.txt` (rendered by Appearance → About Sparkling → Changelog), and `package.json`. `SPARKLING_VERSION` is read from the `style.css` header in `functions.php` and cache-busts every theme asset — never hardcode a version in an enqueue for theme-owned files (vendor files carry their library version, e.g. `3.4.1-sparkling.1`).

## Architecture

### Two option stores — the biggest gotcha

Settings live in **one of two places**, and reading the wrong one silently returns the default:

1. The serialized **`sparkling` option** (legacy Options Framework style) — almost everything. Customizer settings are declared as `'sparkling[key]'` with `'type' => 'option'` and read with `of_get_option( 'key', $fallback )` (`functions.php`).
2. **Theme mods** — only `sparkling_excerpts` and `sparkling_page_comments`, read with `get_theme_mod()`.

Checkbox values may be stored as `true`, `1`, `'1'` or `''` depending on which release saved them: test them for truth, never `=== 1`. Never rename a key without a migration.

### Colours: inline CSS + theme.json palette

- `get_sparkling_theme_options()` (`inc/extras.php`, on `wp_head`) prints a `<style id="sparkling-theme-options">` built from the colour options — only when there is CSS to print. Every colour goes through **`sparkling_css_color()`**, which re-validates at output time (old saves may hold arbitrary text). Keep both properties.
- `theme.json` (v2, so WordPress 6.0 reads it) holds the palette: `primary`, `primary-hover`, `button`, `button-hover`, `heading`, `body`, `background`, `light`, `dark`. `sparkling_theme_json_customizer_colors()` (`inc/block-editor.php`, on `wp_theme_json_data_theme`) writes Link / Link hover / Element / Element hover / Heading / Body text colours into it, so the editor's pickers and block buttons follow a site's colours. Only `styles.elements.button` is styled from theme.json; everything else is `style.css` + the inline CSS.
- Adding a colour option means: a control in `inc/customizer.php`, its CSS in `get_sparkling_theme_options()` via `sparkling_css_color()`, and — if blocks should follow it — a palette slug in `theme.json` plus its entry in `sparkling_theme_json_customizer_colors()`.
- The theme red is `#D83B4B` (4.52:1 on white). It was `#DA4453` (4.25:1) until 2.7.0; sites that saved a colour keep theirs.

### Layout resolution and the wrapper split

`get_layout_class()` (`functions.php`, pluggable) returns `side-pull-left` (right sidebar), `side-pull-right` (left sidebar), `no-sidebar` or `full-width`, by precedence: per-post `site_layout` meta (layout box in `inc/metaboxes.php`) → WooCommerce pages' `woo_site_layout` → the global `site_layout`. `is_it_woocommerce_page()` only matches a real post ID (a 404's `false` used to match). `sparkling_get_post_layout( $post )` (`inc/block-editor.php`) is the same answer for a given post, used to size the editor.

`#secondary` is always printed by `sidebar.php`; `no-sidebar`/`full-width` hide it with CSS. Wrapper divs are split across files, so templates must close what they open:

- `header.php` opens `#page` → `#content` → `.container.main-content-area` → `.row.<layout>` → `.main-content-inner`
- `sidebar.php` **closes `.main-content-inner`**, then prints `#secondary`
- `footer.php` closes the rest

A template that skips `get_sidebar()` must close `.main-content-inner` itself — see `page-fullwidth.php` and `template-blocks.php`. The full-width templates are listed once in `sparkling_full_width_templates()`; the column classes, `$content_width` (1038) and the editor width read it.

### Block editor

`inc/block-editor.php`: the palette filter above; `sparkling_editor_layout_sizes()` sizes the post editor like the rendered column (648px beside a sidebar or with No Sidebar, 1038px for Full Width and `page-fullwidth.php`, 1140px for `template-blocks.php`); and the `sparkling` pattern category. Patterns live in `/patterns/*.php` (auto-registered). Validate new pattern markup in the editor — every block from `wp.blocks.parse( content )` must be `isValid`. Wide/full blocks stay inside the boxed column everywhere except the Blocks template (where `.alignfull` breaks out to the window); `editor-style.css` mirrors that.

### Customizer

`inc/customizer.php` registers the `sparkling_main_options` panel. Own controls: `Sparkling_Customize_Toggle_Control` (`inc/class-sparkling-customize-toggle-control.php`, presentation only — validation belongs in the setting's `sanitize_callback`; `Epsilon_Control_Toggle` is a class_alias for child themes) and `Sparkling_Customize_Link_Section` (a section that is a link; its JS type is registered in `assets/js/customizer-controls.js`). Dependent controls use `active_callback` (e.g. `sparkling_slider_is_enabled()`), not jQuery show/hide. In JS use `wp.customize.bind( 'ready', fn )`; `wp.customize( id, fn )` with two arguments is a lookup with a callback.

`$site_layout` and `$options_categories` are globals filled on `init` (`sparkling_init_globals()`), not at parse time: translations before `init` trigger WordPress 6.7+'s `_load_textdomain_just_in_time` notice, and `get_categories()` at parse time queried on every request. `$options_categories` is only filled in admin and the Customizer preview.

### Navigation

`Sparkling_Bootstrap_Navwalker` (`inc/class-sparkling-bootstrap-navwalker.php`) builds the primary menu; `WP_Bootstrap_Navwalker` is only a back-compat `class_alias`, because plugins bundle a walker of that name (the old unconditional declaration was a fatal with them). Submenu toggles are `<button class="sparkling-dropdown caret">` with `aria-expanded`, handled in `assets/js/functions.js`; desktop (≥1024px) opens submenus on hover/focus-within. A Title Attribute that is an icon class list (`sparkling_navwalker_is_icon_class()`, `inc/extras.php`) is printed as an icon. The social menu (`sparkling_social_icons()`) can print several times per page: the first copy keeps `id="menu-social"`, later ones are numbered, and CSS matches `:is(#menu-social, .menu-social)` to keep id specificity.

### Font Awesome

`inc/fontawesome.php`: by default only `assets/css/fontawesome/subset/` loads (the glyphs the theme renders). The complete set, `all.min.css` (with the v4 shim), loads when a nav menu (label, Title Attribute or CSS Classes) uses an icon outside the subset — cached in the `sparkling_fontawesome_scan` option until a menu is saved — or when Content Options → "Font Awesome icons" says so; the `sparkling_full_fontawesome` filter overrides both. `fontawesome.min.css`, `solid.min.css` and `brands.min.css` are only inputs to `.github/build-icon-subset.py` and are excluded from the zip. **Adding an icon the theme renders (template markup or a `content:` codepoint in style.css) means rebuilding the subset.**

### JavaScript

- `assets/js/functions.js` — plain DOM, no jQuery dependency: core-markup Bootstrap classes, scroll-to-top, submenu toggles, the menu button's `aria-expanded` (via Bootstrap's jQuery events when jQuery is present), and the sticky header (keeps the fixed navbar below the admin bar, re-measures on resize, sets `scroll-padding-top`). `window.SparklingIsMobile` / `window.generateMobileMenu` stay global for child themes.
- `assets/js/vendor/bootstrap(.min).js` is Bootstrap 3.4.1 **patched** (`3.4.1-sparkling.1`) to accept jQuery 4 and to only regex-test string options. `assets/js/jquery-compat.js` restores what jQuery 4 removed (`$.camelCase`, `$.isFunction`, `$.isWindow`, `$.type`, event aliases…); it is a guarded no-op on jQuery 3 and a dependency of Bootstrap and FlexSlider. Don't use jQuery event shorthands or `$(window).load()` in theme JS.
- `assets/js/flexslider-custom.js` starts the slider on `load` (or immediately if already loaded) and names the arrows. FlexSlider assets load only on the front page with the slider on.
- To test jQuery 4: a throwaway mu-plugin that re-registers `jquery-core` as 4.0.0 and deregisters `jquery-migrate`.

### Admin

`inc/welcome-screen/`: Appearance → About Sparkling (`Sparkling_Welcome`, one instance in `$sparkling_welcome`): Getting Started, Recommended Plugins (core plugin-card markup; plugin files found with `get_plugins()`; buttons only for users with `install_plugins`/`activate_plugins`), Changelog (renders `changelog.txt`), Support. The recommended-plugins list is data in `welcome-page-setup.php`. Every AJAX handler needs a capability check **and** `check_ajax_referer()`; never act on `$_GET` from `admin_init` (it runs for unauthenticated `admin-ajax.php` requests).

### Other pieces

- Templates: `index.php`/`archive.php`/`search.php` → `template-parts/content-{format}.php` (`content.php` fallback; `content-video.php` prints the post's embed unescaped on purpose — `wp_kses_post()` strips iframes). `image.php` is the image attachment template. One h1 per view: the site name is the h1 only on the latest-posts front page.
- Widgets (`inc/widgets/`): Sparkling Social, Popular Posts (default-image picker in `assets/js/widget.js`, AJAX `sparkling_get_attachment_media` with nonce + capability), Categories. Seven widget areas: `sidebar-1`, `home-widget-1..3`, `footer-widget-1..3`.
- WooCommerce: support in `sparkling_woo_setup()`; no template overrides.
- `custom_password_form()`, `get_sparkling_theme_options()`, `of_get_option()`, `get_layout_class()`, `is_it_woocommerce_page()`, `get_woocommerce_page_id()` keep unprefixed names for child themes, with annotated `phpcs:ignore`s.
- `wpml-config.xml` lists the user-entered option keys WPML should translate; add new ones there.

## Conventions

- Prefix everything `sparkling_` / `Sparkling_`; wrap template-level functions in `if ( ! function_exists() ) :` so child themes can override them. Template variables are globals — prefix them too (`$sparkling_…`).
- Escape late; colours through `sparkling_css_color()`. Output that must stay unescaped carries a `phpcs:ignore` with the reason.
- Strict comparisons; checkbox options tested for truth.
- JS: ES2022, single quotes, curly braces, `eqeqeq` (`eslint.config.mjs`); new localized objects go in its `globals`.
- After changing `style.css`, compare screenshots/computed styles before and after (375 and 1280px at least) rather than trusting a refactor is neutral. Keep text at WCAG AA (axe-core).
- LF line endings everywhere except three legacy `.po` files.

## Repo quirks worth knowing

- `tools/build-zip.mjs` is deny-by-default: a file ships unless `EXCLUDE` matches it; every hidden file is excluded. Inspect the zip after adding top-level files.
- `.gitignore` anchors `/vendor/`: unanchored, it once matched `assets/js/vendor/` and Bootstrap/FlexSlider went untracked.
- `readme.md` is the GitHub readme (not shipped); `readme.txt` is WordPress.org's.
- `set_tags.sh` is a leftover from Travis; it is not run by CI and is not shipped.
- `languages/` holds `.po`/`.mo` pairs for 20+ locales plus `sparkling.pot` (`npm run i18n`).
- A diverged local copy at `../sparkling` (branch `main`) holds the released 2.6.11 as shipped; this repository is the source of truth from 2.7.0.
