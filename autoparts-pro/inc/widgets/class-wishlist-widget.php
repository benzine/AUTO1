<?php
/**
 * AutoParts Pro - Wishlist Widget
 * Displays user's wishlist with quick add-to-cart functionality
 *
 * @package AutoParts_Pro
 * @since 1.0.0
 */

namespace AutoPartsPro\Widgets;

class Wishlist_Widget extends \WP_Widget {

    public function __construct() {
        parent::__construct(
            'autoparts_wishlist',
            __('AutoParts: Wishlist', 'autoparts-pro'),
            array(
                'description' => __('Display user wishlist with quick actions', 'autoparts-pro'),
                'classname' => 'widget-autoparts-wishlist'
            )
        );
    }

    public function widget($args, $instance) {
        if (!function_exists('is_user_logged_in')) {
            return;
        }

        $title = !empty($instance['title']) ? $instance['title'] : __('My Wishlist', 'autoparts-pro');
        $show_count = !empty($instance['show_count']) ? true : false;
        $show_images = !empty($instance['show_images']) ? true : false;
        $max_items = !empty($instance['max_items']) ? absint($instance['max_items']) : 5;

        // Get wishlist from user meta or session
        $wishlist_items = $this->get_wishlist_items($max_items);

        echo $args['before_widget'];
        
        if ($title) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }

        ?>
        <div class="wishlist-widget-content">
            <?php if (empty($wishlist_items)) : ?>
                <p class="wishlist-empty"><?php esc_html_e('Your wishlist is empty.', 'autoparts-pro'); ?></p>
                <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="button button-small">
                    <?php esc_html_e('Browse Products', 'autoparts-pro'); ?>
                </a>
            <?php else : ?>
                <ul class="wishlist-items-list">
                    <?php foreach ($wishlist_items as $item) : 
                        $product = wc_get_product($item['product_id']);
                        if (!$product) continue;
                    ?>
                        <li class="wishlist-item" data-product-id="<?php echo esc_attr($item['product_id']); ?>">
                            <?php if ($show_images) : ?>
                                <div class="wishlist-item-image">
                                    <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>">
                                        <?php echo $product->get_image('thumbnail'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="wishlist-item-details">
                                <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>" class="product-title">
                                    <?php echo esc_html($product->get_name()); ?>
                                </a>
                                
                                <?php if ($product->get_price_html()) : ?>
                                    <span class="price"><?php echo $product->get_price_html(); ?></span>
                                <?php endif; ?>
                                
                                <?php if ($product->is_in_stock()) : ?>
                                    <button class="button add-to-cart-from-wishlist" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
                                        <?php esc_html_e('Add to Cart', 'autoparts-pro'); ?>
                                    </button>
                                <?php else : ?>
                                    <span class="out-of-stock"><?php esc_html_e('Out of Stock', 'autoparts-pro'); ?></span>
                                <?php endif; ?>
                                
                                <button class="remove-from-wishlist" data-product-id="<?php echo esc_attr($item['product_id']); ?>" aria-label="<?php esc_attr_e('Remove from wishlist', 'autoparts-pro'); ?>">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </button>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                
                <?php if (count($wishlist_items) >= $max_items && $show_count) : ?>
                    <p class="wishlist-count">
                        <?php 
                        printf(
                            wp_kses_post(__('Showing %d of %d items', 'autoparts-pro')),
                            count($wishlist_items),
                            $this->get_total_wishlist_count()
                        );
                        ?>
                    </p>
                <?php endif; ?>
                
                <div class="wishlist-actions">
                    <a href="<?php echo esc_url(get_permalink(get_option('autoparts_wishlist_page_id'))); ?>" class="button">
                        <?php esc_html_e('View All', 'autoparts-pro'); ?>
                    </a>
                    <button class="button add-all-to-cart">
                        <?php esc_html_e('Add All to Cart', 'autoparts-pro'); ?>
                    </button>
                </div>
            <?php endif; ?>
        </div>
        <?php

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('My Wishlist', 'autoparts-pro');
        $show_count = !empty($instance['show_count']) ? true : false;
        $show_images = !empty($instance['show_images']) ? true : false;
        $max_items = !empty($instance['max_items']) ? absint($instance['max_items']) : 5;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'autoparts-pro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <input class="checkbox" type="checkbox" id="<?php echo esc_attr($this->get_field_id('show_count')); ?>" name="<?php echo esc_attr($this->get_field_name('show_count')); ?>" <?php checked($show_count); ?>>
            <label for="<?php echo esc_attr($this->get_field_id('show_count')); ?>"><?php esc_html_e('Show item count', 'autoparts-pro'); ?></label>
        </p>
        <p>
            <input class="checkbox" type="checkbox" id="<?php echo esc_attr($this->get_field_id('show_images')); ?>" name="<?php echo esc_attr($this->get_field_name('show_images')); ?>" <?php checked($show_images); ?>>
            <label for="<?php echo esc_attr($this->get_field_id('show_images')); ?>"><?php esc_html_e('Show product images', 'autoparts-pro'); ?></label>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('max_items')); ?>"><?php esc_html_e('Max items to show:', 'autoparts-pro'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('max_items')); ?>" name="<?php echo esc_attr($this->get_field_name('max_items')); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($max_items); ?>" size="4">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['show_count'] = isset($new_instance['show_count']) ? true : false;
        $instance['show_images'] = isset($new_instance['show_images']) ? true : false;
        $instance['max_items'] = absint($new_instance['max_items']);
        return $instance;
    }

    private function get_wishlist_items($limit = 5) {
        $user_id = get_current_user_id();
        $wishlist = array();

        if ($user_id) {
            $wishlist = get_user_meta($user_id, '_autoparts_wishlist', true);
        } else {
            $wishlist = isset($_COOKIE['autoparts_wishlist']) ? json_decode(stripslashes($_COOKIE['autoparts_wishlist']), true) : array();
        }

        if (!is_array($wishlist)) {
            $wishlist = array();
        }

        return array_slice($wishlist, 0, $limit);
    }

    private function get_total_wishlist_count() {
        $user_id = get_current_user_id();
        
        if ($user_id) {
            $wishlist = get_user_meta($user_id, '_autoparts_wishlist', true);
        } else {
            $wishlist = isset($_COOKIE['autoparts_wishlist']) ? json_decode(stripslashes($_COOKIE['autoparts_wishlist']), true) : array();
        }

        return is_array($wishlist) ? count($wishlist) : 0;
    }
}
