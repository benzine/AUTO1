<?php
/**
 * Template Tags - Reusable template functions
 * 
 * @package AutoParts_Pro
 * @since 1.0.0
 */

namespace AutoParts_Pro\Core;

defined('ABSPATH') || exit;

class Template_Tags {
    
    /**
     * Display site logo with support for light/dark variants
     */
    public static function the_logo($args = []) {
        $defaults = [
            'light_logo' => get_theme_mod('logo_light'),
            'dark_logo'  => get_theme_mod('logo_dark'),
            'sticky_logo'=> get_theme_mod('logo_sticky'),
            'mobile_logo'=> get_theme_mod('logo_mobile'),
            'site_title' => get_bloginfo('name'),
            'height'     => 80,
            'width'      => 300,
        ];
        
        $args = wp_parse_args($args, $defaults);
        $is_dark_mode = get_theme_mod('enable_dark_mode', true);
        
        echo '<a href="' . esc_url(home_url('/')) . '" class="site-logo" rel="home">';
        
        if ($args['light_logo']) {
            $logo_class = $is_dark_mode ? 'logo-light' : 'logo-dark';
            echo wp_get_attachment_image($args['light_logo'], 'full', false, [
                'class' => "site-logo-img {$logo_class}",
                'alt'   => $args['site_title'],
                'height'=> $args['height'],
                'width' => $args['width'],
            ]);
        } else {
            echo '<span class="site-title">' . esc_html($args['site_title']) . '</span>';
        }
        
        echo '</a>';
    }
    
    /**
     * Display vehicle selector dropdown
     */
    public static function the_vehicle_selector($args = []) {
        $defaults = [
            'id'         => 'vehicle-selector-' . uniqid(),
            'show_label' => true,
            'compact'    => false,
        ];
        
        $args = wp_parse_args($args, $defaults);
        
        $years = self::get_available_years();
        $makes = self::get_available_makes();
        
        ?>
        <div id="<?php echo esc_attr($args['id']); ?>" class="vehicle-selector <?php echo $args['compact'] ? 'is-compact' : ''; ?>">
            <?php if ($args['show_label']) : ?>
                <label class="vehicle-selector-label"><?php _e('Select Your Vehicle', 'autoparts-pro'); ?></label>
            <?php endif; ?>
            
            <div class="vehicle-selector-form">
                <select class="vehicle-year-select" data-placeholder="<?php esc_attr_e('Year', 'autoparts-pro'); ?>">
                    <option value=""><?php _e('Year', 'autoparts-pro'); ?></option>
                    <?php foreach ($years as $year) : ?>
                        <option value="<?php echo esc_attr($year); ?>"><?php echo esc_html($year); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <select class="vehicle-make-select" disabled data-placeholder="<?php esc_attr_e('Make', 'autoparts-pro'); ?>">
                    <option value=""><?php _e('Make', 'autoparts-pro'); ?></option>
                </select>
                
                <select class="vehicle-model-select" disabled data-placeholder="<?php esc_attr_e('Model', 'autoparts-pro'); ?>">
                    <option value=""><?php _e('Model', 'autoparts-pro'); ?></option>
                </select>
                
                <select class="vehicle-engine-select" disabled data-placeholder="<?php esc_attr_e('Engine', 'autoparts-pro'); ?>">
                    <option value=""><?php _e('Engine', 'autoparts-pro'); ?></option>
                </select>
                
                <button type="button" class="vehicle-apply-btn" disabled>
                    <?php _e('Apply', 'autoparts-pro'); ?>
                </button>
            </div>
        </div>
        <?php
    }
    
    /**
     * Get available years from products
     */
    private static function get_available_years() {
        $years = [];
        $current_year = date('Y');
        
        for ($i = $current_year + 1; $i >= 1950; $i--) {
            $years[] = $i;
        }
        
        return apply_filters('autoparts_pro_available_years', $years);
    }
    
    /**
     * Get available makes
     */
    private static function get_available_makes() {
        $makes = get_terms([
            'taxonomy'   => 'vehicle_make',
            'hide_empty' => true,
            'orderby'    => 'name',
            'order'      => 'ASC',
        ]);
        
        if (is_wp_error($makes) || empty($makes)) {
            return ['Toyota', 'Honda', 'Ford', 'Chevrolet', 'BMW', 'Mercedes', 'Audi', 'Nissan'];
        }
        
        return wp_list_pluck($makes, 'name', 'slug');
    }
    
    /**
     * Display wishlist button
     */
    public static function the_wishlist_button($product_id = null) {
        if (!$product_id) {
            $product_id = get_the_ID();
        }
        
        $is_in_wishlist = self::is_product_in_wishlist($product_id);
        $icon_class = $is_in_wishlist ? 'wishlist-icon-filled' : 'wishlist-icon-outline';
        
        ?>
        <button class="wishlist-btn <?php echo $is_in_wishlist ? 'in-wishlist' : ''; ?>" 
                data-product-id="<?php echo esc_attr($product_id); ?>"
                aria-label="<?php echo $is_in_wishlist ? esc_attr__('Remove from wishlist', 'autoparts-pro') : esc_attr__('Add to wishlist', 'autoparts-pro'); ?>">
            <svg class="wishlist-icon <?php echo esc_attr($icon_class); ?>" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
            <span class="wishlist-text"><?php echo $is_in_wishlist ? __('Saved', 'autoparts-pro') : __('Wishlist', 'autoparts-pro'); ?></span>
        </button>
        <?php
    }
    
