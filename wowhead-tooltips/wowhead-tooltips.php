<?php
/**
 * Plugin Name: Wowhead Tooltips Shortcode
 * Plugin URI: https://github.com/pro-wow/wowhead-tooltips/
 * Description: Displays Wowhead tooltips via the [wowhead] shortcode. Look for all the details /wp-content/plugins/wowhead-tooltips/FAQ .
 * Version: 3.1.1
 * Author: Strogino (Vladislav)
 * Author URI: https://pro-wow.ru/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wowhead-tooltips
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 5.0
 * Tested up to: 6.8.1
 */

// Exit if accessed directly to prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

/**
 * Main class for the Wowhead Tooltips Shortcode plugin.
 * Handles shortcode registration, script output, and all related logic.
 */
class Wowhead_Tooltips_Core_V3_1_1 {

    // Singleton instance of the class.
    private static $instance;

    // Static properties to store settings aggregated from all shortcodes on a page.
    // These settings determine how the global `whTooltips` JavaScript object will be configured.
    private static $shortcode_settings = [
        'has_custom_name'           => false, // True if any shortcode on the page uses the 'name' attribute.
        'has_custom_style_or_color' => false, // True if any shortcode uses 'color' or 'style' attributes.
        // 'icon_sizes' array is no longer needed as iconSize will be fixed to 'tiny'.
        'assets_needed_for_page'    => false, // True if at least one [wowhead] shortcode is present on the page, signaling that scripts should be outputted.
    ];

    /**
     * Gets the singleton instance of this class.
     * Ensures that only one instance of the class is loaded.
     * @return Wowhead_Tooltips_Core_V3_1_1
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Private constructor to prevent direct instantiation.
     * Sets up WordPress hooks for the plugin's functionality.
     */
    private function __construct() {
        // Hook for loading the plugin's text domain for internationalization.
        add_action( 'init', [ $this, 'load_textdomain' ] );
        // Register the [wowhead] shortcode and its handler function.
        add_shortcode( 'wowhead', [ $this, 'shortcode_handler' ] );
        // Hook into 'wp_footer' to directly output necessary JavaScript before the closing </body> tag.
        // Using a high priority (999) to ensure it outputs late in the footer, after most other items.
        add_action( 'wp_footer', [ $this, 'direct_output_scripts_in_footer' ], 999 );
        // Hook into 'wp' action (runs after WordPress environment is set up but before headers are sent)
        // to reset page-specific flags for each new request, ensuring a clean state.
        add_action('wp', [$this, 'reset_settings_flags_on_wp_action']);
    }
    
    /**
     * Resets the static $shortcode_settings flags at the beginning of each WordPress request
     * via the 'wp' action hook. This ensures that settings from a previous page load or
     * context do not affect the current one.
     */
    public function reset_settings_flags_on_wp_action() {
        self::$shortcode_settings = [
            'has_custom_name'           => false,
            'has_custom_style_or_color' => false,
            'assets_needed_for_page'    => false, // Reset the flag indicating if assets are needed.
        ];
    }

    /**
     * Loads the plugin's text domain for localization.
     * This allows translating strings within the plugin (e.g., error messages)
     * if .mo language files are provided in the path specified by 'Domain Path'.
     */
    public function load_textdomain() {
        load_plugin_textdomain( 
            'wowhead-tooltips', // Unique text domain name, must match the one in the plugin header.
            false,             // Deprecated argument, set to false.
            dirname( plugin_basename( __FILE__ ) ) . '/languages' // Path to the .mo translation files.
        );
    }

