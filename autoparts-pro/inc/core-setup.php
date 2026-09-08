<?php
/**
 * Core theme setup and configuration
 *
 * @package AutoParts_Pro
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Set up theme defaults and register supported WordPress features
 */
function autoparts_pro_setup() {
    // Make theme available for translation
    load_theme_textdomain('autoparts-pro', get_template_directory() . '/languages');

    // Add default posts feed to RSS head
    add_feed('latest_posts', 'autoparts_pro_latest_posts_feed');

    // Add theme support for various features
    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-background', array(
        'default-color' => '0a0a0a',
        'default-image' => '',
    ));
    add_theme_support('custom-logo', array(
        'height'      => 80,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    add_theme_support('custom-header', array(
        'default-image'      => '',
        'default-text-color' => 'ffffff',
        'width'              => 1920,
        'height'             => 400,
        'flex-width'         => true,
        'flex-height'        => true,
    ));
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    add_theme_support('wp-block-styles');
    add_theme_support('editor-styles');
    add_editor_style('assets/css/editor-style.css');

    // WooCommerce support
    if (class_exists('WooCommerce')) {
        add_theme_support('woocommerce');
        add_theme_support('wc-product-gallery-zoom');
        add_theme_support('wc-product-gallery-lightbox');
        add_theme_support('wc-product-gallery-slider');
    }

    // Register navigation menus
    register_nav_menus(array(
        'primary'   => esc_html__('Primary Menu (Gear Shift)', 'autoparts-pro'),
        'secondary' => esc_html__('Secondary Menu', 'autoparts-pro'),
        'mobile'    => esc_html__('Mobile Menu', 'autoparts-pro'),
        'footer'    => esc_html__('Footer Menu', 'autoparts-pro'),
    ));

    // Set content width
    if (!isset($content_width)) {
        $content_width = 1200;
    }
}
add_action('after_setup_theme', 'autoparts_pro_setup', 0);

/**
 * Register widget areas
 */
function autoparts_pro_widgets_init() {
    register_sidebar(array(
        'name'          => esc_html__('Main Sidebar', 'autoparts-pro'),
        'id'            => 'sidebar-1',
        'description'   => esc_html__('Add widgets here to appear in your sidebar.', 'autoparts-pro'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => esc_html__('Shop Sidebar', 'autoparts-pro'),
        'id'            => 'shop-sidebar',
        'description'   => esc_html__('Add widgets here to appear in your shop sidebar.', 'autoparts-pro'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => esc_html__('Footer Widget Area 1', 'autoparts-pro'),
        'id'            => 'footer-1',
        'description'   => esc_html__('First footer widget area.', 'autoparts-pro'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget-title">',
        'after_title'   => '</h4>',
    ));

    register_sidebar(array(
        'name'          => esc_html__('Footer Widget Area 2', 'autoparts-pro'),
        'id'            => 'footer-2',
        'description'   => esc_html__('Second footer widget area.', 'autoparts-pro'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget-title">',
        'after_title'   => '</h4>',
    ));

    register_sidebar(array(
        'name'          => esc_html__('Footer Widget Area 3', 'autoparts-pro'),
        'id'            => 'footer-3',
        'description'   => esc_html__('Third footer widget area.', 'autoparts-pro'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget-title">',
        'after_title'   => '</h4>',
    ));

    register_sidebar(array(
        'name'          => esc_html__('Footer Widget Area 4', 'autoparts-pro'),
        'id'            => 'footer-4',
        'description'   => esc_html__('Fourth footer widget area.', 'autoparts-pro'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget-title">',
        'after_title'   => '</h4>',
    ));

    register_sidebar(array(
        'name'          => esc_html__('Header CTA', 'autoparts-pro'),
        'id'            => 'header-cta',
        'description'   => esc_html__('Call-to-action area in header.', 'autoparts-pro'),
        'before_widget' => '<div id="%1$s" class="header-cta-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<span class="screen-reader-text">',
        'after_title'   => '</span>',
    ));

    if (class_exists('WooCommerce')) {
        register_sidebar(array(
            'name'          => esc_html__('Product Details Sidebar', 'autoparts-pro'),
            'id'            => 'product-sidebar',
            'description'   => esc_html__('Sidebar for product detail pages.', 'autoparts-pro'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        ));
    }
}
add_action('widgets_init', 'autoparts_pro_widgets_init');

/**
 * Set default post thumbnail size
 */
function autoparts_pro_set_post_thumbnail_size() {
    set_post_thumbnail_size(600, 400, true);
    add_image_size('autoparts-pro-large', 1200, 800, true);
    add_image_size('autoparts-pro-medium', 800, 600, true);
    add_image_size('autoparts-pro-thumbnail', 400, 300, true);
    add_image_size('autoparts-pro-hero', 1920, 1080, true);
}
add_action('after_setup_theme', 'autoparts_pro_set_post_thumbnail_size');

/**
 * Custom image output sizes
 */
function autoparts_pro_content_width() {
    $GLOBALS['content_width'] = apply_filters('autoparts_pro_content_width', 1200);
}
add_action('template_redirect', 'autoparts_pro_content_width');

/**
 * Add custom body classes
 */
function autoparts_pro_body_classes($classes) {
    // Add class if sidebar is active
    if (is_active_sidebar('sidebar-1') && !is_page()) {
        $classes[] = 'has-sidebar';
    }

    // Add class for singular pages
    if (is_singular()) {
        $classes[] = 'singular';
    }

    // Add class for dark/light mode
    $theme_mode = autoparts_pro_get_option('default_theme_mode', 'dark');
    $classes[] = 'theme-mode-' . esc_attr($theme_mode);

    return $classes;
}
add_filter('body_class', 'autoparts_pro_body_classes');

/**
 * Add pingback header
 */
function autoparts_pro_pingback_header() {
    if (is_singular() && pings_open()) {
        printf('<link rel="pingback" href="%s">', esc_url(get_bloginfo('pingback_url')));
    }
}
add_action('wp_head', 'autoparts_pro_pingback_header');

/**
 * Preload key resources
 */
function autoparts_pro_resource_hints($urls, $relation_type) {
    if ('preconnect' === $relation_type) {
        $urls[] = array(
            'href' => 'https://fonts.googleapis.com',
        );
        $urls[] = array(
            'href' => 'https://fonts.gstatic.com',
            'crossorigin' => 'anonymous',
        );
    }
    return $urls;
}
add_filter('wp_resource_hints', 'autoparts_pro_resource_hints', 10, 2);

/**
 * Custom excerpt length
 */
function autoparts_pro_excerpt_length($length) {
    if (is_admin()) {
        return $length;
    }
    return 25;
}
add_filter('excerpt_length', 'autoparts_pro_excerpt_length');

/**
 * Custom excerpt more
 */
function autoparts_pro_excerpt_more($more) {
    if (is_admin()) {
        return $more;
    }
    return '&hellip;';
}
add_filter('excerpt_more', 'autoparts_pro_excerpt_more');

/**
 * Enable SVG uploads
 */
function autoparts_pro_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'autoparts_pro_mime_types');

/**
 * Disable emoji scripts for performance
 */
function autoparts_pro_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    add_filter('tiny_mce_plugins', 'autoparts_pro_disable_emojis_tinymce');
    add_filter('wp_resource_hints', 'autoparts_pro_disable_emojis_remove_dns_prefetch', 10, 2);
}
add_action('init', 'autoparts_pro_disable_emojis');

/**
 * Remove emoji tinymce plugin
 */
function autoparts_pro_disable_emojis_tinymce($plugins) {
    if (is_array($plugins)) {
        return array_diff($plugins, array('wpemoji'));
    }
    return array();
}

/**
 * Remove emoji DNS prefetch
 */
function autoparts_pro_disable_emojis_remove_dns_prefetch($urls, $relation_type) {
    if ('dns-prefetch' == $relation_type) {
        $emoji_svg_url = wp_parse_url(esc_url('//s.w.org/images/core/emoji/2/svg/'));
        $urls = array_diff($urls, array($emoji_svg_url['scheme'] . ':' . $emoji_svg_url['host']));
    }
    return $urls;
}

/**
 * Latest posts feed template
 */
function autoparts_pro_latest_posts_feed() {
    load_template(locate_template('feed-latest.php', false, false));
}
