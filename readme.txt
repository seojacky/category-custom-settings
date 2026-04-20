=== Category Custom Settings ===
Contributors: seojacky
Tags: category, custom fields, term meta, taxonomy
Requires at least: 5.6
Tested up to: 6.7
Stable tag: 1.3.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds custom settings fields for WordPress categories and provides helper functions for frontend output.

== Description ==

Category Custom Settings adds additional fields to the category edit screen in WordPress admin and stores them using the standard `term_meta` API.

**Fields available for all categories:**

* `cat_add_description` — extra description shown below the post listing

**Fields available for parent categories only:**

* `cat_version` — game version
* `cat_video` — YouTube video ID
* `bg_category` — category background colour
* `cat_rss` — news RSS feed URL
* `cat_leftlink` — left navigation link URL
* `cat_rightlink` — right navigation link URL
* `cat_adsense1` — AdSense code block
* `cat_headerbanner` — HTML banner code for the category header

**Key features:**

* Stores data via `term_meta` (the modern WordPress approach)
* Works only with the `category` taxonomy
* Extended fields are shown only for top-level (parent) categories
* Save operations are protected by a nonce
* User capability check (`manage_categories`)
* Per-field sanitisation (`sanitize_text_field`, `esc_url_raw`, `wp_kses_post`, `wp_kses`)
* Ready-to-use template functions: `ccs_get_category_field()` and `ccs_the_category_field()`

== Installation ==

1. Upload the `category-custom-settings` folder to `/wp-content/plugins/`.
2. Activate the plugin through **Plugins → Installed Plugins** in WordPress admin.
3. Go to **Posts → Categories**, edit any category, and the custom fields will appear.

== Frequently Asked Questions ==

= How do I display a field value in my theme? =

Use `ccs_get_category_field( 'field_name' )` to retrieve the value, or `ccs_the_category_field( 'field_name' )` to echo it with the appropriate escaping applied automatically.

= Are the code/banner fields safe? =

`cat_adsense1` and `cat_headerbanner` are restricted to users who have the `unfiltered_html` capability (administrators on single-site installations). The values are stored and output through `wp_kses()` with an extended allowlist that includes `<script>`, `<iframe>`, and `<ins>` tags.

== Changelog ==

= 1.3.0 =
* Fixed: `ccs_the_category_field()` — code/banner fields (`cat_adsense1`, `cat_headerbanner`) were not shown to frontend visitors because of an incorrect `current_user_can('unfiltered_html')` check on output; data is already sanitized on save.
* Performance: added `get_term_meta( $term_id )` cache primer in `render_edit_fields()` — reduces 9 separate DB queries to 1.
* Performance: `ccs_get_code_allowed_html()` now uses a `static` cache to avoid rebuilding the allowed-HTML array on every call.
* Performance: `get_fields_config()` now uses a `static` cache.
* Standards: added `Text Domain: category-custom-settings` to plugin header.
* Standards: all admin UI strings wrapped in `esc_html_e()` for i18n readiness.
* Standards: added `load_textdomain()` method hooked on `init`.
* Standards: removed deprecated `valign="top"` HTML attributes from form rows.
* Standards: plugin class instantiated on `plugins_loaded` hook instead of bare file scope.

= 1.2.0 =
* Fixed PHPCS: output of code/banner fields now uses `wp_kses()` with an explicit allowlist instead of bare `echo`.
* Fixed PHPCS: added inline ignore comment for `$_POST['ccs_fields']` array assignment; sanitisation is done per-field in the loop.
* Fixed PHPCS: pass plugin version to `wp_register_style()` to prevent browser caching issues.
* Removed legacy migration code (`maybe_migrate_legacy_data`, `LEGACY_OPTION_PREFIX`, legacy fallback in `ccs_get_category_field`).
* Extracted `ccs_get_code_allowed_html()` as a shared public function.
* Added `readme.txt` for WordPress plugin directory compliance.

= 1.1.0 =
* Refactored to use `term_meta` for all field storage.
* Added nonce verification and capability checks.
* Added `ccs_get_category_field()` and `ccs_the_category_field()` template functions.
* Improved sanitisation with per-field type handling.

== Upgrade Notice ==

= 1.1.0 =
Initial public release.
