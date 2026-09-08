<?php
/**
 * Enqueue Scripts and Styles
 * 
 * @package AutoParts_Pro
 * @since 1.0.0
 */

namespace AutoParts_Pro\Core;

defined('ABSPATH') || exit;

class Enqueue_Scripts {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('wp_localize_script', [$this, 'localize_scripts']);
    }
    
    public function enqueue_frontend_assets() {
        // Three.js for 3D engine
        wp_enqueue_script('three-js', 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js', [], 'r128', true);
        
        // GSAP for animations
        wp_enqueue_script('gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js', [], '3.12.2', true);
        wp_enqueue_script('gsap-scroll-trigger', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js', ['gsap'], '3.12.2', true);
        
        // Main theme CSS
        wp_enqueue_style('autoparts-style', get_stylesheet_uri(), [], '1.0.0');
        
        // Theme JavaScript
        wp_enqueue_script('autoparts-main', 
            get_template_directory_uri() . '/assets/js/main.js', 
            ['jquery', 'gsap'], 
            '1.0.0', 
            true
        );
        
        // Localize script with WordPress data
        wp_localize_script('autoparts-main', 'autopartsData', [
            'ajaxUrl'       => admin_url('admin-ajax.php'),
            'restUrl'       => rest_url('autoparts-pro/v1'),
            'nonce'         => wp_create_nonce('wp_rest'),
            'userId'        => get_current_user_id(),
            'isUserLoggedIn'=> is_user_logged_in(),
            'themeDir'      => get_template_directory_uri(),
            'siteUrl'       => site_url(),
            'homeUrl'       => home_url(),
            'wishlistCount' => self::get_wishlist_count(),
            'i18n'          => [
                'addToCart'     => __('Add to Cart', 'autoparts-pro'),
                'addedToCart'   => __('Added!', 'autoparts-pro'),
                'addToWishlist' => __('Add to Wishlist', 'autoparts-pro'),
                'inWishlist'    => __('In Wishlist', 'autoparts-pro'),
                'checkFitment'  => __('Check if this fits', 'autoparts-pro'),
                'fitsVehicle'   => __('Fits Your Vehicle', 'autoparts-pro'),
                'notFitment'    => __('Does not fit', 'autoparts-pro'),
                'loading'       => __('Loading...', 'autoparts-pro'),
                'error'         => __('Error occurred', 'autoparts-pro'),
            ]
        ]);
        
        // Comment reply script
        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }
    }
    
    public function enqueue_admin_assets($hook) {
        // Only load on theme admin pages
        if (strpos($hook, 'autoparts') === false && strpos($hook, 'toplevel_autoparts') === false) {
            return;
        }
        
        wp_enqueue_style('autoparts-admin', 
            get_template_directory_uri() . '/assets/css/admin.css', 
            [], 
            '1.0.0'
        );
        
        wp_enqueue_script('autoparts-admin', 
            get_template_directory_uri() . '/assets/js/admin.js', 
            ['jquery'], 
            '1.0.0', 
            true
        );
    }
    
    private static function get_wishlist_count() {
        if (is_user_logged_in()) {
            $wishlist = get_user_meta(get_current_user_id(), '_autoparts_wishlist', true);
            return is_array($wishlist) ? count($wishlist) : 0;
        } else {
            $wishlist = isset($_COOKIE['autoparts_wishlist']) ? json_decode(stripslashes($_COOKIE['autoparts_wishlist']), true) : [];
            return is_array($wishlist) ? count($wishlist) : 0;
        }
    }
}

Enqueue_Scripts::get_instance();
