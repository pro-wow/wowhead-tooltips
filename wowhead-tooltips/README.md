=== Wowhead Tooltips Shortcode ===
Contributors: Strogino
Donate link: https://pro-wow.ru/
Tags: wowhead, tooltips, shortcode, world of warcraft, wow
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 3.1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Easily display interactive Wowhead tooltips for WoW items. Look for all the details /wp-content/plugins/wowhead-tooltips/FAQ

== Description ==

***FAQ: Choose your language / Выберите ваш язык / 请选择您的语言 : /wp-content/plugins/wowhead-tooltips/FAQ for more details.***

The "Wowhead Tooltips Shortcode" plugin allows you to seamlessly integrate Wowhead's rich, interactive tooltips into your WordPress posts and pages. By using a simple shortcode `[wowhead]`, you can link to various game entities like items, spells, quests, achievements, NPCs, and zones from any World of Warcraft expansion, including Retail, Classic Era, WotLK Classic, Cataclysm Classic, and Mists of Pandaria Classic/Remix.

The plugin is designed for flexibility, offering support for numerous languages (e.g., English, Russian, German, French, Chinese, Korean) and game versions. It intelligently determines the language and version based on your WordPress settings or allows explicit control via the shortcode. Key features include custom link text, customizable link colors and styles, and automatic icon display (fixed at 'tiny' size for consistency). To ensure accuracy and performance, official entity names are fetched from Wowhead's XML feed and cached locally using WordPress Transients.

This tool is ideal for fan sites, guild pages, or any content creator looking to provide rich, contextual information from Wowhead directly within their content.

== Features ==

* Displays official Wowhead tooltips for a wide range of World of Warcraft entities.
* Supports multiple game versions:
    * Retail (Live)
    * Classic Era / Season of Discovery
    * The Burning Crusade Classic (TBC)
    * Wrath of the Lich King Classic (WotLK)
    * Cataclysm Classic (Cata)
    * Mists of Pandaria Classic (MoP) / Remix
    * Public Test Realm (PTR)
    * Beta servers
* Extensive language support, including English (default), Russian, German, French, Spanish, Italian, Portuguese, Korean, Simplified Chinese, and Traditional Chinese.
* Automatic language detection based on WordPress site settings, with an option to explicitly set language and game version per shortcode.
* Allows users to define custom link text.
* Allows users to customize link color and apply custom CSS styles.
* Icons for links are automatically added by the Wowhead script (default size is 'tiny' for all icons on the page).
* Fetches official names for entities from Wowhead's XML feed if not specified in the shortcode.
* Caches XML responses using WordPress Transients API to improve performance and reduce load on Wowhead servers.
* Simple integration via the `[wowhead]` shortcode.

== Installation ==

