# Wowhead Tooltips Shortcode for WordPress

**Version:** 3.0.0
**Author:** Your Name
**Author URI:** https://your-website.com

The "Wowhead Tooltips Shortcode" plugin allows you to easily insert links to various game entities, spells, NPCs, quests, and much more from the Wowhead database into your WordPress site, complete with automatically appearing tooltips on hover. The plugin supports various World of Warcraft game versions (Retail, Classic, WotLK Classic, etc.) and multiple languages.

## Features

* Displays Wowhead tooltips for various in-game entities.
* Supports Retail, Classic (Era, SoD), TBC Classic, WotLK Classic, Cataclysm Classic, MoP Classic, PTR, and Beta game versions.
* Supports multiple languages, including English, Russian, German, French, Spanish, and others.
* Automatic language detection based on WordPress settings or explicit language specification in the shortcode.
* Ability to set custom text for links.
* Ability to change the color and style of links.
* Icons for links are automatically added by the Wowhead script (default size 'tiny').
* Retrieves object names from Wowhead's XML feed if not specified in the shortcode.
* Caches XML responses to improve performance and reduce load on Wowhead servers.
* Simple integration using the `[wowhead]` shortcode.

## Installation

1.  Upload the plugin folder (e.g., `wowhead-tooltips-shortcode`) to the `/wp-content/plugins/` directory of your WordPress site.
    * Or, upload the plugin's ZIP archive via the "Plugins" > "Add New" > "Upload Plugin" section in your WordPress admin panel.
2.  Activate the plugin through the "Plugins" menu in WordPress.

## Usage

The plugin provides a shortcode `[wowhead]` that can be inserted into posts, pages, or widgets on your site.

### Basic Syntax

[wowhead id="object_parameters"]

### Main Shortcode Attributes

* **`id`** (required): A string containing information about the Wowhead object and its parameters. Parameters within this string are separated by an ampersand (`&`).
    * **Object Type and ID:** For example, `item=19019` (item), `spell=33786` (spell), `quest=10129` (quest), `npc=16060` (NPC), etc. A comprehensive list of supported types can be found in the plugin's `edit.txt` file or Wowhead's documentation.
    * **`domain`**: (optional parameter within `id`) Defines the language and/or game version.
        * Examples: `domain=ru` (Russian Retail), `domain=classic` (English Classic), `domain=ru.wotlk` (Russian WotLK Classic), `domain=fr.cata` (French Cataclysm Classic).
        * If `domain` is not specified, the plugin will try to use your WordPress site's language (if supported for Retail Wowhead). If that's not possible, English for Retail will be used.
        * The plugin attempts to correct invalid formats, e.g., `classic.ru` will be processed as `ru.classic`.
    * **Additional Wowhead Parameters:** Can be added to specify the object further, e.g., `gems=...`, `ench=...`, `pcs=...` for items.
* **`name`** (optional): Allows you to set custom text for the displayed link.
    * Example: `name="My Favorite Sword"`
    * If `name` is not provided, the plugin will attempt to fetch the official object name from Wowhead's XML feed. If that also fails, a default name (e.g., "Item 19019") will be used.
    * When using the `name` attribute, the `renameLinks` option for the Wowhead script will be automatically set to `false` for the entire page to prevent your custom names from being overwritten.
* **`color`** (optional): Allows you to set the text color of the link.
    * Can be a CSS color name (e.g., `color="green"`), a HEX code (e.g., `color="#FF0000"`), or the name of a CSS class you've defined in your styles (e.g., `color="my-custom-color-class"`).
    * When using the `color` (or `style`) attribute, the `colorLinks` option for the Wowhead script will be automatically set to `false` for the entire page to prevent your custom colors from being overwritten by Wowhead's default link colors.
* **`style`** (optional): Allows you to apply custom inline CSS styles to the link.
    * Example: `style="font-weight: bold; text-decoration: underline;"`
    * Similar to the `color` attribute, using `style` will set `colorLinks: false` for the entire page.

**Note on Icons:** In this plugin version (`v3.0.0`), the icon size is set to `'tiny'` by default for all Wowhead links on the page. The `icon` attribute in the shortcode is **ignored** to simplify and ensure stable operation. Icons are added automatically by the Wowhead script if `iconizeLinks` is enabled (which is `true` by default).

### Usage Examples

#### 1. Basic Object Types (Default language and version - Retail, English, unless site language is Russian/German, etc.)

* **Item:**
    `[wowhead id="item=19019"]` (Displays Thunderfury)
* **Spell:**
    `[wowhead id="spell=33786"]` (Displays Cyclone)
* **Quest:**
    `[wowhead id="quest=10129"]`
* **Achievement:**
    `[wowhead id="achievement=621"]`
* **NPC:**
    `[wowhead id="npc=16060"]`
* **Zone:**
    `[wowhead id="zone=3703"]`

