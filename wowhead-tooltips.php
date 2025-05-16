<?php
/**
 * Plugin Name: Wowhead Tooltips Shortcode
 * Plugin URI: https://your-website.com/wowhead-tooltips-shortcode
 * Description: Displays Wowhead tooltips via the [wowhead] shortcode. Supports various languages, game versions, and object types with advanced customization.
 * Version: 3.1.0 (Stable, direct script output, XML caching, fixed iconSize, English comments)
 * Author: Your Name
 * Author URI: https://your-website.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wowhead-tooltips
 * Domain Path: /languages
 * PHP Version: 7.4
 */

// Exit if accessed directly to prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

/**
 * Main class for the Wowhead Tooltips Shortcode plugin.
 * Handles shortcode registration, script enqueuing, and all related logic.
 */
class Wowhead_Tooltips_Core_V3_Commented {

    // Singleton instance of the class.
    private static $instance;

    // Static properties to store settings aggregated from all shortcodes on a page.
    // These settings determine how the global `whTooltips` JavaScript object will be configured.
    private static $shortcode_settings = [
        'has_custom_name'           => false, // True if any shortcode on the page uses the 'name' attribute.
        'has_custom_style_or_color' => false, // True if any shortcode uses 'color' or 'style' attributes.
        'assets_needed_for_page'    => false, // True if at least one [wowhead] shortcode is present on the page, signaling that scripts should be outputted.
    ];

    /**
     * Gets the singleton instance of this class.
     * Ensures that only one instance of the class is loaded.
     * @return Wowhead_Tooltips_Core_V3_Commented
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
        // Priority 999 to try and make it output late in the footer.
        add_action( 'wp_footer', [ $this, 'direct_output_scripts_in_footer' ], 999 );
        // Hook into 'wp' action (which runs after WordPress environment is set up but before headers are sent)
        // to reset page-specific flags for each new request.
        add_action('wp', [$this, 'reset_settings_flags_on_wp_action']);
    }
    
    /**
     * Resets the static $shortcode_settings flags at the beginning of each WordPress request.
     * This ensures that settings from a previous page load do not affect the current one.
     */
    public function reset_settings_flags_on_wp_action() {
        self::$shortcode_settings = [
            'has_custom_name'           => false,
            'has_custom_style_or_color' => false,
            'assets_needed_for_page'    => false, // Crucially reset this for each request.
        ];
    }

    /**
     * Loads the plugin's text domain for localization.
     * Allows translating strings within the plugin.
     */
    public function load_textdomain() {
        load_plugin_textdomain( 
            'wowhead-tooltips', // Unique text domain name.
            false, // Deprecated argument.
            dirname( plugin_basename( __FILE__ ) ) . '/languages' // Path to .mo files.
        );
    }