1.  **Upload via WordPress Admin:**
    * Download the plugin ZIP file from the [GitHub repository](https://github.com/pro-wow/wowhead-tooltips/).
    * In your WordPress admin panel, go to "Plugins" > "Add New".
    * Click "Upload Plugin" at the top.
    * Choose the downloaded ZIP file and click "Install Now".
    * Activate the plugin.
2.  **Manual Upload:**
    * Download and unzip the plugin package.
    * Upload the plugin folder (e.g., `wowhead-tooltips`) to the `/wp-content/plugins/` directory on your server.
    * Activate the plugin through the "Plugins" menu in WordPress.
3.  **Create `languages` folder:**
    * For internationalization, ensure a `languages` folder exists in the plugin's root directory (e.g., `/wp-content/plugins/wowhead-tooltips/languages/`). The plugin uses `wowhead-tooltips` as its text domain.

== Usage ==

The plugin provides the `[wowhead]` shortcode, which can be inserted into WordPress posts, pages, or widgets.

=== Basic Syntax ===

`[wowhead id="entity_parameters_string"]`

=== Shortcode Attributes ===

The shortcode accepts several attributes to customize its behavior and appearance:

* **`id`** (required):
    * A string containing information about the Wowhead entity and its specific parameters.
    * Parameters within this string are separated by an ampersand (`&`).
    * **Entity Type and ID:** Must include the type of entity and its numerical ID (e.g., `item=19019`, `spell=33786`, `quest=10129`).
    * **`domain`** (optional, within the `id` string): Specifies the language and/or game version for the tooltip.
        * Examples: `domain=ru` (Russian Retail), `domain=classic` (English Classic), `domain=de.wotlk` (German WotLK Classic).
        * If omitted, the plugin attempts to use the WordPress site's language for Retail Wowhead. If unsupported, it defaults to English Retail.
        * The plugin attempts to correct common misformats like `version.lang` to the expected `lang.version`.
    * **Additional Wowhead Parameters:** Other parameters recognized by Wowhead (e.g., `gems=...`, `ench=...`, `pcs=...` for items) can be included.
* **`name`** (optional):
    * Sets custom text for the displayed link.
    * Example: `name="My Awesome Item"`
    * If not provided, the plugin tries to fetch the official name via Wowhead's XML feed. If that fails, a generic name like "Item 19019" is used.
    * **Note:** Using this attribute will set the global `renameLinks` option in `whTooltips` to `false` for the page, preventing Wowhead's script from overriding custom link names.
* **`color`** (optional):
    * Sets the text color of the link.
    * Accepts CSS color names (e.g., `color="green"`), HEX codes (e.g., `color="#FF8C00"`), or a custom CSS class name defined in your theme's stylesheet (e.g., `color="my-custom-link-class"`).
    * **Note:** Using this attribute (or `style`) will set the global `colorLinks` option in `whTooltips` to `false` for the page, preventing Wowhead's script from applying its default quality colors.
* **`style`** (optional):
    * Applies custom inline CSS styles to the link.
    * Example: `style="font-weight: bold; text-decoration: underline;"`
    * Using this also sets `colorLinks: false` globally for the page.

**Important Note on Icons:**
In this version of the plugin, the `iconSize` for all Wowhead tooltips on a page is fixed to `'tiny'`. The `icon` attribute in the shortcode is **ignored** for setting `iconSize`. Icons are automatically added by Wowhead's `power.js` script if `iconizeLinks` is enabled in the `whTooltips` object (which it is by default).

=== How `whTooltips` Global Options are Determined by the Plugin ===

The plugin configures the global `whTooltips` JavaScript object based on the attributes used in *any* `[wowhead]` shortcode on the current page:

* `iconSize`: Always set to `'tiny'`.
* `renameLinks`: Set to `false` if any shortcode on the page uses the `name` attribute; otherwise, `true`.
* `colorLinks`: Set to `false` if any shortcode on the page uses the `color` or `style` attribute; otherwise, `true`.
* `iconizeLinks`: Always set to `true`.

=== Examples ===

==== 1. Basic Object Types ====
(Default language/version: Retail English, unless site language is a supported Wowhead retail language)

* **Item:**
    `[wowhead id="item=19019"]`
* **Spell:**
    `[wowhead id="spell=33786"]`
* **Quest:**
    `[wowhead id="quest=10129"]`

==== 2. Specifying Language (Retail Example) ====

* **Russian:**
    `[wowhead id="item=19019&domain=ru"]`
* **German:**
    `[wowhead id="item=19019&domain=de"]`

==== 3. Specifying Game Version (Default language: English) ====

* **World of Warcraft Classic:**
    `[wowhead id="item=19019&domain=classic"]`
* **WotLK Classic:**
    `[wowhead id="item=49623&domain=wotlk"]`

==== 4. Combining Game Version and Language ====

* **Russian Classic:**
    `[wowhead id="item=19019&domain=ru.classic"]`
* **German WotLK Classic:**
    `[wowhead id="item=49623&domain=de.wotlk"]`

==== 5. Using `name`, `color`, `style` Attributes ====

* **Custom Link Name:**
    `[wowhead id="item=19019&domain=en" name="Legendary Sword!"]`
* **Green Link Color:**
    `[wowhead id="item=19019&domain=en" color="green"]`
* **Link Color via HEX and Bold Font:**
    `[wowhead id="item=19019&domain=en" color="#FF8C00" style="font-weight: bold;"]`
* **Custom CSS Class for Link:**
    `[wowhead id="item=19019&domain=en" color="my-custom-wowhead-link"]`
    *(You will need to define the style for `.my-custom-wowhead-link` in your theme's CSS).*

==== 6. Advanced Wowhead Parameters within `id` ====

You can pass additional Wowhead-specific parameters directly in the `id` string. For items, these can include gems, enchants, set bonuses, etc.

* **Item with Gem and Enchant (Example):**
    `[wowhead id="item=25697&domain=tbc&gems=23121&ench=2647"]`

==== 7. Empty or Invalid Shortcodes ====

* **Empty Shortcode `[wowhead]` or `[wowhead id=""]`:**
    Will display a simple link to the Wowhead main page (respecting site language if applicable).
* **Shortcode with Non-existent ID or Invalid Data:**
    `[wowhead id="item=999999999"]`
    Will display a link "Wowhead.com (unavailable)" in red.
* **Shortcode with Only Domain:**
    `[wowhead id="domain=ru.classic"]`
    Will display a link to the Wowhead main page for the specified domain.

== Language and Version Determination Priority ==

1.  **Explicit Specification in Shortcode:** The `domain` parameter in the `id` attribute has the highest priority.
2.  **WordPress Site Language:** If `domain` is not specified, the plugin uses your WordPress site language for the Retail version of Wowhead (if that language is supported).
3.  **English Default:** If none of the above methods are applicable, English for Retail Wowhead is used.

== Caching ==

The plugin caches responses from Wowhead's XML feeds (object names, 404 status) for 12 hours for successful requests and 1 hour for 404 errors using the WordPress Transients API. This helps reduce the number of requests to Wowhead's servers and speeds up your page loads.

== Troubleshooting ==

* **Tooltips Not Appearing / Icons Missing:**
    1.  Ensure the plugin is active.
    2.  Ensure there is at least one `[wowhead]` shortcode on the page.
    3.  Check the page's HTML source (Ctrl+U or Cmd+Option+U): the lines `const whTooltips = {...};` and `<script src="https://wow.zamimg.com/widgets/power.js"></script>` should be present near the end of the page, before `</body>`.
    4.  Check your browser's JavaScript console (F12 -> Console) for errors. Any JS errors can prevent Wowhead scripts from initializing.
    5.  Ensure your theme's `footer.php` file contains the `<?php wp_footer(); ?>` call before the closing `</body>` tag.
    6.  Try temporarily disabling other plugins and switching to a default WordPress theme (like Twenty Twenty-Three) to rule out conflicts.
    7.  Clear all levels of cache (browser, WordPress plugins, server-side cache, CDN).
* **Incorrect Language or Game Version:** Carefully check the value of the `domain` parameter in your shortcode.
* **PHP Errors:** If the plugin fails to activate or causes issues, enable WordPress debugging (`WP_DEBUG`, `WP_DEBUG_LOG` in `wp-config.php`) to check for PHP errors in `wp-content/debug.log`.

== Contributing ==

Contributions are welcome! Please feel free to fork the repository on [GitHub](https://github.com/pro-wow/wowhead-tooltips/), make changes, and submit pull requests.

== Changelog ==

= 3.1.1 =
* Updated plugin header for WordPress.org compatibility (PCP plugin check).
* Added English comments throughout the PHP code (for development version).
* Set "Tested up to" WordPress version to 6.8.
* Standardized plugin name to "Wowhead Tooltips Shortcode".
* Corrected `Stable tag` to match plugin version.
* Shortened short description to meet 150 character limit.

= 3.0.0 =
* Initial stable version with direct script output for Wowhead JS.
* XML Caching enabled via Transients API.
* `iconSize` fixed to 'tiny' globally.
* `renameLinks` and `colorLinks` determined globally based on shortcode attributes.
* Comprehensive domain and language handling.