<?php
/**
 * Dynamic Reader Mode - Unit Tests
 *
 * Comprehensive test suite for ensuring plugin functionality.
 * Tests core features, settings management, and frontend behavior.
 *
 * @package     DynamicReaderMode
 * @subpackage  Tests
 * @author      Bart Boch
 * @copyright   2024 Bart Boch
 * @link        https://bartboch.com
 * @since       1.0.0
 */

/**
 * Main Test Class
 *
 * Contains all unit tests for the Dynamic Reader Mode plugin.
 *
 * @since 1.0.0
 */
class DynamicReaderModeTest extends WP_UnitTestCase {
    
    /**
     * Plugin instance
     *
     * @since  1.0.0
     * @access protected
     * @var    Dynamic_Reader_Mode
     */
    protected $plugin;

    /**
     * Settings instance
     *
     * @since  1.0.0
     * @access protected
     * @var    Dynamic_Reader_Mode_Settings
     */
    protected $settings;

    /**
     * Set up test environment
     *
     * Creates fresh instances for each test.
     *
     * @since  1.0.0
     * @access public
     */
    public function setUp(): void {
        parent::setUp();
        $this->plugin = Dynamic_Reader_Mode::get_instance();
        $this->settings = Dynamic_Reader_Mode_Settings::get_instance();
    }

    /**
     * Test plugin instantiation
     *
     * Verifies proper singleton pattern implementation.
     *
     * @since  1.0.0
     * @access public
     */
    public function test_plugin_instance() {
        $instance = Dynamic_Reader_Mode::get_instance();
        $this->assertInstanceOf(
            Dynamic_Reader_Mode::class,
            $instance,
            'Plugin instance should be of correct class'
        );

        $second_instance = Dynamic_Reader_Mode::get_instance();
        $this->assertSame(
            $instance,
            $second_instance,
            'Multiple get_instance() calls should return same instance'
        );
    }

    /**
     * Test default settings
     *
     * Ensures default settings are properly initialized.
     *
     * @since  1.0.0
     * @access public
     */
    public function test_default_settings() {
        delete_option('drm_settings');
        $this->plugin->activate();
        
        $settings = get_option('drm_settings');
        
        $this->assertIsArray($settings, 'Settings should be an array');
        
        // Test required settings existence
        $required_settings = array(
            'auto_activate_all',
            'show_toggle_button',
            'scroll_threshold',
            'dimming_intensity',
            'transition_speed'
        );
        
        foreach ($required_settings as $setting) {
            $this->assertArrayHasKey(
                $setting,
                $settings,
                "Setting '$setting' should exist"
            );
        }
        
        // Test default values
        $this->assertEquals(100, $settings['scroll_threshold']);
        $this->assertEquals(0.5, $settings['dimming_intensity']);
        $this->assertEquals(300, $settings['transition_speed']);
    }

    /**
     * Test asset loading conditions
     *
     * Verifies assets are loaded only when appropriate.
     *
     * @since  1.0.0
     * @access public
     */
    public function test_asset_loading() {
        // Create test post
        $post_id = $this->factory->post->create(array(
            'post_type' => 'post',
            'post_status' => 'publish'
        ));
        
        // Test with auto-activate enabled
        update_option('drm_settings', array(
            'auto_activate_all' => true
        ));
        
        $this->go_to(get_permalink($post_id));
        $this->assertTrue(
            $this->plugin->should_load_assets(),
            'Assets should load when auto-activate is enabled'
        );
        
        // Test with auto-activate disabled
        update_option('drm_settings', array(
            'auto_activate_all' => false
        ));
        
        $this->assertTrue(
            $this->plugin->should_load_assets(),
            'Assets should load on singular posts'
        );
        
        // Test on non-singular page
        $this->go_to(home_url());
        $this->assertFalse(
            $this->plugin->should_load_assets(),
            'Assets should not load on non-singular pages'
        );
    }

    /**
     * Test settings sanitization
     *
     * Ensures user input is properly sanitized.
     *
     * @since  1.0.0
     * @access public
     */
    public function test_settings_sanitization() {
        $test_input = array(
            'dimming_intensity' => '1.5',    // Should be capped at 1.0
            'scroll_threshold' => '-50',     // Should be minimum 0
            'transition_speed' => '2000',    // Should be capped at 1000
            'auto_activate_all' => '1',
            'show_toggle_button' => 'yes'    // Should be converted to 1
        );
        
        $sanitized = $this->settings->sanitize_settings($test_input);
        
        $this->assertEquals(1.0, $sanitized['dimming_intensity']);
        $this->assertEquals(0, $sanitized['scroll_threshold']);
        $this->assertEquals(1000, $sanitized['transition_speed']);
        $this->assertEquals(1, $sanitized['auto_activate_all']);
        $this->assertEquals(1, $sanitized['show_toggle_button']);
    }

    /**
     * Test toggle button rendering
     *
     * Verifies proper toggle button HTML output.
     *
     * @since  1.0.0
     * @access public
     */
    public function test_toggle_button_rendering() {
        // Test with button enabled
        update_option('drm_settings', array(
            'show_toggle_button' => true
        ));
        
        ob_start();
        $this->plugin->render_toggle_button();
        $output = ob_get_clean();
        
        $this->assertStringContainsString(
            'class="drm-toggle-button"',
            $output,
            'Toggle button should be rendered when enabled'
        );
        
        // Test with button disabled
        update_option('drm_settings', array(
            'show_toggle_button' => false
        ));
        
        ob_start();
        $this->plugin->render_toggle_button();
        $output = ob_get_clean();
        
        $this->assertEmpty(
            $output,
            'Toggle button should not be rendered when disabled'
        );
    }

    /**
     * Test frontend settings preparation
     *
     * Verifies settings are properly formatted for frontend.
     *
     * @since  1.0.0
     * @access public
     */
    public function test_frontend_settings() {
        $test_settings = array(
            'dimming_intensity' => 0.7,
            'scroll_threshold' => 150,
            'transition_speed' => 400,
            'auto_activate_all' => true,
            'show_toggle_button' => true
        );
        
        update_option('drm_settings', $test_settings);
        
        $frontend_settings = $this->plugin->get_frontend_settings();
        
        $this->assertIsArray($frontend_settings);
        $this->assertEquals(0.7, $frontend_settings['dimmingIntensity']);
        $this->assertEquals(150, $frontend_settings['scrollThreshold']);
        $this->assertEquals(400, $frontend_settings['transitionSpeed']);
        $this->assertTrue($frontend_settings['autoActivate']);
        $this->assertTrue($frontend_settings['showToggleButton']);
    }

    /**
     * Clean up after tests
     *
     * Removes test data from database.
     *
     * @since  1.0.0
     * @access public
     */
    public function tearDown(): void {
        parent::tearDown();
        delete_option('drm_settings');
        delete_option('drm_version');
    }
}