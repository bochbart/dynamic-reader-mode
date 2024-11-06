<?php
/**
 * Dynamic Reader Mode - Updater Class
 *
 * Handles plugin updates, migrations, and version management functionality.
 * Ensures smooth transitions between plugin versions and manages database updates.
 *
 * @package     DynamicReaderMode
 * @subpackage  Updates
 * @author      Bart Boch
 * @copyright   2024 Bart Boch
 * @link        https://bartboch.com
 * @since       1.0.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * Updater Class
 *
 * Manages version checking and updates, ensuring smooth transitions
 * between plugin versions and handling any necessary database migrations.
 *
 * @since 1.0.0
 */
class Dynamic_Reader_Mode_Updater {

    /**
     * Singleton instance
     *
     * @since  1.0.0
     * @access private
     * @var    Dynamic_Reader_Mode_Updater
     */
    private static $instance;

    /**
     * Get singleton instance
     *
     * Ensures only one instance of the updater exists in memory at any time.
     *
     * @since  1.0.0
     * @access public
     * @return Dynamic_Reader_Mode_Updater
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
     * Sets up necessary hooks for version checking and updates.
     *
     * @since  1.0.0
     * @access private
     */
    private function __construct() {
        add_action('admin_init', array($this, 'check_version'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));
    }

    /**
     * Check plugin version
     *
     * Compares current version with stored version and triggers updates
     * if necessary.
     *
     * @since  1.0.0
     * @access public
     */
    public function check_version() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $current_version = get_option('drm_version', '0.0.0');

        if (version_compare($current_version, DRM_VERSION, '<')) {
            $this->run_updates($current_version);
            update_option('drm_version', DRM_VERSION);
        }
    }

    /**
     * Run version-specific updates
     *
     * Executes necessary updates based on the version difference.
     *
     * @since  1.0.0
     * @access private
     * @param  string $current_version Currently installed version
     */
    private function run_updates($current_version) {
        // Initial install or update to 1.0.0
        if (version_compare($current_version, '1.0.0', '<')) {
            $this->update_to_1_0_0();
        }

        // Future version updates will be added here
        // Example:
        // if (version_compare($current_version, '1.1.0', '<')) {
        //     $this->update_to_1_1_0();
        // }
    }

    /**
     * Update to version 1.0.0
     *
     * Handles the initial setup and any migrations needed for version 1.0.0.
     *
     * @since  1.0.0
     * @access private
     */
    private function update_to_1_0_0() {
        $default_settings = array(
            'auto_activate_all' => false,
            'show_toggle_button' => true,
            'scroll_threshold' => 100,
            'dimming_intensity' => 0.5,
            'transition_speed' => 300
        );

        $existing_settings = get_option('drm_settings', array());
        $merged_settings = wp_parse_args($existing_settings, $default_settings);
        
        update_option('drm_settings', $merged_settings);
    }

    /**
     * Clean up plugin data
     *
     * Removes plugin-related options and data from the database.
     * Used during uninstallation or for cleanup operations.
     *
     * @since  1.0.0
     * @access public
     * @static
     */
    public static function cleanup() {
        delete_option('drm_version');
        delete_option('drm_settings');
    }

    /**
     * Load plugin textdomain
     *
     * Enables plugin internationalization by loading appropriate
     * language files.
     *
     * @since  1.0.0
     * @access public
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'dynamic-reader-mode',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }
}