    /**
     * Returns the map of Wowhead domains, their base URL paths, language codes, 
     * and script domain contexts expected by power.js.
     * This data is crucial for constructing correct URLs and data-wowhead attributes.
     * @return array The domain map.
     */
    private function get_domain_map() { 
        // This map is based on observed Wowhead URL structures and power.js behavior.
        // 'key' (e.g., 'ru', 'classic', 'ru.classic') is used for lookups.
        // 'base_path': Path segment for Wowhead URLs (e.g., 'ru/', 'classic/').
        // 'lang_code': The ISO language code part (e.g., 'ru', 'en').
        // 'script_domain': The domain context value expected by Wowhead's power.js.
        return [
            // Retail (Live) - Default English has an empty base_path and script_domain
            'en'    => ['base_path' => '', 'lang_code' => 'en', 'script_domain' => ''],
            'ru'    => ['base_path' => 'ru/', 'lang_code' => 'ru', 'script_domain' => 'ru'],
            'de'    => ['base_path' => 'de/', 'lang_code' => 'de', 'script_domain' => 'de'],
            'es'    => ['base_path' => 'es/', 'lang_code' => 'es', 'script_domain' => 'es'],
            'fr'    => ['base_path' => 'fr/', 'lang_code' => 'fr', 'script_domain' => 'fr'],
            'it'    => ['base_path' => 'it/', 'lang_code' => 'it', 'script_domain' => 'it'],
            'pt'    => ['base_path' => 'pt/', 'lang_code' => 'pt', 'script_domain' => 'pt'], // pt-br
            'ko'    => ['base_path' => 'ko/', 'lang_code' => 'ko', 'script_domain' => 'ko'],
            'cn'    => ['base_path' => 'cn/', 'lang_code' => 'cn', 'script_domain' => 'cn'], // Simplified Chinese
            'tw'    => ['base_path' => 'tw/', 'lang_code' => 'tw', 'script_domain' => 'tw'], // Traditional Chinese
            
            // Classic Era
            'classic'        => ['base_path' => 'classic/', 'lang_code' => 'en', 'script_domain' => 'classic'],
            'ru.classic'     => ['base_path' => 'classic/ru/', 'lang_code' => 'ru', 'script_domain' => 'ru.classic'],
            'de.classic'     => ['base_path' => 'classic/de/', 'lang_code' => 'de', 'script_domain' => 'de.classic'],
            'es.classic'     => ['base_path' => 'classic/es/', 'lang_code' => 'es', 'script_domain' => 'es.classic'],
            'fr.classic'     => ['base_path' => 'classic/fr/', 'lang_code' => 'fr', 'script_domain' => 'fr.classic'],
            'it.classic'     => ['base_path' => 'classic/it/', 'lang_code' => 'it', 'script_domain' => 'it.classic'],
            'pt.classic'     => ['base_path' => 'classic/pt/', 'lang_code' => 'pt', 'script_domain' => 'pt.classic'],
            'ko.classic'     => ['base_path' => 'classic/ko/', 'lang_code' => 'ko', 'script_domain' => 'ko.classic'],
            'cn.classic'     => ['base_path' => 'classic/cn/', 'lang_code' => 'cn', 'script_domain' => 'cn.classic'],
            'tw.classic'     => ['base_path' => 'classic/tw/', 'lang_code' => 'tw', 'script_domain' => 'tw.classic'],

            // The Burning Crusade Classic
            'tbc'            => ['base_path' => 'tbc/', 'lang_code' => 'en', 'script_domain' => 'tbc'],
            'ru.tbc'         => ['base_path' => 'tbc/ru/', 'lang_code' => 'ru', 'script_domain' => 'ru.tbc'],
            // ... other tbc languages ...
            'de.tbc'         => ['base_path' => 'tbc/de/', 'lang_code' => 'de', 'script_domain' => 'de.tbc'],
            'es.tbc'         => ['base_path' => 'tbc/es/', 'lang_code' => 'es', 'script_domain' => 'es.tbc'],
            'fr.tbc'         => ['base_path' => 'tbc/fr/', 'lang_code' => 'fr', 'script_domain' => 'fr.tbc'],
            'it.tbc'         => ['base_path' => 'tbc/it/', 'lang_code' => 'it', 'script_domain' => 'it.tbc'],
            'pt.tbc'         => ['base_path' => 'tbc/pt/', 'lang_code' => 'pt', 'script_domain' => 'pt.tbc'],
            'ko.tbc'         => ['base_path' => 'tbc/ko/', 'lang_code' => 'ko', 'script_domain' => 'ko.tbc'],
            'cn.tbc'         => ['base_path' => 'tbc/cn/', 'lang_code' => 'cn', 'script_domain' => 'cn.tbc'],
            'tw.tbc'         => ['base_path' => 'tbc/tw/', 'lang_code' => 'tw', 'script_domain' => 'tw.tbc'],

            // Wrath of the Lich King Classic
            'wotlk'          => ['base_path' => 'wotlk/', 'lang_code' => 'en', 'script_domain' => 'wotlk'],
            'ru.wotlk'       => ['base_path' => 'wotlk/ru/', 'lang_code' => 'ru', 'script_domain' => 'ru.wotlk'],
            // ... other wotlk languages ...
            'de.wotlk'       => ['base_path' => 'wotlk/de/', 'lang_code' => 'de', 'script_domain' => 'de.wotlk'],
            'es.wotlk'       => ['base_path' => 'wotlk/es/', 'lang_code' => 'es', 'script_domain' => 'es.wotlk'],
            'fr.wotlk'       => ['base_path' => 'wotlk/fr/', 'lang_code' => 'fr', 'script_domain' => 'fr.wotlk'],
            'it.wotlk'       => ['base_path' => 'wotlk/it/', 'lang_code' => 'it', 'script_domain' => 'it.wotlk'],
            'pt.wotlk'       => ['base_path' => 'wotlk/pt/', 'lang_code' => 'pt', 'script_domain' => 'pt.wotlk'],
            'ko.wotlk'       => ['base_path' => 'wotlk/ko/', 'lang_code' => 'ko', 'script_domain' => 'ko.wotlk'],
            'cn.wotlk'       => ['base_path' => 'wotlk/cn/', 'lang_code' => 'cn', 'script_domain' => 'cn.wotlk'],
            'tw.wotlk'       => ['base_path' => 'wotlk/tw/', 'lang_code' => 'tw', 'script_domain' => 'tw.wotlk'],

            // Cataclysm Classic
            'cata'           => ['base_path' => 'cata/', 'lang_code' => 'en', 'script_domain' => 'cata'],
            'ru.cata'        => ['base_path' => 'cata/ru/', 'lang_code' => 'ru', 'script_domain' => 'ru.cata'],
            // ... other cata languages ...
            'de.cata'        => ['base_path' => 'cata/de/', 'lang_code' => 'de', 'script_domain' => 'de.cata'],
            'es.cata'        => ['base_path' => 'cata/es/', 'lang_code' => 'es', 'script_domain' => 'es.cata'],
            'fr.cata'        => ['base_path' => 'cata/fr/', 'lang_code' => 'fr', 'script_domain' => 'fr.cata'],
            'it.cata'        => ['base_path' => 'cata/it/', 'lang_code' => 'it', 'script_domain' => 'it.cata'],
            'pt.cata'        => ['base_path' => 'cata/pt/', 'lang_code' => 'pt', 'script_domain' => 'pt.cata'],
            'ko.cata'        => ['base_path' => 'cata/ko/', 'lang_code' => 'ko', 'script_domain' => 'ko.cata'],
            'cn.cata'        => ['base_path' => 'cata/cn/', 'lang_code' => 'cn', 'script_domain' => 'cn.cata'],
            'tw.cata'        => ['base_path' => 'cata/tw/', 'lang_code' => 'tw', 'script_domain' => 'tw.cata'],
            
            // Mists of Pandaria Classic (or "Remix" if applicable)
            'mop-classic'    => ['base_path' => 'mop-classic/', 'lang_code' => 'en', 'script_domain' => 'mop-classic'],
            'ru.mop-classic' => ['base_path' => 'mop-classic/ru/', 'lang_code' => 'ru', 'script_domain' => 'ru.mop-classic'],
            // ... other mop-classic languages ...
            'de.mop-classic' => ['base_path' => 'mop-classic/de/', 'lang_code' => 'de', 'script_domain' => 'de.mop-classic'],
            'es.mop-classic' => ['base_path' => 'mop-classic/es/', 'lang_code' => 'es', 'script_domain' => 'es.mop-classic'],
            'fr.mop-classic' => ['base_path' => 'mop-classic/fr/', 'lang_code' => 'fr', 'script_domain' => 'fr.mop-classic'],
            'it.mop-classic' => ['base_path' => 'mop-classic/it/', 'lang_code' => 'it', 'script_domain' => 'it.mop-classic'],
            'pt.mop-classic' => ['base_path' => 'mop-classic/pt/', 'lang_code' => 'pt', 'script_domain' => 'pt.mop-classic'],
            'ko.mop-classic' => ['base_path' => 'mop-classic/ko/', 'lang_code' => 'ko', 'script_domain' => 'ko.mop-classic'],
            'cn.mop-classic' => ['base_path' => 'mop-classic/cn/', 'lang_code' => 'cn', 'script_domain' => 'cn.mop-classic'],
            'tw.mop-classic' => ['base_path' => 'mop-classic/tw/', 'lang_code' => 'tw', 'script_domain' => 'tw.mop-classic'],

            // PTR and Beta (English only by Wowhead convention for these subdomains)
            'ptr'  => ['base_path' => 'ptr/', 'lang_code' => 'en', 'script_domain' => 'ptr'], // Public Test Realm
            'beta' => ['base_path' => 'beta/', 'lang_code' => 'en', 'script_domain' => 'beta'],// Beta servers
        ];
    }