#### 2. Specifying Language (Retail Example)

* **Russian:**
    `[wowhead id="item=19019&domain=ru"]`
* **German:**
    `[wowhead id="item=19019&domain=de"]`
* **French:**
    `[wowhead id="item=19019&domain=fr"]`
* **Spanish:**
    `[wowhead id="item=19019&domain=es"]`

#### 3. Specifying Game Version (Default language - English, unless site language matches a supported version language)

* **World of Warcraft Classic (English):**
    `[wowhead id="item=19019&domain=classic"]`
* **WotLK Classic (English):**
    `[wowhead id="item=49623&domain=wotlk"]`
* **Cataclysm Classic (English):**
    `[wowhead id="item=71086&domain=cata"]`

#### 4. Combining Game Version and Language

* **Russian Classic:**
    `[wowhead id="item=19019&domain=ru.classic"]`
* **German WotLK Classic (corrected format):**
    `[wowhead id="item=49623&domain=de.wotlk"]` (the plugin will handle `wotlk.de` as `de.wotlk`)
* **French Cataclysm Classic:**
    `[wowhead id="item=71086&domain=fr.cata"]`

#### 5. Using `name`, `color`, `style` Attributes

* **Custom Link Name:**
    `[wowhead id="item=19019&domain=en" name="Legendary Sword!"]`
* **Green Link Color:**
    `[wowhead id="item=19019&domain=en" color="green"]`
* **Link Color via HEX and Bold Font:**
    `[wowhead id="item=19019&domain=en" color="#FF8C00" style="font-weight: bold;"]`
* **Custom CSS Class for Link:**
    `[wowhead id="item=19019&domain=en" color="my-custom-wowhead-link"]`
    *(You will need to define the style for `.my-custom-wowhead-link` in your theme's CSS).*
* **Full Example with Different Attributes:**
    `[wowhead id="spell=33786&domain=fr" name="Cyclone (FR)" style="text-transform: uppercase;" color="blue"]`

#### 6. Advanced Wowhead Parameters within `id`

You can pass additional Wowhead-specific parameters directly in the `id` string. For items, these can include gems, enchants, set bonuses, etc.

* **Item with Gem and Enchant (Example):**
    `[wowhead id="item=25697&domain=tbc&gems=23121&ench=2647"]`
* **Item with Set Bonuses (Example):**
    `[wowhead id="item=25697&domain=tbc&pcs=25695:25697"]`

#### 7. Empty or Invalid Shortcodes

* **Empty Shortcode `[wowhead]` or `[wowhead id=""]`:**
    Will display a simple link to the Wowhead main page (respecting site language if applicable).
* **Shortcode with Non-existent ID or Invalid Data:**
    `[wowhead id="item=999999999"]`
    Will display a link "Wowhead.com (unavailable)" in red.
* **Shortcode with Only Domain:**
    `[wowhead id="domain=ru.classic"]`
    Will display a link to the Wowhead main page for the specified domain.

## Language and Version Determination Priority

1.  **Explicit Specification in Shortcode:** The `domain` parameter in the `id` attribute has the highest priority.
2.  **WordPress Site Language:** If `domain` is not specified, the plugin uses your WordPress site language for the Retail version of Wowhead (if that language is supported).
3.  **English Default:** If none of the above methods are applicable, English for Retail Wowhead is used.

## Caching

The plugin caches responses from Wowhead's XML feeds (object names, 404 status) for 12 hours for successful requests and 1 hour for 404 errors. This helps reduce the number of requests to Wowhead's servers and speeds up your page loads.

## Troubleshooting

* **Tooltips Not Appearing:**
    1.  Ensure the plugin is active.
    2.  Ensure there is at least one `[wowhead]` shortcode on the page.
    3.  Check the page's HTML source (Ctrl+U or Cmd+Option+U): the lines `const whTooltips = {...};` and `<script src="https://wow.zamimg.com/widgets/power.js"></script>` should be present near the end of the page, before `</body>`.
    4.  Check your browser's JavaScript console (F12 -> Console) for errors.
    5.  Ensure your theme's `footer.php` file contains the `<?php wp_footer(); ?>` call.
    6.  Try temporarily disabling other plugins and switching to a default WordPress theme to rule out conflicts.
    7.  Clear all levels of cache (browser, WordPress plugins, server-side cache, CDN).
* **Incorrect Language or Game Version:** Carefully check the value of the `domain` parameter in your shortcode.

## Supported Entity List (Main types for `id`)

The plugin focuses on the main entities for which Wowhead provides tooltips. The most commonly used are:
`item`, `spell`, `quest`, `achievement`, `npc`, `zone`, `currency`, `faction`, `pet`, `itemset`, `skill`, `mount`, `outfit`, `event`, `object`, `transmog`, `affix`.

For more specific or rare entity types, please refer to Wowhead's documentation.

---

We hope this plugin is useful for your website!