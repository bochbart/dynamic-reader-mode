<?php
/**
 * Dynamic Reader Mode - Settings Management Class
 *
 * Handles all settings-related functionality for the Dynamic Reader Mode plugin.
 * Provides admin interface for configuring dimming behavior and activation settings.
 *
 * @package     DynamicReaderMode
 * @subpackage  Settings
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
 * Settings Class
 *
 * Manages plugin settings including:
 * - Admin interface creation
 * - Settings registration and validation
 * - Options management
 *
 * @since 1.0.0
 */
class Dynamic_Reader_Mode_Settings {

    /**
     * Singleton instance
     *
     * @since  1.0.0
     * @access private
     * @var    Dynamic_Reader_Mode_Settings
     */
    private static $instance;

    /**
     * Settings page slug
     *
     * @since  1.0.0
     * @access private
     * @var    string
     */
    private $page_slug = 'drm-settings';

    /**
     * Option name in database
     *
     * @since  1.0.0
     * @access private
     * @var    string
     */
    private $option_name = 'drm_settings';

    /**
     * Get singleton instance
     *
     * @since  1.0.0
     * @access public
     * @return Dynamic_Reader_Mode_Settings
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
     * @since  1.0.0
     * @access private
     */
    private function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_filter('plugin_action_links_' . DRM_PLUGIN_BASENAME, array($this, 'add_settings_link'));
    }

    /**
     * Add settings page to admin menu
     *
     * @since  1.0.0
     * @access public
     */
    public function add_settings_page() {
        add_options_page(
            __('Reader Mode Settings', 'dynamic-reader-mode'),
            __('Reader Mode', 'dynamic-reader-mode'),
            'manage_options',
            $this->page_slug,
            array($this, 'render_settings_page')
        );
    }

    /**
     * Register plugin settings
     *
     * @since  1.0.0
     * @access public
     */
    public function register_settings() {
        register_setting(
            $this->option_name,
            $this->option_name,
            array($this, 'sanitize_settings')
        );

        // Activation Section
        add_settings_section(
            'drm_activation_section',
            __('Activation Settings', 'dynamic-reader-mode'),
            array($this, 'render_section_description'),
            $this->page_slug
        );

        // Appearance Section
        add_settings_section(
            'drm_appearance_section',
            __('Appearance Settings', 'dynamic-reader-mode'),
            array($this, 'render_section_description'),
            $this->page_slug
        );

        $this->register_activation_fields();
        $this->register_appearance_fields();
    }

    /**
     * Register activation settings fields
     *
     * @since  1.0.0
     * @access private
     */
    private function register_activation_fields() {
        // Auto-activation Field
        add_settings_field(
            'auto_activate_all',
            __('Automatic Activation', 'dynamic-reader-mode'),
            array($this, 'render_checkbox_field'),
            $this->page_slug,
            'drm_activation_section',
            array(
                'id' => 'auto_activate_all',
                'description' => __('Enable reader mode automatically on all content pages', 'dynamic-reader-mode')
            )
        );

        // Scroll Threshold Field
        add_settings_field(
            'scroll_threshold',
            __('Scroll Threshold', 'dynamic-reader-mode'),
            array($this, 'render_number_field'),
            $this->page_slug,
            'drm_activation_section',
            array(
                'id' => 'scroll_threshold',
                'description' => __('Number of pixels to scroll before activating reader mode', 'dynamic-reader-mode'),
                'min' => 0,
                'max' => 1000,
                'step' => 10
            )
        );
    }

    /**
     * Register appearance settings fields
     *
     * @since  1.0.0
     * @access private
     */
    private function register_appearance_fields() {
        // Dimming Intensity Field
        add_settings_field(
            'dimming_intensity',
            __('Dimming Intensity', 'dynamic-reader-mode'),
            array($this, 'render_range_field'),
            $this->page_slug,
            'drm_appearance_section',
            array(
                'id' => 'dimming_intensity',
                'description' => __('How much to dim non-content areas (0 = no dimming, 1 = maximum dimming)', 'dynamic-reader-mode'),
                'min' => 0,
                'max' => 1,
                'step' => 0.1
            )
        );

        // Transition Speed Field
        add_settings_field(
            'transition_speed',
            __('Transition Speed', 'dynamic-reader-mode'),
            array($this, 'render_number_field'),
            $this->page_slug,
            'drm_appearance_section',
            array(
                'id' => 'transition_speed',
                'description' => __('Speed of dimming transitions in milliseconds', 'dynamic-reader-mode'),
                'min' => 100,
                'max' => 1000,
                'step' => 50
            )
        );
    }

    /**
     * Render settings page
     *
     * @since  1.0.0
     * @access public
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <p><?php _e('Configure how the reader mode behaves and appears on your site.', 'dynamic-reader-mode'); ?></p>
            <form action="options.php" method="post">
                <?php
                settings_fields($this->option_name);
                do_settings_sections($this->page_slug);
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Render checkbox field
     *
     * @since  1.0.0
     * @access public
     * @param  array $args Field arguments
     */
    public function render_checkbox_field($args) {
        $options = get_option($this->option_name);
        $value = isset($options[$args['id']]) ? $options[$args['id']] : false;
        ?>
        <label>
            <input type="checkbox" 
                   name="<?php echo esc_attr($this->option_name . '[' . $args['id'] . ']'); ?>"
                   value="1"
                   <?php checked(1, $value); ?>>
            <?php echo esc_html($args['description']); ?>
        </label>
        <?php
    }

    /**
     * Render range field
     *
     * @since  1.0.0
     * @access public
     * @param  array $args Field arguments
     */
    public function render_range_field($args) {
        $options = get_option($this->option_name);
        $value = isset($options[$args['id']]) ? $options[$args['id']] : 0.5;
        ?>
        <input type="range"
               name="<?php echo esc_attr($this->option_name . '[' . $args['id'] . ']'); ?>"
               value="<?php echo esc_attr($value); ?>"
               min="<?php echo esc_attr($args['min']); ?>"
               max="<?php echo esc_attr($args['max']); ?>"
               step="<?php echo esc_attr($args['step']); ?>"
               class="drm-range">
        <span class="drm-range-value"><?php echo esc_html($value); ?></span>
        <p class="description"><?php echo esc_html($args['description']); ?></p>
        <?php
    }

    /**
     * Render number field
     *
     * @since  1.0.0
     * @access public
     * @param  array $args Field arguments
     */
    public function render_number_field($args) {
        $options = get_option($this->option_name);
        $value = isset($options[$args['id']]) ? $options[$args['id']] : '';
        ?>
        <input type="number"
               name="<?php echo esc_attr($this->option_name . '[' . $args['id'] . ']'); ?>"
               value="<?php echo esc_attr($value); ?>"
               min="<?php echo esc_attr($args['min']); ?>"
               max="<?php echo esc_attr($args['max']); ?>"
               step="<?php echo esc_attr($args['step']); ?>"
               class="regular-text">
        <p class="description"><?php echo esc_html($args['description']); ?></p>
        <?php
    }

    /**
     * Sanitize settings
     *
     * @since  1.0.0
     * @access public
     * @param  array $input Raw input data
     * @return array Sanitized input data
     */
    public function sanitize_settings($input) {
        $sanitized = array();

        // Sanitize checkbox
        $sanitized['auto_activate_all'] = isset($input['auto_activate_all']) ? 1 : 0;

        // Sanitize numbers
        if (isset($input['scroll_threshold'])) {
            $sanitized['scroll_threshold'] = absint($input['scroll_threshold']);
            $sanitized['scroll_threshold'] = min(max($sanitized['scroll_threshold'], 0), 1000);
        }

        if (isset($input['transition_speed'])) {
            $sanitized['transition_speed'] = absint($input['transition_speed']);
            $sanitized['transition_speed'] = min(max($sanitized['transition_speed'], 100), 1000);
        }

        // Sanitize float
        if (isset($input['dimming_intensity'])) {
            $sanitized['dimming_intensity'] = floatval($input['dimming_intensity']);
            $sanitized['dimming_intensity'] = min(max($sanitized['dimming_intensity'], 0), 1);
        }

        return $sanitized;
    }

    /**
     * Add settings link to plugins page
     *
     * @since  1.0.0
     * @access public
     * @param  array $links Existing plugin action links
     * @return array Modified plugin action links
     */
    public function add_settings_link($links) {
        $settings_link = sprintf(
            '<a href="%s">%s</a>',
            admin_url('options-general.php?page=' . $this->page_slug),
            __('Settings', 'dynamic-reader-mode')
        );
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Render section descriptions
     *
     * @since  1.0.0
     * @access public
     * @param  array $args Section arguments
     */
    public function render_section_description($args) {
        switch ($args['id']) {
            case 'drm_activation_section':
                echo '<p>' . esc_html__('Configure when the reader mode should activate.', 'dynamic-reader-mode') . '</p>';
                break;
            case 'drm_appearance_section':
                echo '<p>' . esc_html__('Customize how the reader mode appears.', 'dynamic-reader-mode') . '</p>';
                break;
        }
    }
}