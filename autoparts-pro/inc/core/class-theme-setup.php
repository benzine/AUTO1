<?php
/**
 * Theme Setup and Core Configuration
 * 
 * @package AutoParts_Pro
 * @since 1.0.0
 */

namespace AutoParts_Pro\Core;

defined('ABSPATH') || exit;

class Theme_Setup {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('after_setup_theme', [$this, 'theme_setup']);
        add_action('init', [$this, 'register_image_sizes']);
        add_action('widgets_init', [$this, 'register_sidebars']);
        add_filter('template_include', [$this, 'custom_template_loader']);
    }
    
    public function theme_setup() {
        load_theme_textdomain('autoparts-pro', get_template_directory() . '/languages');
        
        add_theme_support('title-tag');
        add_theme_support('automatic-feed-links');
        
        add_theme_support('html5', [
            'search-form', 'comment-form', 'comment-list', 'gallery', 
            'caption', 'style', 'script', 'navigation-widgets'
        ]);
        
        add_theme_support('post-formats', ['aside', 'gallery', 'link', 'image', 'quote', 'video', 'audio']);
        add_theme_support('post-thumbnails');
        set_post_thumbnail_size(1200, 675, true);
        
        add_theme_support('custom-logo', [
            'height'      => 80,
            'width'       => 300,
            'flex-height' => true,
            'flex-width'  => true,
            'header-text' => ['site-title', 'site-description']
        ]);
        
        add_theme_support('custom-background', ['default-color' => '0a0a0a']);
        add_theme_support('custom-header', [
            'width'       => 1920,
            'height'      => 400,
            'flex-height' => true,
            'flex-width'  => true,
            'video'       => true
        ]);
        
        add_theme_support('align-wide');
        add_theme_support('responsive-embeds');
        add_theme_support('editor-styles');
        add_editor_style('assets/css/editor-style.css');
        
        add_theme_support('woocommerce');
        add_theme_support('wc-product-gallery-zoom');
        add_theme_support('wc-product-gallery-lightbox');
        add_theme_support('wc-product-gallery-slider');
        
        add_theme_support('wp-block-styles');
        add_theme_support('editor-color-palette', [
            ['name' => __('Racing Red', 'autoparts-pro'), 'slug' => 'racing-red', 'color' => '#DC2626'],
            ['name' => __('Obsidian Black', 'autoparts-pro'), 'slug' => 'obsidian-black', 'color' => '#0A0A0A'],
            ['name' => __('Charcoal Grey', 'autoparts-pro'), 'slug' => 'charcoal-grey', 'color' => '#2D2D2D'],
            ['name' => __('Steel Grey', 'autoparts-pro'), 'slug' => 'steel-grey', 'color' => '#6B7280'],
            ['name' => __('Pure White', 'autoparts-pro'), 'slug' => 'pure-white', 'color' => '#FFFFFF'],
            ['name' => __('Off-White', 'autoparts-pro'), 'slug' => 'off-white', 'color' => '#F5F5F5'],
            ['name' => __('Burnt Orange', 'autoparts-pro'), 'slug' => 'burnt-orange', 'color' => '#EA580C'],
        ]);
    }
    
    public function register_image_sizes() {
        add_image_size('autoparts-product-sm', 300, 300, true);
        add_image_size('autoparts-product-md', 600, 600, true);
        add_image_size('autoparts-product-lg', 1200, 1200, true);
        add_image_size('autoparts-hero-full', 1920, 1080, true);
        add_image_size('autoparts-hero-mobile', 768, 1024, true);
        add_image_size('autoparts-category', 400, 300, true);
        add_image_size('autoparts-brand', 200, 150, true);
        add_image_size('autoparts-blog-thumb', 400, 250, true);
    }
    
    public function register_sidebars() {
        $sidebars = [
            'sidebar-main'   => __('Main Sidebar', 'autoparts-pro'),
            'sidebar-shop'   => __('Shop Sidebar', 'autoparts-pro'),
            'sidebar-product'=> __('Product Details Sidebar', 'autoparts-pro'),
            'header-top'     => __('Header Top Bar', 'autoparts-pro'),
            'sidebar-mobile' => __('Mobile Sidebar', 'autoparts-pro'),
        ];
        
        foreach ($sidebars as $id => $name) {
            register_sidebar([
                'name'          => $name,
                'id'            => $id,
                'description'   => sprintf(__('Widgets for %s.', 'autoparts-pro'), $name),
                'before_widget' => '<div id="%1$s" class="widget %2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<h3 class="widget-title">',
                'after_title'   => '</h3>',
            ]);
        }
        
        for ($i = 1; $i <= 4; $i++) {
            register_sidebar([
                'name'          => sprintf(__('Footer Column %d', 'autoparts-pro'), $i),
                'id'            => "footer-$i",
                'before_widget' => '<div id="%1$s" class="widget widget-footer %2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<h4 class="widget-title">',
                'after_title'   => '</h4>',
            ]);
        }
    }
    
    public function custom_template_loader($template) {
        if (is_page_template('page-templates/wishlist.php')) {
            $wishlist_template = locate_template('page-templates/wishlist.php');
            if ($wishlist_template) return $wishlist_template;
        }
        
        if (is_page_template('page-templates/order-tracking.php')) {
            $tracking_template = locate_template('page-templates/order-tracking.php');
            if ($tracking_template) return $tracking_template;
        }
        
        return $template;
    }
}

Theme_Setup::get_instance();