    /**
     * Returns a list of Wowhead-supported languages for Retail, used for WP language fallback.
     * @return array List of language codes.
     */
    private function get_supported_wowhead_langs() {
        return ['en', 'ru', 'de', 'es', 'fr', 'it', 'pt', 'ko', 'cn', 'tw'];
    }
    
    /**
     * Returns a list of Wowhead game version identifiers (keywords used in domain parameter).
     * @return array List of game version codes.
     */
    private function get_wow_versions() {
        // These are the "version" parts of a combined domain (e.g., 'classic' in 'ru.classic')
        // or standalone version domains (e.g., 'wotlk' for English WotLK).
        return ['classic', 'tbc', 'wotlk', 'cata', 'mop-classic', 'ptr', 'beta'];
    }

    /**
     * Normalizes the domain parameter from the shortcode.
     * Attempts to match direct keys (e.g., 'ru', 'classic', 'ru.classic').
     * Also handles "version.lang" by trying to convert it to the expected "lang.version" format.
     * @param string $domain_param The domain parameter from the shortcode.
     * @return string The normalized domain key or the original (lowercased) if no specific normalization rule applies.
     */
    private function normalize_domain_param($domain_param) {
        if (empty($domain_param)) {
            return '';
        }
        $domain_map = $this->get_domain_map();
        $domain_param_lower = strtolower($domain_param); 
        
        // Direct match first (e.g., "ru", "classic", "ru.classic")
        if (isset($domain_map[$domain_param_lower])) {
            return $domain_param_lower;
        }

        // If it contains a dot, it might be a combined domain like "lang.version" or "version.lang"
        if (strpos($domain_param_lower, '.') !== false) {
            $parts = explode('.', $domain_param_lower, 2); // Limit to 2 parts
            if (count($parts) === 2) {
                $part1 = $parts[0];
                $part2 = $parts[1];
                // Try as is: lang.version (already checked by direct match if $domain_param_lower was correct)
                if (isset($domain_map[$part1 . '.' . $part2])) return $part1 . '.' . $part2;
                // Try reversed: if input was version.lang, check if lang.version exists in map
                if (isset($domain_map[$part2 . '.' . $part1])) return $part2 . '.' . $part1;
            }
        }
        // Return the (lowercased) original if no specific normalization rule matched.
        // get_domain_details will then attempt further processing or fallback.
        return $domain_param_lower; 
    }

