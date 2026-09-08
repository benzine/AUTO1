<?php
/**
 * Enqueue scripts and styles for the theme
 *
 * @package AutoParts_Pro
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue all theme scripts and styles
 */
function autoparts_pro_scripts() {
    // Google Fonts - Rajdhani, Inter, JetBrains Mono
    wp_enqueue_style(
        'autoparts-pro-google-fonts',
        'https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap',
        array(),
        null
    );

    // Main stylesheet
    wp_enqueue_style(
        'autoparts-pro-style',
        get_stylesheet_uri(),
        array(),
        AUTOPARTS_PRO_VERSION
    );

    // Vendor CSS (Three.js, GSAP, etc.)
    wp_enqueue_style(
        'autoparts-pro-vendor',
        AUTOPARTS_PRO_ASSETS . 'css/vendor.min.css',
        array(),
        AUTOPARTS_PRO_VERSION
    );

    // Theme main CSS
    wp_enqueue_style(
        'autoparts-pro-main',
        AUTOPARTS_PRO_ASSETS . 'css/main.min.css',
        array('autoparts-pro-style', 'autoparts-pro-vendor'),
        AUTOPARTS_PRO_VERSION
    );

    // Custom cursor styles
    if (autoparts_pro_get_option('enable_custom_cursor', true)) {
        wp_enqueue_style(
            'autoparts-pro-cursor',
            AUTOPARTS_PRO_ASSETS . 'css/cursor.min.css',
            array('autoparts-pro-main'),
            AUTOPARTS_PRO_VERSION
        );
    }

    // Three.js for 3D rendering
    wp_enqueue_script(
        'three-js',
        'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js',
        array(),
        'r128',
        true
    );

    // GSAP for animations
    wp_enqueue_script(
        'gsap',
        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js',
        array(),
        '3.12.2',
        true
    );

    wp_enqueue_script(
        'gsap-scrolltrigger',
        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js',
        array('gsap'),
        '3.12.2',
        true
    );

    // Vendor JS bundle
    wp_enqueue_script(
        'autoparts-pro-vendor',
        AUTOPARTS_PRO_ASSETS . 'js/vendor.min.js',
        array('jquery'),
        AUTOPARTS_PRO_VERSION,
        true
    );

    // Main theme JS
    wp_enqueue_script(
        'autoparts-pro-main',
        AUTOPARTS_PRO_ASSETS . 'js/main.min.js',
        array('jquery', 'autoparts-pro-vendor', 'three-js', 'gsap'),
        AUTOPARTS_PRO_VERSION,
        true
    );

    // 3D Hero component
    if (is_front_page() || autoparts_pro_get_option('hero_on_all_pages', false)) {
        wp_enqueue_script(
            'autoparts-pro-hero-3d',
            AUTOPARTS_PRO_ASSETS . 'js/hero-3d.min.js',
            array('three-js', 'gsap', 'gsap-scrolltrigger'),
            AUTOPARTS_PRO_VERSION,
            true
        );
    }

    // Custom cursor JS
    if (autoparts_pro_get_option('enable_custom_cursor', true) && !wp_is_mobile()) {
        wp_enqueue_script(
            'autoparts-pro-cursor',
            AUTOPARTS_PRO_ASSETS . 'js/cursor.min.js',
            array('autoparts-pro-main'),
            AUTOPARTS_PRO_VERSION,
            true
        );
    }

    // Theme mode toggle (light/dark)
    wp_enqueue_script(
        'autoparts-pro-theme-toggle',
        AUTOPARTS_PRO_ASSETS . 'js/theme-toggle.min.js',
        array('autoparts-pro-main'),
        AUTOPARTS_PRO_VERSION,
        true
    );

    // Wishlist functionality
    wp_enqueue_script(
        'autoparts-pro-wishlist',
        AUTOPARTS_PRO_ASSETS . 'js/wishlist.min.js',
        array('jquery', 'autoparts-pro-main'),
        AUTOPARTS_PRO_VERSION,
        true
    );

    // Localize scripts with WordPress data
    wp_localize_script('autoparts-pro-main', 'autopartsProData', array(
        'ajaxUrl'          => admin_url('admin-ajax.php'),
        'restUrl'          => rest_url('autoparts-pro/v1'),
        'nonce'            => wp_create_nonce('wp_rest'),
        'themeUrl'         => get_template_directory_uri(),
        'siteUrl'          => get_site_url(),
        'currentUser'      => get_current_user_id(),
        'isWooCommerce'    => class_exists('WooCommerce'),
        'cartCount'        => function_exists('WC') ? WC()->cart->get_cart_contents_count() : 0,
        'wishlistCount'    => autoparts_pro_get_wishlist_count(),
        'strings'          => array(
            'addToCart'     => esc_html__('Add to Cart', 'autoparts-pro'),
            'addedToCart'   => esc_html__('Added!', 'autoparts-pro'),
            'viewCart'      => esc_html__('View Cart', 'autoparts-pro'),
            'loading'       => esc_html__('Loading...', 'autoparts-pro'),
            'error'         => esc_html__('An error occurred', 'autoparts-pro'),
            'searchPlaceholder' => esc_html__('Search parts...', 'autoparts-pro'),
        ),
    ));

    // Comment reply script
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'autoparts_pro_scripts');

