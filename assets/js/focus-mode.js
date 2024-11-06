/**
 * Dynamic Reader Mode - Frontend JavaScript
 *
 * Implements content focus by specifically targeting and dimming
 * non-content areas while preserving content visibility.
 *
 * @package     DynamicReaderMode
 * @author      Bart Boch
 * @copyright   2024 Bart Boch
 * @since       1.0.0
 */

(function($) {
    'use strict';

    class ReaderMode {
        constructor(options) {
            this.options = {
                dimmingIntensity: 0.5,
                transitionSpeed: 300,
                autoActivate: false,
                showToggleButton: true,
                scrollThreshold: 100,
                ...options
            };

            this.isActive = false;
            this.hasScrolledToContent = false;
            this.init();
        }

        init() {
            this.setupDOM();
            this.loadUserPreference();
            this.setupEventListeners();
            
            if (this.isActive) {
                this.checkScrollPosition();
            }
        }

        setupDOM() {
            // Find main content container
            this.contentArea = $('.entry-content, article, .post-content, .site-content').first();
            
            // Find common theme containers to dim
            this.dimmableElements = $(
                'header, ' +
                '.site-header, ' +
                '.navbar, ' +
                '.nav-menu, ' +
                '#masthead, ' +
                '.sidebar, ' +
                '#secondary, ' +
                'aside, ' +
                'footer, ' +
                '.site-footer, ' +
                '#colophon, ' +
                '.wp-site-blocks > *:not(:has(.entry-content, article, .post-content, .site-content))'
            ).not(this.contentArea.parents());

            // Add necessary classes
            this.dimmableElements.addClass('drm-dimmable');
            
            // Create style tag for dynamic styles
            this.styleTag = $('<style>', {
                id: 'drm-dynamic-styles'
            }).appendTo('head');

            this.updateStyles();
        }

        updateStyles() {
            const css = `
                /* Transition for dimmable elements */
                .drm-dimmable {
                    transition: opacity ${this.options.transitionSpeed}ms ease !important;
                }

                /* Dimmed state */
                body.drm-active .drm-dimmable {
                    opacity: ${this.options.dimmingIntensity} !important;
                }

                /* Ensure toggle button stays visible */
                .drm-toggle-button {
                    opacity: 1 !important;
                    z-index: 999999 !important;
                }
            `;
            this.styleTag.html(css);
        }

        setupEventListeners() {
            // Toggle button click
            $('.drm-toggle-button').on('click', () => this.toggle());

            // Keyboard shortcut
            $(document).on('keydown', (e) => {
                if (e.altKey && e.key === 'f') {
                    this.toggle();
                }
            });

            // Scroll event for initial activation
            $(window).on('scroll', () => {
                if (this.isActive) {
                    this.checkScrollPosition();
                }
            });
        }

        checkScrollPosition() {
            const scrollTop = $(window).scrollTop();

            if (scrollTop > this.options.scrollThreshold) {
                if (!this.hasScrolledToContent) {
                    this.hasScrolledToContent = true;
                    this.applyDimming();
                }
            } else {
                if (this.hasScrolledToContent) {
                    this.hasScrolledToContent = false;
                    this.removeDimming();
                }
            }
        }

        applyDimming() {
            $('body').addClass('drm-active');
        }

        removeDimming() {
            $('body').removeClass('drm-active');
        }

        toggle() {
            if (this.isActive) {
                this.deactivate();
            } else {
                this.activate();
            }
        }

        activate() {
            this.isActive = true;
            this.checkScrollPosition();
            $('.drm-toggle-button').addClass('drm-active');
            this.saveUserPreference(true);
        }

        deactivate() {
            this.isActive = false;
            this.hasScrolledToContent = false;
            $('body').removeClass('drm-active');
            $('.drm-toggle-button').removeClass('drm-active');
            this.saveUserPreference(false);
        }

        saveUserPreference(isActive) {
            const expiryDate = new Date();
            expiryDate.setFullYear(expiryDate.getFullYear() + 1);
            document.cookie = `drm_active=${isActive ? '1' : '0'}; expires=${expiryDate.toUTCString()}; path=/; SameSite=Lax`;
        }

        loadUserPreference() {
            const match = document.cookie.match(/drm_active=([01])/);
            if (match) {
                this.isActive = match[1] === '1';
            }
        }
    }

    // Initialize when document is ready
    $(document).ready(() => {
        if (typeof drmSettings !== 'undefined') {
            new ReaderMode(drmSettings);
        }
    });

})(jQuery);