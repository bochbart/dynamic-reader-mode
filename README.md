# Dynamic Reader Mode

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![WordPress](https://img.shields.io/badge/wordpress-%3E%3D5.0-green.svg)
![PHP](https://img.shields.io/badge/php-%3E%3D7.2-purple.svg)

A WordPress plugin that enhances reading experience by automatically dimming non-content areas when users scroll through articles.

## Features

- 🎯 Smart dimming of non-content areas (navigation, sidebars, footers)
- 🔄 Smooth transitions when activating/deactivating
- 📱 Fully responsive and mobile-friendly
- ⌨️ Keyboard shortcut support (Alt + F)
- 🎨 Customizable dimming intensity and transition speeds
- 🔍 Automatic activation on scroll
- 🌓 Dark mode support

## Installation

1. Download the latest release
2. Upload to your WordPress site
3. Activate through WordPress plugin manager
4. Configure at Settings > Reader Mode

## Configuration

Access the plugin settings at WordPress Admin → Settings → Reader Mode to customize:

- Dimming intensity
- Transition speed
- Scroll threshold
- Automatic activation preferences

## Development

### Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- Modern browser support

### Structure

```
dynamic-reader-mode/
├── dynamic-reader-mode.php      # Main plugin file
├── includes/
│   ├── class-settings.php       # Settings management
│   └── class-updater.php        # Update handling
├── assets/
│   ├── css/
│   │   └── focus-mode.css       # Frontend styles
│   └── js/
│       └── focus-mode.js        # Frontend functionality
├── languages/                   # Translations
└── readme.txt                   # WordPress.org readme
```

### Filters

```php
// Modify dimming intensity
add_filter('drm_dimming_intensity', function($intensity) {
    return 0.7;
});

// Modify scroll threshold
add_filter('drm_scroll_threshold', function($threshold) {
    return 150;
});

// Add custom elements to dim
add_filter('drm_dimmable_selectors', function($selectors) {
    $selectors[] = '.my-custom-element';
    return $selectors;
});
```

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

GPL v2 or later - see [LICENSE](LICENSE) file for details.

## Author

[Bart Boch](https://bartboch.com)

## Support

- [Plugin Homepage](https://bartboch.com)
- [Report Issues](https://github.com/bartboch/dynamic-reader-mode/issues)

## Changelog

### 1.0.0
- Initial release
- Smart element dimming
- Scroll-based activation
- Customizable settings
- Performance optimizations

---

For more detailed information, please check the [WordPress plugin page](https://wordpress.org/plugins/dynamic-reader-mode).