    /**
     * Determines the final domain details (base_path, lang_code, script_domain) based on priority:
     * 1. Explicit 'domain' parameter in the shortcode.
     * 2. WordPress site language (for Retail if 'domain' is not set).
     * 3. Default to English Retail.
     * It also attempts to use WordPress site language if a version-only domain is given 
     * (e.g., domain=classic on a Russian site might try to resolve to ru.classic).
     * @param string $domain_param_from_shortcode The 'domain' value from the shortcode's 'id' attribute.
     * @param string $wp_lang_code The current WordPress site's language code (e.g., 'ru').
     * @return array An array with 'base_path', 'lang_code', and 'script_domain'.
     */
    private function get_domain_details($domain_param_from_shortcode, $wp_lang_code) {
        $domain_map = $this->get_domain_map();
        $default_retail_english = $domain_map['en']; // Ultimate fallback.
        $wow_versions = $this->get_wow_versions();
        $supported_langs = $this->get_supported_wowhead_langs();

        if (!empty($domain_param_from_shortcode)) {
            $normalized_domain = $this->normalize_domain_param($domain_param_from_shortcode);

            // If a fully qualified and known domain (like 'ru.classic' or 'de') is found after normalization.
            if (isset($domain_map[$normalized_domain])) {
                return $domain_map[$normalized_domain];
            }
            
            // Further attempt to resolve if normalized_domain might be a version part from an unrecognized combo.
            // For example, if input was "classic.xx" and "xx.classic" isn't in map, normalized_domain might be "classic.xx".
            // We extract the potential version part.
            $potential_version_part = '';
            if (strpos($normalized_domain, '.') !== false) {
                $parts = explode('.', $normalized_domain, 2);
                if (in_array($parts[0], $wow_versions)) $potential_version_part = $parts[0]; // e.g. classic from classic.xx
                elseif (in_array($parts[1], $wow_versions)) $potential_version_part = $parts[1]; // e.g. classic from xx.classic
            } elseif (in_array($normalized_domain, $wow_versions)) { // e.g. domain=classic
                 $potential_version_part = $normalized_domain;
            }
            
            if (!empty($potential_version_part)) {
                // Try to combine this version part with WordPress site language.
                if (in_array($wp_lang_code, $supported_langs)) {
                    $combined_key = $wp_lang_code . '.' . $potential_version_part;
                    if (isset($domain_map[$combined_key])) {
                        return $domain_map[$combined_key]; // e.g. found ru.classic
                    }
                }
                // Fallback to the English version of that game type if it exists in the map.
                if (isset($domain_map[$potential_version_part])) { 
                    return $domain_map[$potential_version_part]; // e.g. found classic (which is English)
                }
            }
        } else { // No 'domain' in shortcode.
            // Try to use WordPress site language for Retail version.
            if (in_array($wp_lang_code, $supported_langs) && isset($domain_map[$wp_lang_code])) {
                return $domain_map[$wp_lang_code]; // e.g. found 'ru' for a Russian WP site.
            }
        }
        // Ultimate fallback if no other rule matches.
        return $default_retail_english; 
    }

