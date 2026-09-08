/**
 * Customizer Live Preview JavaScript
 *
 * @package AutoParts_Pro
 */

(function($) {
    'use strict';

    // Update site title in real-time
    wp.customize('blogname', function(value) {
        value.bind(function(to) {
            $('.site-title a').text(to);
        });
    });

    // Update site description in real-time
    wp.customize('blogdescription', function(value) {
        value.bind(function(to) {
            $('.site-description').text(to);
        });
    });

    // Update primary red color
    wp.customize('app_color_primary_red', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--app-primary-red', to);
        });
    });

    // Update hero headline
    wp.customize('app_hero_headline', function(value) {
        value.bind(function(to) {
            $('.hero-headline').text(to);
        });
    });

    // Update hero subheadline
    wp.customize('app_hero_subheadline', function(value) {
        value.bind(function(to) {
            $('.hero-subheadline').text(to);
        });
    });

    // Update dark mode background
    wp.customize('app_color_bg_dark', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--app-bg-primary', to);
        });
    });

    // Update dark mode secondary background
    wp.customize('app_color_bg_secondary_dark', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--app-bg-secondary', to);
        });
    });

    // Update dark mode text color
    wp.customize('app_color_text_dark', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--app-text-primary', to);
        });
    });

    // Update accent color
    wp.customize('app_color_accent', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--app-accent-color', to);
        });
    });

})(jQuery);