    /**
     * Returns the map of Wowhead domains, their base paths, language codes, and script domain contexts.
     * This data is based on the user-provided edit.txt.
     * 'key' (e.g., 'ru', 'classic', 'ru.classic') is used for lookups.
     * 'base_path': Path segment for Wowhead URLs (e.g., 'ru/', 'classic/').
     * 'lang_code': The language code part (e.g., 'ru', 'en').
     * 'script_domain': The domain context value expected by Wowhead's power.js.
     * @return array The domain map.
     */
    private function get_domain_map() { 
        return [
            // Retail (Live)
            'en'    => ['base_path' => '', 'lang_code' => 'en', 'script_domain' => ''],
            'ru'    => ['base_path' => 'ru/', 'lang_code' => 'ru', 'script_domain' => 'ru'],
            'de'    => ['base_path' => 'de/', 'lang_code' => 'de', 'script_domain' => 'de'],
            'es'    => ['base_path' => 'es/', 'lang_code' => 'es', 'script_domain' => 'es'],
            'fr'    => ['base_path' => 'fr/', 'lang_code' => 'fr', 'script_domain' => 'fr'],
            'it'    => ['base_path' => 'it/', 'lang_code' => 'it', 'script_domain' => 'it'],
            'pt'    => ['base_path' => 'pt/', 'lang_code' => 'pt', 'script_domain' => 'pt'],
            'ko'    => ['base_path' => 'ko/', 'lang_code' => 'ko', 'script_domain' => 'ko'],
            'cn'    => ['base_path' => 'cn/', 'lang_code' => 'cn', 'script_domain' => 'cn'],
            'tw'    => ['base_path' => 'tw/', 'lang_code' => 'tw', 'script_domain' => 'tw'],
            // Classic
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
            // TBC Classic
            'tbc'            => ['base_path' => 'tbc/', 'lang_code' => 'en', 'script_domain' => 'tbc'],
            'ru.tbc'         => ['base_path' => 'tbc/ru/', 'lang_code' => 'ru', 'script_domain' => 'ru.tbc'],
            'de.tbc'         => ['base_path' => 'tbc/de/', 'lang_code' => 'de', 'script_domain' => 'de.tbc'],
            'es.tbc'         => ['base_path' => 'tbc/es/', 'lang_code' => 'es', 'script_domain' => 'es.tbc'],
            'fr.tbc'         => ['base_path' => 'tbc/fr/', 'lang_code' => 'fr', 'script_domain' => 'fr.tbc'],
            'it.tbc'         => ['base_path' => 'tbc/it/', 'lang_code' => 'it', 'script_domain' => 'it.tbc'],
            'pt.tbc'         => ['base_path' => 'tbc/pt/', 'lang_code' => 'pt', 'script_domain' => 'pt.tbc'],
            'ko.tbc'         => ['base_path' => 'tbc/ko/', 'lang_code' => 'ko', 'script_domain' => 'ko.tbc'],
            'cn.tbc'         => ['base_path' => 'tbc/cn/', 'lang_code' => 'cn', 'script_domain' => 'cn.tbc'],
            'tw.tbc'         => ['base_path' => 'tbc/tw/', 'lang_code' => 'tw', 'script_domain' => 'tw.tbc'],
            // WotLK Classic
            'wotlk'          => ['base_path' => 'wotlk/', 'lang_code' => 'en', 'script_domain' => 'wotlk'],
            'ru.wotlk'       => ['base_path' => 'wotlk/ru/', 'lang_code' => 'ru', 'script_domain' => 'ru.wotlk'],
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
            'de.cata'        => ['base_path' => 'cata/de/', 'lang_code' => 'de', 'script_domain' => 'de.cata'],
            'es.cata'        => ['base_path' => 'cata/es/', 'lang_code' => 'es', 'script_domain' => 'es.cata'],
            'fr.cata'        => ['base_path' => 'cata/fr/', 'lang_code' => 'fr', 'script_domain' => 'fr.cata'],
            'it.cata'        => ['base_path' => 'cata/it/', 'lang_code' => 'it', 'script_domain' => 'it.cata'],
            'pt.cata'        => ['base_path' => 'cata/pt/', 'lang_code' => 'pt', 'script_domain' => 'pt.cata'],
            'ko.cata'        => ['base_path' => 'cata/ko/', 'lang_code' => 'ko', 'script_domain' => 'ko.cata'],
            'cn.cata'        => ['base_path' => 'cata/cn/', 'lang_code' => 'cn', 'script_domain' => 'cn.cata'],
            'tw.cata'        => ['base_path' => 'cata/tw/', 'lang_code' => 'tw', 'script_domain' => 'tw.cata'],
            // Mists of Pandaria Classic
            'mop-classic'    => ['base_path' => 'mop-classic/', 'lang_code' => 'en', 'script_domain' => 'mop-classic'],
            'ru.mop-classic' => ['base_path' => 'mop-classic/ru/', 'lang_code' => 'ru', 'script_domain' => 'ru.mop-classic'],
            'de.mop-classic' => ['base_path' => 'mop-classic/de/', 'lang_code' => 'de', 'script_domain' => 'de.mop-classic'],
            'es.mop-classic' => ['base_path' => 'mop-classic/es/', 'lang_code' => 'es', 'script_domain' => 'es.mop-classic'],
            'fr.mop-classic' => ['base_path' => 'mop-classic/fr/', 'lang_code' => 'fr', 'script_domain' => 'fr.mop-classic'],
            'it.mop-classic' => ['base_path' => 'mop-classic/it/', 'lang_code' => 'it', 'script_domain' => 'it.mop-classic'],
            'pt.mop-classic' => ['base_path' => 'mop-classic/pt/', 'lang_code' => 'pt', 'script_domain' => 'pt.mop-classic'],
            'ko.mop-classic' => ['base_path' => 'mop-classic/ko/', 'lang_code' => 'ko', 'script_domain' => 'ko.mop-classic'],
            'cn.mop-classic' => ['base_path' => 'mop-classic/cn/', 'lang_code' => 'cn', 'script_domain' => 'cn.mop-classic'],
            'tw.mop-classic' => ['base_path' => 'mop-classic/tw/', 'lang_code' => 'tw', 'script_domain' => 'tw.mop-classic'],
            // PTR and Beta (English only)
            'ptr'  => ['base_path' => 'ptr/', 'lang_code' => 'en', 'script_domain' => 'ptr'],
            'beta' => ['base_path' => 'beta/', 'lang_code' => 'en', 'script_domain' => 'beta'],
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
     * Returns a list of Wowhead game version identifiers.
     * @return array List of game version codes.
     */
    private function get_wow_versions() {
        return ['classic', 'tbc', 'wotlk', 'cata', 'mop-classic', 'ptr', 'beta'];
    }

    /**
     * Normalizes the domain parameter from the shortcode.
     * Tries to match direct keys and also handles "version.lang" by converting to "lang.version".
     * @param string $domain_param The domain parameter from the shortcode.
     * @return string The normalized domain key or the original if no specific rule applies.
     */
    private function normalize_domain_param($domain_param) {
        if (empty($domain_param)) {
            return '';
        }
        $domain_map = $this->get_domain_map();
        $domain_param_lower = strtolower($domain_param); // Work with lowercase
        
        // Direct match first
        if (isset($domain_map[$domain_param_lower])) {
            return $domain_param_lower;
        }

        // If it contains a dot, try to reorder parts (e.g., classic.ru -> ru.classic)
        if (strpos($domain_param_lower, '.') !== false) {
            $parts = explode('.', $domain_param_lower, 2);
            if (count($parts) === 2) {
                $part1 = $parts[0];
                $part2 = $parts[1];
                // Try as is: lang.version (already checked by direct match if $domain_param_lower was correct)
                if (isset($domain_map[$part1 . '.' . $part2])) return $part1 . '.' . $part2;
                // Try reversed: version.lang -> attempt to match as lang.version
                if (isset($domain_map[$part2 . '.' . $part1])) return $part2 . '.' . $part1;
            }
        }
        // Return the (lowercased) original if no specific normalization rule applied or matched.
        // The get_domain_details function will then try to use it or parts of it.
        return $domain_param_lower; 
    }

    /**
     * Determines the final domain details (base_path, lang_code, script_domain) based on:
     * 1. Explicit 'domain' parameter in the shortcode.
     * 2. WordPress site language (for Retail if 'domain' is not set).
     * 3. Default to English Retail.
     * It also attempts to use WordPress site language if a version-only domain is given (e.g. domain=classic on a Russian site might try ru.classic).
     * @param string $domain_param_from_shortcode The 'domain' value from the shortcode's 'id' attribute.
     * @param string $wp_lang_code The current WordPress site's language code (e.g., 'ru').
     * @return array An array with 'base_path', 'lang_code', and 'script_domain'.
     */
    private function get_domain_details($domain_param_from_shortcode, $wp_lang_code) {
        $domain_map = $this->get_domain_map();
        $default_retail_english = $domain_map['en']; // Fallback
        $wow_versions = $this->get_wow_versions();
        $supported_langs = $this->get_supported_wowhead_langs();

        if (!empty($domain_param_from_shortcode)) {
            $normalized_domain = $this->normalize_domain_param($domain_param_from_shortcode);

            // If a fully qualified domain (like 'ru.classic' or 'de') is found after normalization
            if (isset($domain_map[$normalized_domain])) {
                return $domain_map[$normalized_domain];
            }
            
            // If the normalized domain is just a version identifier (e.g., 'classic', 'wotlk')
            // (this can happen if input was 'classic.xx' and 'xx.classic' wasn't found, so normalize_domain_param returned 'classic.xx')
            // Let's re-check if $normalized_domain itself is a known version after potential split attempt.
            $potential_version_part = $normalized_domain;
            if (strpos($normalized_domain, '.') !== false) {
                $parts = explode('.', $normalized_domain, 2);
                // Prioritize version part if lang.version or version.lang didn't fully match
                if (in_array($parts[0], $wow_versions)) $potential_version_part = $parts[0];
                elseif (in_array($parts[1], $wow_versions)) $potential_version_part = $parts[1];
            }
            
            $is_version_only = in_array($potential_version_part, $wow_versions);

            if ($is_version_only) {
                // Try to combine with WordPress language if it's a supported language for game versions
                if (in_array($wp_lang_code, $supported_langs)) {
                    $combined_key = $wp_lang_code . '.' . $potential_version_part;
                    if (isset($domain_map[$combined_key])) {
                        return $domain_map[$combined_key];
                    }
                }
                // Fallback to the English version of that game type if it exists
                if (isset($domain_map[$potential_version_part])) { 
                    return $domain_map[$potential_version_part];
                }
            }
        } else {
            // No 'domain' in shortcode, try to use WordPress site language for Retail.
            if (in_array($wp_lang_code, $supported_langs) && isset($domain_map[$wp_lang_code])) {
                return $domain_map[$wp_lang_code];
            }
        }
        // Ultimate fallback to English Retail.
        return $default_retail_english; 
    }

    /**
     * Returns a list of valid Wowhead object types that can be primary identifiers in the 'id' attribute.
     * Based on user-provided edit.txt.
     * @return array List of object type strings.
     */
    private function get_valid_object_types() { 
        return [
            "item", "spell", "quest", "achievement", "npc", "zone", "currency", "faction", 
            "pet", "itemset", "skill", "mount", "outfit", "event", "object", "transmog", 
            "affix", "player-class", "race", "title", "building", "follower", "mission-ability", "mission", 
            "ship", "threat", "champion", "order-advancement", "bfa-champion", "azerite-essence", 
            "azerite-essence-power", "storyline", "profession-trait", "battle-pet-ability", 
            "trading-post-activity", "transmog-set", "hunter-pet", "guide", "icon", "sound", "resource"
        ];
    }

    /**
     * Handles the [wowhead] shortcode.
     * Parses attributes, fetches data if needed, and generates the HTML for the Wowhead link.
     * @param array $atts Shortcode attributes.
     * @param string|null $content Content enclosed by the shortcode (not used).
     * @return string HTML output for the shortcode.
     */
    public function shortcode_handler($atts, $content = null) {
        // Signal that assets (JS for tooltips) will be needed for this page,
        // as at least one shortcode is being processed.
        self::$shortcode_settings['assets_needed_for_page'] = true;

        // Define default shortcode attributes and parse incoming ones.
        // 'icon' attribute is parsed but will be ignored for whTooltips.iconSize as per new requirements.
        $all_atts = shortcode_atts([
            'id'    => '',    // Main data string: e.g., "item=123&param=value&domain=xyz"
            'name'  => '',    // Custom link text
            'style' => '',    // Custom CSS style attribute for the link
            'icon'  => '',    // Shortcode attribute for icon size (currently ignored for global whTooltips.iconSize)
            'color' => '',    // CSS color name, hex, or CSS class name for the link
        ], $atts, 'wowhead');

        // Update global flags based on whether 'name', 'color', or 'style' attributes are used.
        // These flags will influence the `whTooltips` object configuration.
        if (!empty($all_atts['name'])) {
            self::$shortcode_settings['has_custom_name'] = true;
        }
        if (!empty($all_atts['color']) || !empty($all_atts['style'])) {
            self::$shortcode_settings['has_custom_style_or_color'] = true;
        }
        
        // Extract and sanitize parameters from attributes.
        $id_string = trim($all_atts['id']); // The main string from the 'id' attribute.
        $custom_link_name_from_attr = sanitize_text_field($all_atts['name']);
        $custom_link_style_from_attr = $all_atts['style']; // Will be sanitized later with safecss_filter_attr.
        $custom_link_color_from_attr = $all_atts['color'];

        // Get current WordPress site language (e.g., 'en', 'ru').
        $wp_locale = get_locale();
        $wp_lang_code = strtolower(substr($wp_locale, 0, 2));

        // Parse the 'id' string (e.g., "item=123&domain=ru") into an associative array.
        $params_from_id_attr = [];
        if (!empty($id_string)) {
            parse_str(html_entity_decode($id_string), $params_from_id_attr);
        }
        
        // Extract the 'domain' parameter if it exists within the 'id' string.
        $domain_param_value = isset($params_from_id_attr['domain']) ? sanitize_text_field($params_from_id_attr['domain']) : '';

        // Determine the final Wowhead domain details (paths, language codes) based on priority.
        $domain_details = $this->get_domain_details($domain_param_value, $wp_lang_code);
        $wowhead_base_url_path_segment = $domain_details['base_path']; // e.g., "ru/" or "classic/ru/" or ""
        $current_lang_code = $domain_details['lang_code'];             // e.g., "ru", "en"
        $wowhead_script_domain_context = $domain_details['script_domain']; // e.g., "ru", "classic", "ru.classic"

        // Handle empty shortcode ([wowhead] or [wowhead id=""]) by linking to the main Wowhead page for the determined domain.
        if (empty($id_string) || empty($params_from_id_attr)) {
            $main_page_url = 'https://www.wowhead.com/' . $wowhead_base_url_path_segment;
            return '<a href="' . esc_url($main_page_url) . '">Wowhead.com</a>';
        }

        // Initialize variables to store the primary object type and its ID.
        $object_type = '';
        $object_id_value = '';
        $wowhead_query_params = []; // For additional Wowhead URL parameters like gems, ench, etc.
        $valid_object_types = $this->get_valid_object_types();

        // Iterate through parameters parsed from the 'id' string to find the main object type and ID.
        foreach ($params_from_id_attr as $key => $value) {
            $sanitized_key = sanitize_key($key);
            $raw_value = $value; // Keep raw value as some params (like 'pcs') can be complex.

            if (in_array($sanitized_key, $valid_object_types)) {
                // If the key is a known object type (item, spell, etc.)
                if (is_numeric($raw_value) || !empty($raw_value) ) { // ID can be non-numeric for some types.
                    $object_type = $sanitized_key;
                    $object_id_value = $raw_value;
                }
            } elseif ($sanitized_key !== 'domain') {
                // Store other parameters (gems, ench, pcs, bonus, lvl, etc.) for the Wowhead URL.
                $wowhead_query_params[$sanitized_key] = $raw_value;
            }
        }
        
        // Handle cases where 'id' only contained 'domain' (e.g., [wowhead id="domain=ru"]).
        if (empty($object_type) && empty($object_id_value) && !empty($domain_param_value) && count($params_from_id_attr) === 1 && isset($params_from_id_attr['domain'])) {
             $main_page_url = 'https://www.wowhead.com/' . $wowhead_base_url_path_segment;
             return '<a href="' . esc_url($main_page_url) . '">Wowhead.com</a>';
        }

        // If no valid object type or ID was found, return an "unavailable" link.
        if (empty($object_type) || $object_id_value === '') { // Check for empty string ID as well.
            $fallback_url = 'https://www.wowhead.com/' . ($current_lang_code !== 'en' && !empty($current_lang_code) ? $current_lang_code . '/' : '');
            return '<a href="' . esc_url($fallback_url) . '" style="color: red;">' . esc_html__('Wowhead.com (unavailable)', 'wowhead-tooltips') . '</a>';
        }

        // Construct the base Wowhead link path (e.g., "item=19019").
        $wowhead_link_path = $object_type . '=' . $object_id_value;
        // Construct the full URL for the link's href attribute.
        $full_wowhead_url = 'https://www.wowhead.com/' . $wowhead_base_url_path_segment . $wowhead_link_path;
        
        // Construct the base URL for fetching XML data.
        // This needs careful construction of the path segment, especially if a version-only domain was given
        // (e.g. domain=classic for an EN WP site, base_path might be empty, but XML needs /classic/).
        $xml_fetch_url_path_segment = $wowhead_base_url_path_segment;
        if (in_array($wowhead_script_domain_context, $this->get_wow_versions()) && strpos($xml_fetch_url_path_segment, $wowhead_script_domain_context) === false) {
             if(empty($xml_fetch_url_path_segment) || substr($xml_fetch_url_path_segment, -1) !== '/') { $xml_fetch_url_path_segment .= '/'; }
             if (strpos($xml_fetch_url_path_segment, $wowhead_script_domain_context.'/') === false) {
                $xml_fetch_url_path_segment = $wowhead_script_domain_context . '/' . $xml_fetch_url_path_segment;
                $xml_fetch_url_path_segment = str_replace('//', '/', $xml_fetch_url_path_segment); // Clean up double slashes.
             }
        }
        $xml_data_url = 'https://www.wowhead.com/' . $xml_fetch_url_path_segment . $wowhead_link_path;


        // Append additional query parameters (gems, ench, etc.) to both URLs.
        if (!empty($wowhead_query_params)) {
            $query_string = http_build_query($wowhead_query_params);
            $full_wowhead_url .= '&' . $query_string;
            $xml_data_url .= '&' . $query_string;
        }
        $xml_data_url .= '&xml=1'; // Specific parameter for Wowhead XML feed.

        $item_name_from_xml = '';
        $is_404_error = false;

        // Fetch item name from XML and cache it if SimpleXML is available.
        if (class_exists('SimpleXMLElement')) {
            $transient_key = 'wh_tooltip_data_' . md5($xml_data_url); // Unique key for caching.
            $cached_data = get_transient($transient_key);

            if (false === $cached_data) { // If data is not in cache or expired.
                $plugin_file_data = get_file_data(__FILE__, ['Version' => 'Version']);
                $plugin_version = $plugin_file_data['Version'] ?? '3.1.0'; // Get plugin version for User-Agent.
                
                // Fetch data from Wowhead XML.
                $response = wp_remote_get($xml_data_url, [
                    'timeout' => 10, // Request timeout in seconds.
                    'user-agent' => 'WordPress WowheadTooltipsPlugin/' . $plugin_version . '; ' . get_bloginfo('url')
                ]);
                
                $fetched_data_to_cache = ['name' => '', 'is_404' => false]; // Structure for cached data.

                if (!is_wp_error($response)) {
                    $status_code = wp_remote_retrieve_response_code($response);
                    $xml_body = wp_remote_retrieve_body($response);

                    if ($status_code === 200 && !empty($xml_body)) {
                        libxml_use_internal_errors(true); // Suppress libxml errors from displaying on page.
                        $xml_object = simplexml_load_string($xml_body);
                        libxml_clear_errors();

                        if ($xml_object !== false) { // If XML was parsed successfully.
                            if (isset($xml_object->error)) { // Wowhead XML returned an error message.
                                $error_message_lower = strtolower((string)$xml_object->error);
                                // Check for common "not found" messages.
                                if (strpos($error_message_lower, 'not found') !== false || strpos($error_message_lower, 'не найден') !== false || strpos($error_message_lower, 'no results') !== false) {
                                    $fetched_data_to_cache['is_404'] = true;
                                }
                            } elseif (isset($xml_object->{$object_type}->name)) { // Try to get name specific to object type.
                                $fetched_data_to_cache['name'] = sanitize_text_field((string)$xml_object->{$object_type}->name);
                            } else if (isset($xml_object->item) && isset($xml_object->item->name)) { // Fallback for general item structure in XML.
                                $fetched_data_to_cache['name'] = sanitize_text_field((string)$xml_object->item->name);
                            }
                        }
                    } elseif ($status_code === 404) { // HTTP 404 error.
                        $fetched_data_to_cache['is_404'] = true;
                    }
                }
                // Set cache expiration: 1 hour for 404s, 12 hours for successful fetches.
                $expiration_hours = $fetched_data_to_cache['is_404'] ? 1 : 12; 
                set_transient($transient_key, $fetched_data_to_cache, $expiration_hours * HOUR_IN_SECONDS);
                
                $item_name_from_xml = $fetched_data_to_cache['name'];
                $is_404_error = $fetched_data_to_cache['is_404'];
            } else { // Data was found in cache.
                $item_name_from_xml = $cached_data['name'] ?? '';
                $is_404_error = $cached_data['is_404'] ?? false;
            }
        }


        // If XML fetch resulted in a 404 error, display the "unavailable" link.
        if ($is_404_error) {
            $unavailable_url = 'https://www.wowhead.com/' . ($current_lang_code !== 'en' && !empty($current_lang_code) ? $current_lang_code . '/' : '');
            return '<a href="' . esc_url($unavailable_url) . '" style="color: red;">' . esc_html__('Wowhead.com (unavailable)', 'wowhead-tooltips') . '</a>';
        }

        // Determine the final link text.
        // Priority: 1. 'name' attribute -> 2. Name from XML -> 3. Generic "Type ID" text.
        $link_text = $custom_link_name_from_attr;
        if (empty($link_text) && !empty($item_name_from_xml)) {
            $link_text = $item_name_from_xml;
        }
        if (empty($link_text)) {
            $link_text = ucfirst(str_replace('-', ' ', $object_type)) . ' ' . $object_id_value;
        }

        // Prepare the value for the 'data-wowhead' attribute.
        // This attribute is used by Wowhead's power.js to initialize tooltips.
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

        // Handle custom styling from 'color' and 'style' attributes.
        $link_classes = ['wowhead-tooltip-link']; // Default class for Wowhead links.
        $final_link_style = $custom_link_style_from_attr;

        if (!empty($custom_link_color_from_attr)) {
            // If 'color' attribute looks like a hex code or a CSS named color.
            if (preg_match('/^#([a-f0-9]{3}){1,2}$/i', $custom_link_color_from_attr) || 
                (preg_match('/^[a-zA-Z]+$/', $custom_link_color_from_attr) && !in_array(strtolower($custom_link_color_from_attr), ['default', 'initial', 'inherit', 'unset', 'revert'])) ) {
                // Append to existing style attribute.
                $final_link_style = rtrim($final_link_style, ';') . '; color:' . esc_attr($custom_link_color_from_attr) . ';';
            } else {
                // Otherwise, assume 'color' attribute is a custom CSS class name.
                $link_classes[] = sanitize_html_class($custom_link_color_from_attr);
            }
        }
        
        // Sanitize and add the 'style' attribute.
        $safe_style = safecss_filter_attr(trim($final_link_style));
        if (!empty($safe_style)) {
            $html_attrs_string .= ' style="' . esc_attr($safe_style) . '"';
        }
        // Add CSS classes.
        if (!empty($link_classes)) {
            $html_attrs_string .= ' class="' . esc_attr(implode(' ', array_unique($link_classes))) . '"';
        }
        
        // Return the complete HTML <a> tag.
        return '<a' . $html_attrs_string . '>' . esc_html($link_text) . '</a>';
    }
    
    /**
     * Directly outputs the necessary JavaScript for Wowhead tooltips into the site's footer.
     * This method is used for testing if standard WordPress enqueueing causes issues.
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
            'colorLinks'   => !self::$shortcode_settings['has_custom_style_or_color'], // Disable Wowhead's coloring if custom color/style is used.
            'iconizeLinks' => true, // Always enable icons from Wowhead.
            'renameLinks'  => !self::$shortcode_settings['has_custom_name'],         // Disable Wowhead's renaming if custom name is used.
            'iconSize'     => 'tiny',                                               // Fixed to 'tiny' as per latest requirements.
        ];
        
        // Allow themes or other plugins to filter these options if necessary.
        $filtered_options = apply_filters('wowhead_tooltips_script_options', $tooltip_options);
        
        // Directly echo the JavaScript HTML into the footer.
        // This bypasses wp_enqueue_script and related WordPress functions for this test.
        echo "\n\n";
        echo "<script>const whTooltips = " . wp_json_encode($filtered_options) . ";</script>\n";
        // Load power.js synchronously after whTooltips is defined.
        echo "<script src=\"https://wow.zamimg.com/widgets/power.js\"></script>\n"; 
        echo "\n";
        
        // Reset the flag after outputting scripts for the current page load.
        // This ensures scripts are not outputted again if wp_footer is somehow called multiple times,
        // and prepares for the next HTTP request (though reset_settings_flags_on_wp_action also handles this).
        self::$shortcode_settings['assets_needed_for_page'] = false; 
    }
}

// Initialize the plugin by getting the singleton instance.
// This will also register the hooks defined in the constructor.
Wowhead_Tooltips_Core_V3_Commented::get_instance();
?>