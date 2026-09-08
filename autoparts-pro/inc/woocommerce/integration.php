<?php
/**
 * AutoParts Pro WooCommerce Integration
 * 
 * @package AutoParts_Pro
 * @since 1.0.0
 */

namespace AutoPartsPro\WooCommerce;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * WooCommerce Integration Class
 */
class WooCommerce_Integration {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('after_setup_theme', [$this, 'woocommerce_support']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_woocommerce_styles'], 20);
        
        // Product catalog hooks
        add_action('woocommerce_before_shop_loop_item_title', [$this, 'add_wishlist_button'], 9);
        add_action('woocommerce_before_shop_loop_item_title', [$this, 'add_compatibility_badge'], 8);
        add_action('woocommerce_after_shop_loop_item', [$this, 'add_part_number'], 9);
        
        // Single product hooks
        add_action('woocommerce_single_product_summary', [$this, 'add_part_numbers'], 15);
        add_action('woocommerce_single_product_summary', [$this, 'add_vehicle_compatibility'], 25);
        add_action('woocommerce_single_product_summary', [$this, 'add_share_buttons'], 35);
        
        // Modify product gallery
        add_filter('woocommerce_gallery_thumbnail_size', [$this, 'gallery_thumbnail_size']);
        add_filter('woocommerce_gallery_image_size', [$this, 'gallery_image_size']);
        
        // Cart and checkout
        add_action('woocommerce_cart_totals_before_order_total', [$this, 'add_bulk_discount_info']);
        add_filter('woocommerce_add_to_cart_validation', [$this, 'validate_vehicle_compatibility'], 10, 3);
        
        // Custom product tabs
        add_filter('woocommerce_product_tabs', [$this, 'add_custom_tabs']);
        
        // Order tracking
        add_shortcode('app_order_tracking', [$this, 'order_tracking_shortcode']);
        
        // Flash deals
        add_action('wp_ajax_app_get_flash_deal', [$this, 'ajax_get_flash_deal']);
        add_action('wp_ajax_nopriv_app_get_flash_deal', [$this, 'ajax_get_flash_deal']);
    }
    
    /**
     * Declare WooCommerce support
     */
    public function woocommerce_support() {
        add_theme_support('woocommerce');
        add_theme_support('wc-product-gallery-zoom');
        add_theme_support('wc-product-gallery-lightbox');
        add_theme_support('wc-product-gallery-slider');
    }
    
    /**
     * Enqueue WooCommerce-specific styles
     */
    public function enqueue_woocommerce_styles() {
        if (!class_exists('WooCommerce')) {
            return;
        }
        
        wp_enqueue_style(
            'app-woocommerce',
            get_template_directory_uri() . '/assets/css/woocommerce.css',
            ['woocommerce-general'],
            APP_VERSION
        );
    }
    
    /**
     * Add wishlist button to product cards
     */
    public function add_wishlist_button() {
        global $product;
        ?>
        <button class="app-wishlist-btn" data-product-id="<?php echo esc_attr($product->get_id()); ?>" aria-label="<?php esc_attr_e('Add to wishlist', 'autoparts-pro'); ?>">
            <svg class="icon-heart" viewBox="0 0 24 24">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
        </button>
        <?php
    }
    
    /**
     * Add compatibility badge to product cards
     */
    public function add_compatibility_badge() {
        global $product;
        $vehicle_id = isset($_COOKIE['app_selected_vehicle']) ? absint($_COOKIE['app_selected_vehicle']) : 0;
        
        if ($vehicle_id && $this->check_product_compatibility($product->get_id(), $vehicle_id)) {
            echo '<span class="app-compatibility-badge fits">' . __('Fits Your Vehicle', 'autoparts-pro') . '</span>';
        }
    }
    
    /**
     * Add part number to product cards
     */
    public function add_part_number() {
        global $product;
        $part_number = $product->get_meta('_part_number');
        
        if ($part_number) {
            echo '<div class="app-part-number"><small>' . esc_html__('Part #:', 'autoparts-pro') . ' ' . esc_html($part_number) . '</small></div>';
        }
    }
    