/**
 * Get wishlist count
 */
function autoparts_pro_get_wishlist_count() {
    if (is_user_logged_in()) {
        $wishlist = get_user_meta(get_current_user_id(), '_autoparts_pro_wishlist', true);
        return is_array($wishlist) ? count($wishlist) : 0;
    } else {
        // Guest wishlist from cookie
        if (isset($_COOKIE['autoparts_pro_guest_wishlist'])) {
            $wishlist = json_decode(stripslashes($_COOKIE['autoparts_pro_guest_wishlist']), true);
            return is_array($wishlist) ? count($wishlist) : 0;
        }
    }
    return 0;
}

/**
 * Admin scripts and styles
 */
function autoparts_pro_admin_scripts($hook) {
    // Only load on theme pages
    if (strpos($hook, 'autoparts-pro') === false && $hook !== 'toplevel_page_autoparts-pro-backoffice') {
        return;
    }

    wp_enqueue_style(
        'autoparts-pro-admin',
        AUTOPARTS_PRO_ASSETS . 'css/admin.min.css',
        array('wp-color-picker'),
        AUTOPARTS_PRO_VERSION
    );

    wp_enqueue_script(
        'autoparts-pro-admin',
        AUTOPARTS_PRO_ASSETS . 'js/admin.min.js',
        array('jquery', 'wp-color-picker', 'jquery-ui-sortable'),
        AUTOPARTS_PRO_VERSION,
        true
    );

    wp_localize_script('autoparts-pro-admin', 'autopartsProAdmin', array(
        'ajaxUrl'       => admin_url('admin-ajax.php'),
        'nonce'         => wp_create_nonce('autoparts_pro_admin_nonce'),
        'themeUrl'      => get_template_directory_uri(),
        'strings'       => array(
            'confirmDelete' => esc_html__('Are you sure you want to delete this?', 'autoparts-pro'),
            'saving'        => esc_html__('Saving...', 'autoparts-pro'),
            'saved'         => esc_html__('Saved!', 'autoparts-pro'),
            'error'         => esc_html__('Error saving', 'autoparts-pro'),
        ),
    ));
}
add_action('admin_enqueue_scripts', 'autoparts_pro_admin_scripts');

/**
 * Add preload links for critical resources
 */
function autoparts_pro_preload_resources() {
    echo '<link rel="preload" href="' . esc_url(AUTOPARTS_PRO_ASSETS . 'fonts/rajdhani-latin.woff2') . '" as="font" type="font/woff2" crossorigin>';
    echo '<link rel="preload" href="' . esc_url(AUTOPARTS_PRO_ASSETS . 'fonts/inter-latin.woff2') . '" as="font" type="font/woff2" crossorigin>';
}
add_action('wp_head', 'autoparts_pro_preload_resources', 1);

/**
 * Remove unnecessary scripts for performance
 */
function autoparts_pro_remove_scripts() {
    // Remove WordPress default emoji script (already handled)
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    
    // Remove WP Generator meta tag for security
    remove_action('wp_head', 'wp_generator');
    
    // Remove wlwmanifest link
    remove_action('wp_head', 'wlwmanifest_link');
    
    // Remove RSD link
    remove_action('wp_head', 'rsd_link');
}
add_action('wp_enqueue_scripts', 'autoparts_pro_remove_scripts', 100);
