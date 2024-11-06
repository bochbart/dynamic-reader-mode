=== Dynamic Reader Mode ===
Contributors: bartboch
Donate link: https://bartboch.com
Tags: reader mode, focus mode, readability, content, accessibility, reading
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Enhance reading experience by automatically dimming non-content areas when scrolling through articles.

== Description ==

Dynamic Reader Mode improves reading focus by gently dimming distracting elements like headers, sidebars, and footers while keeping your main content clear and readable. The effect activates smoothly as readers scroll through your content, creating a distraction-free reading environment.

= Key Features =

* **Smart Dimming**: Automatically dims navigation, sidebars, footers, and other non-content areas
* **Content Focus**: Main article content stays clear and readable
* **Smooth Transitions**: Gentle fade effects when activating/deactivating
* **Scroll Activation**: Activates automatically when scrolling begins (customizable threshold)
* **Subtle Controls**: Unintrusive toggle button that becomes more subtle when active
* **Keyboard Support**: Quick toggle with Alt + F
* **Performance Optimized**: Minimal impact on page load and scroll performance

= Perfect For =

* Long-form articles
* Blog posts
* Documentation pages
* Tutorial content
* News articles
* Any text-heavy content

= Technical Features =

* Intelligent element detection for proper dimming
* Configurable dimming intensity
* Customizable transition speeds
* Responsive design support
* Cookie-based user preference storage
* Translation-ready

== Installation ==

1. Upload `dynamic-reader-mode` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > Reader Mode to configure options
4. Test the reader mode on any post or page

= Quick Start Guide =

1. After activation, visit Settings > Reader Mode
2. Configure basic settings:
   * Enable/disable automatic activation
   * Set scroll threshold for activation
   * Adjust dimming intensity
   * Configure transition speed
3. Save your settings
4. Visit any post to see the reader mode in action

== Frequently Asked Questions ==

= How does the dimming work? =

When activated, the plugin intelligently identifies and dims non-content areas of your site (navigation, sidebars, footers, etc.) while keeping the main content area clear and readable.

= Will this affect my site's performance? =

No. The plugin uses efficient selectors and modern transitions, loading only when needed on content pages. All animations are hardware-accelerated for smooth performance.

= Can visitors disable the effect? =

Yes! A subtle toggle button appears in the bottom-right corner, allowing readers to turn the effect on/off. Their preference is saved for future visits. They can also use the Alt + F keyboard shortcut.

= How do I customize the appearance? =

The settings page allows you to adjust:
* Dimming intensity for non-content areas
* Transition speed for the dimming effect
* Scroll threshold for activation
* Automatic activation preferences

= Is it mobile-friendly? =

Yes! The plugin is fully responsive and works smoothly on all devices and screen sizes. The toggle button is positioned to avoid interfering with content reading on any device.

= Does it work with my theme? =

The plugin is designed to work with any properly structured WordPress theme. It automatically detects standard theme elements like headers, navigation, sidebars, and footers.

== Screenshots ==

1. Reading experience with dimmed non-content areas
2. Settings panel
3. Mobile view
4. Toggle button states (active/inactive)

== Changelog ==

= 1.0.0 =
* Initial release
* Smart element dimming
* Scroll-based activation
* Customizable settings
* Subtle toggle button
* Keyboard shortcuts
* Performance optimizations

== Upgrade Notice ==

= 1.0.0 =
Initial release of Dynamic Reader Mode

== Development ==

* [GitHub Repository](https://github.com/bochbart/dynamic-reader-mode)
* Found a bug? [Create an issue](https://github.com/bochbart/dynamic-reader-mode/issues)

= For Developers =

The plugin includes several filters for customization:

`
// Modify dimming intensity
add_filter('drm_dimming_intensity', function($intensity) {
    return 0.7; // Custom intensity
});

// Modify scroll threshold
add_filter('drm_scroll_threshold', function($threshold) {
    return 150; // Custom threshold in pixels
});

// Add custom elements to dim
add_filter('drm_dimmable_selectors', function($selectors) {
    $selectors[] = '.my-custom-element';
    return $selectors;
});
`

== Custom CSS ==

You can customize the appearance using CSS:

```css
/* Custom toggle button styles */
.drm-toggle-button {
    /* Your custom styles */
}

/* Adjust dimming for specific elements */
body.drm-active .drm-dimmable {
    /* Your custom styles */
}
```

== Support ==

* Visit [plugin homepage](https://bartboch.com)
* Get [premium support](https://bartboch.com/support)

== Privacy ==

This plugin:
* Does not collect any personal data
* Uses a single cookie to remember user's preference
* Does not connect to external services

== Additional Notes ==

= Requirements =
* WordPress 5.0 or higher
* PHP 7.2 or higher
* Modern browser support

= Performance Considerations =
* Minimal DOM manipulation
* Efficient element detection
* Hardware-accelerated animations
* Assets load only on content pages

= Accessibility =
* WCAG 2.1 compliant
* Keyboard shortcut support
* Respects reduced motion preferences
* Maintained content readability
* High contrast support