    /**
     * Add part numbers to single product
     */
    public function add_part_numbers() {
        global $product;
        $part_number = $product->get_meta('_part_number');
        $oem_number = $product->get_meta('_oem_number');
        $cross_ref = $product->get_meta('_cross_reference_numbers');
        
        if (!$part_number && !$oem_number && !$cross_ref) {
            return;
        }
        ?>
        <div class="app-part-numbers">
            <?php if ($part_number): ?>
                <div class="part-number-row">
                    <strong><?php _e('Part Number:', 'autoparts-pro'); ?></strong>
                    <span><?php echo esc_html($part_number); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ($oem_number): ?>
                <div class="part-number-row">
                    <strong><?php _e('OEM Number:', 'autoparts-pro'); ?></strong>
                    <span><?php echo esc_html($oem_number); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ($cross_ref): ?>
                <div class="part-number-row">
                    <strong><?php _e('Cross Reference:', 'autoparts-pro'); ?></strong>
                    <span><?php echo esc_html($cross_ref); ?></span>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Add vehicle compatibility section
     */
    public function add_vehicle_compatibility() {
        global $product;
        $compatible_vehicles = $product->get_meta('_compatible_vehicles');
        
        if (empty($compatible_vehicles)) {
            return;
        }
        ?>
        <div class="app-vehicle-compatibility">
            <h3><?php _e('Vehicle Compatibility', 'autoparts-pro'); ?></h3>
            <div class="compatibility-list">
                <?php foreach ((array)$compatible_vehicles as $vehicle): ?>
                    <div class="vehicle-item">
                        <span class="dashicons dashicons-yes"></span>
                        <?php echo esc_html($vehicle); ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="app-check-fit-btn" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
                <?php _e('Check if this fits your vehicle', 'autoparts-pro'); ?>
            </button>
        </div>
        <?php
    }
    
    /**
     * Add share buttons
     */
    public function add_share_buttons() {
        global $product;
        $product_url = get_permalink($product->get_id());
        $product_title = $product->get_name();
        ?>
        <div class="app-share-buttons">
            <span class="share-label"><?php _e('Share:', 'autoparts-pro'); ?></span>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($product_url); ?>" target="_blank" rel="noopener" class="share-btn facebook" aria-label="<?php esc_attr_e('Share on Facebook', 'autoparts-pro'); ?>">
                <span class="dashicons dashicons-facebook"></span>
            </a>
            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($product_url); ?>&text=<?php echo urlencode($product_title); ?>" target="_blank" rel="noopener" class="share-btn twitter" aria-label="<?php esc_attr_e('Share on Twitter', 'autoparts-pro'); ?>">
                <span class="dashicons dashicons-twitter"></span>
            </a>
            <a href="https://pinterest.com/pin/create/button/?url=<?php echo urlencode($product_url); ?>&description=<?php echo urlencode($product_title); ?>" target="_blank" rel="noopener" class="share-btn pinterest" aria-label="<?php esc_attr_e('Share on Pinterest', 'autoparts-pro'); ?>">
                <span class="dashicons dashicons-pinterest"></span>
            </a>
            <a href="mailto:?subject=<?php echo urlencode($product_title); ?>&body=<?php echo urlencode($product_url); ?>" class="share-btn email" aria-label="<?php esc_attr_e('Share via Email', 'autoparts-pro'); ?>">
                <span class="dashicons dashicons-email"></span>
            </a>
            <button type="button" class="share-btn copy-link" data-url="<?php echo esc_url($product_url); ?>" aria-label="<?php esc_attr_e('Copy Link', 'autoparts-pro'); ?>">
                <span class="dashicons dashicons-admin-links"></span>
            </button>
        </div>
        <?php
    }
    
    /**
     * Gallery thumbnail size
     */
    public function gallery_thumbnail_size($size) {
        return 'thumbnail';
    }
    
    /**
     * Gallery image size
     */
    public function gallery_image_size($size) {
        return 'large';
    }
    
    /**
     * Add bulk discount info to cart
     */
    public function add_bulk_discount_info() {
        ?>
        <div class="app-bulk-discount-info">
            <p><?php _e('Bulk discounts available for orders over 10 items!', 'autoparts-pro'); ?></p>
        </div>
        <?php
    }
    
    /**
     * Validate vehicle compatibility before adding to cart
     */
    public function validate_vehicle_compatibility($passed, $product_id, $quantity) {
        $check_compatibility = get_option('app_check_vehicle_compatibility', false);
        
        if (!$check_compatibility) {
            return $passed;
        }
        
        $vehicle_id = isset($_COOKIE['app_selected_vehicle']) ? absint($_COOKIE['app_selected_vehicle']) : 0;
        
        if ($vehicle_id && !$this->check_product_compatibility($product_id, $vehicle_id)) {
            wc_add_notice(__('This part may not be compatible with your selected vehicle. Please verify before ordering.', 'autoparts-pro'), 'warning');
        }
        
        return $passed;
    }
    
    /**
     * Check product compatibility with vehicle
     */
    private function check_product_compatibility($product_id, $vehicle_id) {
        $compatible_vehicles = get_post_meta($product_id, '_compatible_vehicles', true);
        
        if (empty($compatible_vehicles)) {
            return true; // No restrictions means compatible
        }
        
        $vehicle = get_post($vehicle_id);
        if (!$vehicle || $vehicle->post_type !== 'app_vehicle') {
            return false;
        }
        
        $vehicle_year = get_post_meta($vehicle_id, '_vehicle_year', true);
        $vehicle_make = get_post_meta($vehicle_id, '_vehicle_make', true);
        $vehicle_model = get_post_meta($vehicle_id, '_vehicle_model', true);
        
        foreach ((array)$compatible_vehicles as $compatible) {
            if (strpos($compatible, $vehicle_year) !== false &&
                strpos($compatible, $vehicle_make) !== false &&
                strpos($compatible, $vehicle_model) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Add custom product tabs
     */
    public function add_custom_tabs($tabs) {
        global $product;
        
        // Installation Guide tab
        $installation_guide = $product->get_meta('_installation_guide');
        if ($installation_guide) {
            $tabs['installation'] = [
                'title' => __('Installation Guide', 'autoparts-pro'),
                'priority' => 50,
                'callback' => [$this, 'display_installation_tab'],
            ];
        }
        
        // Specifications tab
        $specs = $product->get_meta('_specifications');
        if ($specs) {
            $tabs['specifications'] = [
                'title' => __('Specifications', 'autoparts-pro'),
                'priority' => 45,
                'callback' => [$this, 'display_specifications_tab'],
            ];
        }
        
        // Reviews tab priority adjustment
        if (isset($tabs['reviews'])) {
            $tabs['reviews']['priority'] = 60;
        }
        
        return $tabs;
    }
    
    /**
     * Display installation guide tab content
     */
    public function display_installation_tab() {
        global $product;
        $guide = $product->get_meta('_installation_guide');
        echo wp_kses_post(wpautop($guide));
    }
    
    /**
     * Display specifications tab content
     */
    public function display_specifications_tab() {
        global $product;
        $specs = $product->get_meta('_specifications');
        
        if (is_array($specs)) {
            echo '<table class="app-specs-table">';
            foreach ($specs as $key => $value) {
                echo '<tr><th>' . esc_html($key) . '</th><td>' . esc_html($value) . '</td></tr>';
            }
            echo '</table>';
        } else {
            echo wp_kses_post(wpautop($specs));
        }
    }
    
    /**
     * Order tracking shortcode
     */
    public function order_tracking_shortcode($atts) {
        $atts = shortcode_atts([
            'title' => __('Track Your Order', 'autoparts-pro'),
        ], $atts);
        
        ob_start();
        ?>
        <div class="app-order-tracking">
            <h2><?php echo esc_html($atts['title']); ?></h2>
            <form class="app-tracking-form" method="post">
                <p><?php _e('Enter your order number and email to track your shipment.', 'autoparts-pro'); ?></p>
                <div class="form-row">
                    <label for="order-number"><?php _e('Order Number', 'autoparts-pro'); ?></label>
                    <input type="text" id="order-number" name="order_number" required />
                </div>
                <div class="form-row">
                    <label for="order-email"><?php _e('Email Address', 'autoparts-pro'); ?></label>
                    <input type="email" id="order-email" name="order_email" required />
                </div>
                <button type="submit" class="button alt"><?php _e('Track Order', 'autoparts-pro'); ?></button>
            </form>
            <div class="tracking-result"></div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * AJAX: Get flash deal
     */
    public function ajax_get_flash_deal() {
        check_ajax_referer('app_builder_nonce', 'nonce');
        
        $args = [
            'post_type' => 'product',
            'posts_per_page' => 1,
            'meta_query' => [
                [
                    'key' => '_flash_deal',
                    'value' => '1',
                    'compare' => '='
                ],
                [
                    'key' => '_flash_deal_end',
                    'value' => current_time('timestamp'),
                    'compare' => '>',
                    'type' => 'NUMERIC'
                ]
            ]
        ];
        
        $query = new \WP_Query($args);
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                global $product;
                
                $end_time = get_post_meta(get_the_ID(), '_flash_deal_end', true);
                $discount = get_post_meta(get_the_ID(), '_flash_deal_discount', true);
                
                wp_send_json_success([
                    'product_id' => get_the_ID(),
                    'title' => get_the_title(),
                    'permalink' => get_permalink(),
                    'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
                    'price' => $product->get_price_html(),
                    'discount' => $discount,
                    'end_time' => $end_time,
                ]);
            }
        } else {
            wp_send_json_error(['message' => __('No active flash deals', 'autoparts-pro')]);
        }
        
        wp_reset_postdata();
    }
}

// Initialize
WooCommerce_Integration::get_instance();