    /**
     * Returns a list of valid Wowhead object types.
     * These are used to identify the main entity in the 'id' shortcode attribute.
     * Based on data from user-provided edit.txt and common Wowhead entity types.
     * @return array List of object type strings (e.g., "item", "spell").
     */
    private function get_valid_object_types() { 
        return [
            "item", "spell", "quest", "achievement", "npc", "zone", "currency", "faction", 
            "pet", "itemset", "skill", "mount", "outfit", "event", "object", "transmog", 
            "affix", 
            // Including types that might be less common as primary shortcode types but are valid Wowhead entities.
             "player-class", "race", "title", "building", "follower", "mission-ability", "mission", 
             "ship", "threat", "resource", "champion", "order-advancement", "bfa-champion", "azerite-essence", 
             "azerite-essence-power", "storyline", "profession-trait", "battle-pet-ability", 
             "trading-post-activity", "transmog-set", "hunter-pet", "guide", "icon", "sound"
        ];
    }

    /**
     * Handles the [wowhead] shortcode.
     * This function is called by WordPress when it encounters the [wowhead] shortcode in content.
     * It parses attributes, fetches data if needed, and generates the HTML for the Wowhead link.
     * @param array $atts Shortcode attributes provided by the user (e.g., id, name, color, style).
     * @param string|null $content Content enclosed by the shortcode (not used by this plugin).
     * @return string HTML output for the shortcode.
     */
    public function shortcode_handler($atts, $content = null) {
        // Signal that JavaScript assets (for tooltips) will be needed for this page load,
        // because at least one [wowhead] shortcode is being processed.
        self::$shortcode_settings['assets_needed_for_page'] = true;

        // Define default shortcode attributes and parse incoming ones from the $atts array.
        // The 'icon' attribute is parsed but will be ignored for setting whTooltips.iconSize,
        // as iconSize is now fixed to 'tiny' globally as per user requirements.
        $all_atts = shortcode_atts([
            'id'    => '',    // Main data string, e.g., "item=123&param=value&domain=xyz"
            'name'  => '',    // Custom link text override.
            'style' => '',    // Custom CSS style attribute for the link.
            'icon'  => '',    // Shortcode attribute for icon size (functionally ignored for global iconSize setting).
            'color' => '',    // CSS color name, hex code, or a CSS class name for link styling.
        ], $atts, 'wowhead');

        // Update global flags based on whether 'name', 'color', or 'style' attributes are used by any shortcode on the page.
        // These flags will influence the global `whTooltips` object configuration in the footer.
        if (!empty($all_atts['name'])) {
            self::$shortcode_settings['has_custom_name'] = true;
        }
        if (!empty($all_atts['color']) || !empty($all_atts['style'])) {
            self::$shortcode_settings['has_custom_style_or_color'] = true;
        }
        // The 'icon_sizes' array is no longer populated from $all_atts['icon'] because iconSize is fixed.
        
        // Extract and sanitize parameters from the parsed shortcode attributes.
        $id_string = trim($all_atts['id']); // The main string from the 'id' attribute (e.g., "item=19019&domain=ru").
        $custom_link_name_from_attr = sanitize_text_field($all_atts['name']); // User-defined link text.
        $custom_link_style_from_attr = $all_atts['style']; // User-defined inline CSS (will be sanitized later).
        $custom_link_color_from_attr = $all_atts['color']; // User-defined color or CSS class.

        // Get current WordPress site's language code (e.g., 'en', 'ru') for default domain determination.
        $wp_locale = get_locale();
        $wp_lang_code = strtolower(substr($wp_locale, 0, 2));

        // Parse the 'id' string (e.g., "item=123&domain=ru") into an associative array ($params_from_id_attr).
        $params_from_id_attr = [];
        if (!empty($id_string)) {
            parse_str(html_entity_decode($id_string), $params_from_id_attr);
        }
        
        // Extract the 'domain' parameter if it exists within the 'id' string.
        $domain_param_value = isset($params_from_id_attr['domain']) ? sanitize_text_field($params_from_id_attr['domain']) : '';

        // Determine the final Wowhead domain details (URL paths, language codes for scripts) based on priority.
        $domain_details = $this->get_domain_details($domain_param_value, $wp_lang_code);
        $wowhead_base_url_path_segment = $domain_details['base_path'];     // e.g., "ru/" or "classic/ru/" or "" for URL construction.
        $current_lang_code             = $domain_details['lang_code'];     // e.g., "ru", "en" for potential fallback display.
        $wowhead_script_domain_context = $domain_details['script_domain']; // e.g., "ru", "classic", "ru.classic" for power.js.

        // Handle empty shortcode ([wowhead] or [wowhead id=""]) by linking to the main Wowhead page for the determined domain.
        if (empty($id_string) || empty($params_from_id_attr)) {
            $main_page_url = 'https://www.wowhead.com/' . $wowhead_base_url_path_segment;
            return '<a href="' . esc_url($main_page_url) . '">Wowhead.com</a>';
        }

        // Initialize variables to store the primary object type (e.g., "item") and its ID (e.g., "19019").
        $object_type = '';
        $object_id_value = '';
        $wowhead_query_params = []; // For additional Wowhead URL parameters like gems, ench, etc. (e.g., &gems=123).
        $valid_object_types = $this->get_valid_object_types();

        // Iterate through parameters parsed from the 'id' string to find the main object type and ID.
        // Other parameters are stored in $wowhead_query_params.
        foreach ($params_from_id_attr as $key => $value) {
            $sanitized_key = sanitize_key($key); // Sanitize the parameter key.
            $raw_value = $value;                 // Keep raw value as some params (like 'pcs' for item sets) can be complex.

            if (in_array($sanitized_key, $valid_object_types)) {
                // If the key is a known object type (item, spell, etc.).
                if (is_numeric($raw_value) || !empty($raw_value) ) { // ID can be non-numeric for some entity types.
                    $object_type = $sanitized_key;
                    $object_id_value = $raw_value;
                }
            } elseif ($sanitized_key !== 'domain') { // 'domain' is handled separately for URL/script context.
                // Store other parameters (gems, ench, pcs, bonus, lvl, etc.) for the Wowhead URL.
                $wowhead_query_params[$sanitized_key] = $raw_value;
            }
        }
        
        // Handle cases where 'id' attribute only contained a 'domain' parameter (e.g., [wowhead id="domain=ru"]).
        // This should also link to the main Wowhead page for that domain.
        if (empty($object_type) && empty($object_id_value) && !empty($domain_param_value) && count($params_from_id_attr) === 1 && isset($params_from_id_attr['domain'])) {
             $main_page_url = 'https://www.wowhead.com/' . $wowhead_base_url_path_segment;
             return '<a href="' . esc_url($main_page_url) . '">Wowhead.com</a>';
        }

        // If no valid object type or ID was found after parsing, return an "unavailable" link.
        if (empty($object_type) || $object_id_value === '') { // Check for empty string ID as well.
            $fallback_url = 'https://www.wowhead.com/' . ($current_lang_code !== 'en' && !empty($current_lang_code) ? $current_lang_code . '/' : '');
            return '<a href="' . esc_url($fallback_url) . '" style="color: red;">' . esc_html__('Wowhead.com (unavailable)', 'wowhead-tooltips') . '</a>';
        }

        // Construct the base Wowhead link path (e.g., "item=19019").
        $wowhead_link_path = $object_type . '=' . $object_id_value;
        // Construct the full URL for the link's href attribute.
        $full_wowhead_url = 'https://www.wowhead.com/' . $wowhead_base_url_path_segment . $wowhead_link_path;
        
        // Construct the base URL for fetching XML data.
        // This path needs to be carefully constructed, especially if a version-only domain was given
        // (e.g. domain=classic for an EN WP site, base_path might be empty, but XML needs /classic/).
        $xml_fetch_url_path_segment = $wowhead_base_url_path_segment;
        if (in_array($wowhead_script_domain_context, $this->get_wow_versions()) && strpos($xml_fetch_url_path_segment, $wowhead_script_domain_context) === false) {
             if(empty($xml_fetch_url_path_segment) || substr($xml_fetch_url_path_segment, -1) !== '/') { $xml_fetch_url_path_segment .= '/'; }
             // Avoid double version path (e.g. classic/classic/) if base_path already had it for a specific language.
             if (strpos($xml_fetch_url_path_segment, $wowhead_script_domain_context.'/') === false) {
                $xml_fetch_url_path_segment = $wowhead_script_domain_context . '/' . $xml_fetch_url_path_segment;
                $xml_fetch_url_path_segment = str_replace('//', '/', $xml_fetch_url_path_segment); // Clean up potential double slashes.
             }
        }
        $xml_data_url = 'https://www.wowhead.com/' . $xml_fetch_url_path_segment . $wowhead_link_path;


        // Append additional query parameters (like &gems=..., &ench=...) to both the display URL and XML URL.
        if (!empty($wowhead_query_params)) {
            $query_string = http_build_query($wowhead_query_params);
            $full_wowhead_url .= '&' . $query_string;
            $xml_data_url .= '&' . $query_string;
        }
        $xml_data_url .= '&xml=1'; // Specific parameter required by Wowhead to return XML data.

        $item_name_from_xml = '';
        $is_404_error = false;

        // Fetch item name from XML and cache it if SimpleXML PHP extension is available.
        if (class_exists('SimpleXMLElement')) {
            $transient_key = 'wh_tooltip_data_' . md5($xml_data_url); // Unique key for caching based on the full XML URL.
            $cached_data = get_transient($transient_key); // Attempt to retrieve data from WordPress cache.

            if (false === $cached_data) { // If data is not in cache or has expired.
                $plugin_file_data = get_file_data(__FILE__, ['Version' => 'Version']);
                $plugin_version = $plugin_file_data['Version'] ?? '3.1.1'; // Get plugin version for User-Agent string.
                
                // Perform HTTP GET request to Wowhead XML feed.
                $response = wp_remote_get($xml_data_url, [
                    'timeout' => 10, // Request timeout in seconds.
                    'user-agent' => 'WordPress WowheadTooltipsPlugin/' . $plugin_version . '; ' . get_bloginfo('url') // User-Agent for the request.
                ]);
                
                $fetched_data_to_cache = ['name' => '', 'is_404' => false]; // Default structure for data to be cached.

                if (!is_wp_error($response)) { // Check if the HTTP request itself was successful.
                    $status_code = wp_remote_retrieve_response_code($response);
                    $xml_body = wp_remote_retrieve_body($response);

                    if ($status_code === 200 && !empty($xml_body)) { // Successful response with content.
                        libxml_use_internal_errors(true); // Suppress libxml errors from displaying directly on page.
                        $xml_object = simplexml_load_string($xml_body); // Parse XML string.
                        libxml_clear_errors(); // Clear any libxml errors.

                        if ($xml_object !== false) { // If XML was parsed successfully.
                            if (isset($xml_object->error)) { // Check if Wowhead XML itself returned an error message.
                                $error_message_lower = strtolower((string)$xml_object->error);
                                // Check for common "not found" messages in the error.
                                if (strpos($error_message_lower, 'not found') !== false || strpos($error_message_lower, 'не найден') !== false || strpos($error_message_lower, 'no results') !== false) {
                                    $fetched_data_to_cache['is_404'] = true;
                                }
                            } elseif (isset($xml_object->{$object_type}->name)) { // Try to get name specific to the object type.
                                $fetched_data_to_cache['name'] = sanitize_text_field((string)$xml_object->{$object_type}->name);
                            } else if (isset($xml_object->item) && isset($xml_object->item->name)) { // Fallback for general item structure often found in XML.
                                $fetched_data_to_cache['name'] = sanitize_text_field((string)$xml_object->item->name);
                            }
                        }
                    } elseif ($status_code === 404) { // HTTP 404 error from Wowhead.
                        $fetched_data_to_cache['is_404'] = true;
                    }
                }
                // Set cache expiration: 1 hour for 404 errors, 12 hours for successful fetches.
                $expiration_hours = $fetched_data_to_cache['is_404'] ? 1 : 12; 
                set_transient($transient_key, $fetched_data_to_cache, $expiration_hours * HOUR_IN_SECONDS); // Store data in WordPress cache.
                
                $item_name_from_xml = $fetched_data_to_cache['name'];
                $is_404_error = $fetched_data_to_cache['is_404'];
            } else { // Data was successfully retrieved from cache.
                $item_name_from_xml = $cached_data['name'] ?? '';
                $is_404_error = $cached_data['is_404'] ?? false;
            }
        } // End XML fetching block.


        // If XML fetch (or cache) resulted in a 404 error, display the "unavailable" link.
        if ($is_404_error) {
            $unavailable_url = 'https://www.wowhead.com/' . ($current_lang_code !== 'en' && !empty($current_lang_code) ? $current_lang_code . '/' : '');
            return '<a href="' . esc_url($unavailable_url) . '" style="color: red;">' . esc_html__('Wowhead.com (unavailable)', 'wowhead-tooltips') . '</a>';
        }

        // Determine the final link text.
        // Priority: 1. 'name' attribute from shortcode -> 2. Name from XML -> 3. Generic "Type ID" text.
        $link_text = $custom_link_name_from_attr;
        if (empty($link_text) && !empty($item_name_from_xml)) {
            $link_text = $item_name_from_xml;
        }
        if (empty($link_text)) {
            // Fallback link text if no custom name and XML failed or no name was found in XML.
            $link_text = ucfirst(str_replace('-', ' ', $object_type)) . ' ' . $object_id_value;
        }

        // Prepare the value for the 'data-wowhead' HTML attribute.
        // This attribute is crucial for Wowhead's power.js to identify and initialize tooltips.
        $data_wowhead_parts = [];
        $data_wowhead_parts[] = $object_type . '=' . $object_id_value; // e.g., "item=19019"
        foreach ($wowhead_query_params as $key => $value) { // Add other params like gems, ench, etc.
            $data_wowhead_parts[] = sanitize_key($key) . '=' . esc_attr($value);
        }
        if (!empty($wowhead_script_domain_context)) { // Add domain context if needed (e.g., "ru", "classic").
            $data_wowhead_parts[] = 'domain=' . $wowhead_script_domain_context;
        }
        $data_wowhead_attr_value = implode('&amp;', $data_wowhead_parts); // Join parts with '&amp;' for HTML attribute.

        // Start building the HTML attributes string for the <a> tag.
        $html_attrs_string = ' href="' . esc_url($full_wowhead_url) . '"';
        if (!empty($data_wowhead_attr_value)) {
            $html_attrs_string .= ' data-wowhead="' . esc_attr($data_wowhead_attr_value) . '"';
        }

        // Handle custom styling from 'color' and 'style' shortcode attributes.
        $link_classes = ['wowhead-tooltip-link']; // Default class for Wowhead links.
        $final_link_style = $custom_link_style_from_attr; // Start with style attribute.

        if (!empty($custom_link_color_from_attr)) {
            // Check if 'color' attribute is a hex code or a CSS named color (simple check, not exhaustive for all CSS color names).
            if (preg_match('/^#([a-f0-9]{3}){1,2}$/i', $custom_link_color_from_attr) || 
                (preg_match('/^[a-zA-Z]+$/', $custom_link_color_from_attr) && !in_array(strtolower($custom_link_color_from_attr), ['default', 'initial', 'inherit', 'unset', 'revert'])) ) {
                // If it's a color value, append it to the inline style.
                $final_link_style = rtrim($final_link_style, ';') . '; color:' . esc_attr($custom_link_color_from_attr) . ';';
            } else {
                // Otherwise, assume 'color' attribute is intended as a custom CSS class name.
                $link_classes[] = sanitize_html_class($custom_link_color_from_attr);
            }
        }
        
        // Sanitize and add the 'style' attribute if any styles were defined.
        $safe_style = safecss_filter_attr(trim($final_link_style)); // WordPress function to sanitize CSS.
        if (!empty($safe_style)) {
            $html_attrs_string .= ' style="' . esc_attr($safe_style) . '"';
        }
        // Add CSS classes to the link.
        if (!empty($link_classes)) {
            $html_attrs_string .= ' class="' . esc_attr(implode(' ', array_unique($link_classes))) . '"';
        }
        
        // Return the complete HTML <a> tag.
        return '<a' . $html_attrs_string . '>' . esc_html($link_text) . '</a>';
    }
    