    /**
     * Check if product is in wishlist
     */
    public static function is_product_in_wishlist($product_id) {
        if (is_user_logged_in()) {
            $wishlist = get_user_meta(get_current_user_id(), '_autoparts_wishlist', true);
            return is_array($wishlist) && in_array($product_id, $wishlist);
        } else {
            $wishlist = isset($_COOKIE['autoparts_wishlist']) ? json_decode(stripslashes($_COOKIE['autoparts_wishlist']), true) : [];
            return is_array($wishlist) && in_array($product_id, $wishlist);
        }
    }
    
    /**
     * Display compatibility badge
     */
    public static function the_compatibility_badge($product_id = null) {
        if (!$product_id) {
            $product_id = get_the_ID();
        }
        
        $user_vehicle = self::get_user_garage_primary();
        
        if (!$user_vehicle) {
            return;
        }
        
        $fits = self::check_product_fitment($product_id, $user_vehicle);
        
        if ($fits) {
            echo '<span class="compatibility-badge fits-vehicle">';
            echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>';
            echo esc_html__('Fits Your Vehicle', 'autoparts-pro');
            echo '</span>';
        } else {
            echo '<span class="compatibility-badge not-fit">';
            echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';
            echo esc_html__('Check Fitment', 'autoparts-pro');
            echo '</span>';
        }
    }
    
    /**
     * Get user's primary vehicle from garage
     */
    private static function get_user_garage_primary() {
        if (!is_user_logged_in()) {
            return null;
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'autoparts_vehicle_garage';
        
        $vehicle = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE user_id = %d AND is_primary = 1 LIMIT 1",
            get_current_user_id()
        ));
        
        if (!$vehicle) {
            // Get most recent vehicle
            $vehicle = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table WHERE user_id = %d ORDER BY created_at DESC LIMIT 1",
                get_current_user_id()
            ));
        }
        
        return $vehicle;
    }
    
    /**
     * Check if product fits vehicle
     */
    private static function check_product_fitment($product_id, $vehicle) {
        if (!$vehicle) {
            return false;
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'autoparts_product_compatibility';
        
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table 
             WHERE product_id = %d 
             AND vehicle_year = %d 
             AND vehicle_make = %s 
             AND vehicle_model = %s",
            $product_id,
            $vehicle->vehicle_year,
            $vehicle->vehicle_make,
            $vehicle->vehicle_model
        ));
        
        return (int)$exists > 0;
    }
    
    /**
     * Display AI chatbot widget
     */
    public static function the_chatbot_widget() {
        $chatbot_enabled = get_theme_mod('enable_chatbot', true);
        if (!$chatbot_enabled) {
            return;
        }
        
        $greeting = get_theme_mod('chatbot_greeting', __('Hi! Need help finding the right part?', 'autoparts-pro'));
        $avatar = get_theme_mod('chatbot_avatar');
        
        ?>
        <div id="autoparts-chatbot" class="chatbot-widget" data-auto-open="<?php echo get_theme_mod('chatbot_auto_open', 'true'); ?>">
            <button class="chatbot-toggle" aria-label="<?php esc_attr_e('Open chat', 'autoparts-pro'); ?>">
                <?php if ($avatar) : ?>
                    <?php echo wp_get_attachment_image($avatar, 'thumbnail', false, ['class' => 'chatbot-avatar']); ?>
                <?php else : ?>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
                        <line x1="9" y1="9" x2="9.01" y2="9"></line>
                        <line x1="15" y1="9" x2="15.01" y2="9"></line>
                    </svg>
                <?php endif; ?>
                <span class="chatbot-badge" style="display: none;">0</span>
            </button>
            
            <div class="chatbot-window" style="display: none;">
                <div class="chatbot-header">
                    <h4><?php _e('Parts Assistant', 'autoparts-pro'); ?></h4>
                    <button class="chatbot-close" aria-label="<?php esc_attr_e('Close chat', 'autoparts-pro'); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                
                <div class="chatbot-messages">
                    <div class="chatbot-message bot">
                        <p><?php echo esc_html($greeting); ?></p>
                    </div>
                </div>
                
                <form class="chatbot-input-form">
                    <input type="text" class="chatbot-input" placeholder="<?php esc_attr_e('Ask about parts...', 'autoparts-pro'); ?>" />
                    <button type="submit" class="chatbot-submit">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        <?php
    }
    
    /**
     * Display back-to-top button
     */
    public static function the_back_to_top() {
        $icon_type = get_theme_mod('back_to_top_icon', 'arrow');
        ?>
        <button id="back-to-top" class="back-to-top-btn" aria-label="<?php esc_attr_e('Back to top', 'autoparts-pro'); ?>" style="display: none;">
            <?php if ($icon_type === 'gear') : ?>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                </svg>
            <?php else : ?>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="18 15 12 9 6 15"></polyline>
                </svg>
            <?php endif; ?>
        </button>
        <?php
    }
}
