<?php
/**
 * Dynamic Reader Mode - Main Plugin File
 *
 * Enhances reading experience by dimming non-content areas of the website
 * when users scroll through articles. Provides a distraction-free reading
 * environment with smooth transitions.
 *
 * @package     DynamicReaderMode
 * @author      Bart Boch
 * @copyright   2024 Bart Boch
 * @license     GPL-2.0-or-later
 * @link        https://bartboch.com
 * @since       1.0.0
 *
 * @wordpress-plugin
 * Plugin Name: Dynamic Reader Mode
 * Plugin URI:  https://wordpress.org/plugins/dynamic-reader-mode
 * Description: Enhances reading experience by automatically dimming non-content areas as users scroll through articles.
 * Version:     1.0.0
 * Author:      Bart Boch
 * Author URI:  https://bartboch.com
 * Text Domain: dynamic-reader-mode
 * License:     GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * Define plugin constants
 * These constants are used throughout the plugin for consistency
 */
define('DRM_VERSION', '1.0.0');
define('DRM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DRM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('DRM_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 *
 * Primary class responsible for initializing and managing the Dynamic Reader Mode plugin.
 * Implements singleton pattern to ensure only one instance runs at a time.
 *
 * @since 1.0.0
 */
class Dynamic_Reader_Mode {

    /**
     * Singleton instance
     *
     * @since  1.0.0
     * @access private
     * @var    Dynamic_Reader_Mode
     */
    private static $instance;

    /**
     * Plugin settings
     *
     * @since  1.0.0
     * @access private
     * @var    object
     */
    private $settings;

    /**
     * Get singleton instance
     *
     * @since  1.0.0
     * @access public
     * @return Dynamic_Reader_Mode
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     *
     * Protected constructor to prevent creating a new instance of the
     * class via the `new` operator from outside of this class.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function __construct() {
        $this->init();
    }

    /**
     * Initialize plugin
     *
     * Sets up necessary hooks and loads required dependencies.
     *
     * @since  1.0.0
     * @access private
     */
    private function init() {
        // Load text domain for internationalization
        add_action('plugins_loaded', array($this, 'load_textdomain'));

        // Initialize settings
        require_once DRM_PLUGIN_DIR . 'includes/class-settings.php';
        $this->settings = Dynamic_Reader_Mode_Settings::get_instance();

        // Initialize updater
        require_once DRM_PLUGIN_DIR . 'includes/class-updater.php';
        Dynamic_Reader_Mode_Updater::get_instance();

        // Register activation/deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        // Initialize frontend functionality
        if (!is_admin()) {
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
            add_action('wp_footer', array($this, 'render_toggle_button'));
        }
    }

    /**
     * Load plugin translations
     *
     * @since  1.0.0
     * @access public
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'dynamic-reader-mode',
            false,
            dirname(DRM_PLUGIN_BASENAME) . '/languages'
        );
    }

    /**
     * Enqueue frontend assets
     *
     * Loads necessary CSS and JavaScript files only when needed.
     *
     * @since  1.0.0
     * @access public
     */
    public function enqueue_scripts() {
        if (!$this->should_load_assets()) {
            return;
        }

        // Enqueue styles
        wp_enqueue_style(
            'drm-reader-mode',
            DRM_PLUGIN_URL . 'assets/css/focus-mode.css',
            array(),
            DRM_VERSION
        );

        // Enqueue scripts
        wp_enqueue_script(
            'drm-reader-mode',
            DRM_PLUGIN_URL . 'assets/js/focus-mode.js',
            array('jquery'),
            DRM_VERSION,
            true
        );

        // Localize script with settings
        wp_localize_script(
            'drm-reader-mode',
            'drmSettings',
            $this->get_frontend_settings()
        );
    }

    /**
     * Check if assets should be loaded
     *
     * Determines whether the plugin's assets should be loaded on the current page.
     *
     * @since  1.0.0
     * @access private
     * @return boolean
     */
    private function should_load_assets() {
        $options = get_option('drm_settings', array());
        
        // Always load if enabled for all content
        if (!empty($options['auto_activate_all'])) {
            return true;
        }

        // Load only on single posts and pages
        return is_singular(array('post', 'page'));
    }

    /**
     * Get frontend settings
     *
     * Prepares plugin settings for use in JavaScript.
     *
     * @since  1.0.0
     * @access private
     * @return array
     */
    private function get_frontend_settings() {
        $options = get_option('drm_settings', array());
        
        return array(
            'dimmingIntensity' => isset($options['dimming_intensity'])
                ? floatval($options['dimming_intensity'])
                : 0.5,
            'transitionSpeed' => isset($options['transition_speed'])
                ? intval($options['transition_speed'])
                : 300,
            'scrollThreshold' => isset($options['scroll_threshold'])
                ? intval($options['scroll_threshold'])
                : 100,
            'autoActivate' => !empty($options['auto_activate_all']),
            'showToggleButton' => true // Always show toggle button
        );
    }

    /**
     * Render toggle button
     *
     * Outputs the HTML for the reader mode toggle button.
     *
     * @since  1.0.0
     * @access public
     */
    public function render_toggle_button() {
        if ($this->should_load_assets()) {
            $button_text = esc_html__('Toggle Reader Mode', 'dynamic-reader-mode');
            echo sprintf(
                '<button class="drm-toggle-button" aria-label="%s" title="%s"><span class="drm-toggle-icon"></span></button>',
                esc_attr($button_text),
                esc_attr($button_text)
            );
        }
    }

    /**
     * Plugin activation
     *
     * Sets up default options when the plugin is activated.
     *
     * @since  1.0.0
     * @access public
     */
    public function activate() {
        if (!get_option('drm_settings')) {
            update_option('drm_settings', array(
                'auto_activate_all' => false,
                'scroll_threshold' => 100,
                'dimming_intensity' => 0.5,
                'transition_speed' => 300
            ));
        }
    }

    /**
     * Plugin deactivation
     *
     * Cleanup tasks when the plugin is deactivated.
     *
     * @since  1.0.0
     * @access public
     */
    public function deactivate() {
        // Currently no cleanup needed
    }
}

// Initialize the plugin
Dynamic_Reader_Mode::get_instance();