    /**
     * Directly outputs the necessary JavaScript for Wowhead tooltips into the site's footer.
     * This method uses `echo` to output scripts, bypassing standard WordPress enqueueing,
     * as per user request for troubleshooting and ensuring scripts appear.
     * It outputs the `whTooltips` configuration object and the script tag for `power.js`.
     */
    public function direct_output_scripts_in_footer() {
        // Only output scripts if at least one [wowhead] shortcode was processed on the page.
        if (!self::$shortcode_settings['assets_needed_for_page']) {
            return;
        }

        // Determine the options for the global `whTooltips` JavaScript object.
        // These are based on attributes found across all shortcodes on the current page.
        $tooltip_options = [
            'colorLinks'   => !self::$shortcode_settings['has_custom_style_or_color'], // Disable Wowhead's coloring if custom color/style is used by any shortcode.
            'iconizeLinks' => true,                                                   // Always enable icons from Wowhead by default.
            'renameLinks'  => !self::$shortcode_settings['has_custom_name'],         // Disable Wowhead's renaming if a custom name is used by any shortcode.
            'iconSize'     => 'tiny',                                               // Fixed to 'tiny' as per latest requirements. 'icon' shortcode attribute is ignored.
        ];
        
        // Allow themes or other plugins to filter these options if necessary, using a WordPress filter.
        $filtered_options = apply_filters('wowhead_tooltips_script_options', $tooltip_options);
        
        // Directly echo the JavaScript HTML into the footer.
        echo "\n\n";
        echo "<script>const whTooltips = " . wp_json_encode($filtered_options) . ";</script>\n";
        // Load power.js synchronously (no 'async' or 'defer') after whTooltips is defined.
        echo "<script src=\"https://wow.zamimg.com/widgets/power.js\"></script>\n"; 
        echo "\n";
        
        // Reset the flag after outputting scripts for the current page load.
        // This ensures scripts are not outputted again if wp_footer is somehow called multiple times,
        // and prepares for the next HTTP request (though reset_settings_flags_on_wp_action also handles this initial reset).
        self::$shortcode_settings['assets_needed_for_page'] = false; 
    }
}

// Initialize the plugin by getting the singleton instance.
// This will also register the hooks (actions, shortcode) defined in the constructor.
Wowhead_Tooltips_Core_V3_1_1::get_instance();

?>
