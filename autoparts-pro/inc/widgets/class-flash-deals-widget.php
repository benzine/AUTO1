<?php
/**
 * AutoParts Pro - Flash Deals Widget
 * Displays products with active countdown timers
 *
 * @package AutoParts_Pro
 * @since 1.0.0
 */

namespace AutoPartsPro\Widgets;

class Flash_Deals_Widget extends \WP_Widget {

    public function __construct() {
        parent::__construct(
            'autoparts_flash_deals',
            __('AutoParts: Flash Deals', 'autoparts-pro'),
            array(
                'description' => __('Display products with countdown timers', 'autoparts-pro'),
                'classname' => 'widget-autoparts-flash-deals'
            )
        );
    }

    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Flash Deals', 'autoparts-pro');
        $number = !empty($instance['number']) ? absint($instance['number']) : 3;
        $show_timer = !empty($instance['show_timer']) ? true : false;
        $show_progress = !empty($instance['show_progress']) ? true : false;
        $category = !empty($instance['category']) ? sanitize_text_field($instance['category']) : '';

        // Query sale products
        $query_args = array(
            'post_type' => 'product',
            'posts_per_page' => $number,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => '_sale_price',
                    'value' => 0,
                    'compare' => '>',
                    'type' => 'NUMERIC'
                ),
                array(
                    'key' => '_sale_price_dates_from',
                    'value' => time(),
                    'compare' => '<=',
                    'type' => 'NUMERIC'
                ),
                array(
                    'key' => '_sale_price_dates_to',
                    'value' => time(),
                    'compare' => '>=',
                    'type' => 'NUMERIC'
                )
            )
        );

        if ($category) {
            $query_args['tax_query'][] = array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $category
            );
        }

        $products = new \WP_Query($query_args);

        echo $args['before_widget'];
        
        if ($title) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }

        if ($products->have_posts()) :
            ?>
            <div class="flash-deals-widget">
                <?php while ($products->have_posts()) : $products->the_post(); 
                    global $product;
                    
                    $sale_from = get_post_meta(get_the_ID(), '_sale_price_dates_from', true);
                    $sale_to = get_post_meta(get_the_ID(), '_sale_price_dates_to', true);
                    $regular_price = get_post_meta(get_the_ID(), '_regular_price', true);
                    $sale_price = get_post_meta(get_the_ID(), '_sale_price', true);
                    
                    // Calculate discount percentage
                    $discount_percent = 0;
                    if ($regular_price && $sale_price) {
                        $discount_percent = round((($regular_price - $sale_price) / $regular_price) * 100);
                    }
                ?>
                    <div class="flash-deal-item" data-product-id="<?php the_ID(); ?>" data-end-time="<?php echo esc_attr($sale_to); ?>">
                        <div class="deal-badge">
                            <span class="discount-percent">-<?php echo esc_html($discount_percent); ?>%</span>
                        </div>
                        
                        <a href="<?php the_permalink(); ?>" class="deal-product-link">
                            <?php echo $product->get_image('medium'); ?>
                            <h4 class="deal-product-title"><?php the_title(); ?></h4>
                        </a>
                        
                        <div class="deal-pricing">
                            <span class="regular-price"><?php echo wc_price($regular_price); ?></span>
                            <span class="sale-price"><?php echo wc_price($sale_price); ?></span>
                        </div>
                        
                        <?php if ($show_timer && $sale_to) : ?>
                            <div class="deal-timer" data-end-time="<?php echo esc_attr($sale_to * 1000); ?>">
                                <div class="timer-label"><?php esc_html_e('Ends in:', 'autoparts-pro'); ?></div>
                                <div class="timer-countdown">
                                    <span class="timer-days">00</span><span class="timer-separator">:</span>
                                    <span class="timer-hours">00</span><span class="timer-separator">:</span>
                                    <span class="timer-minutes">00</span><span class="timer-separator">:</span>
                                    <span class="timer-seconds">00</span>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($show_progress && $sale_from && $sale_to) : 
                            $total_time = $sale_to - $sale_from;
                            $elapsed_time = time() - $sale_from;
                            $progress_percent = min(100, max(0, ($elapsed_time / $total_time) * 100));
                        ?>
                            <div class="deal-progress">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo esc_attr($progress_percent); ?>%"></div>
                                </div>
                                <span class="progress-text"><?php echo esc_html(round($progress_percent)); ?>% claimed</span>
                            </div>
                        <?php endif; ?>
                        
                        <button class="button add-to-cart-deal" data-product-id="<?php the_ID(); ?>">
                            <?php esc_html_e('Add to Cart', 'autoparts-pro'); ?>
                        </button>
                    </div>
                <?php endwhile; ?>
                
                <div class="flash-deals-footer">
                    <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>?orderby=price&on_sale=true" class="button button-small">
                        <?php esc_html_e('View All Deals', 'autoparts-pro'); ?>
                    </a>
                </div>
            </div>
            <?php
        else :
            ?>
            <p class="no-deals"><?php esc_html_e('No active flash deals.', 'autoparts-pro'); ?></p>
            <?php
        endif;

        wp_reset_postdata();
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Flash Deals', 'autoparts-pro');
        $number = !empty($instance['number']) ? absint($instance['number']) : 3;
        $show_timer = !empty($instance['show_timer']) ? true : false;
        $show_progress = !empty($instance['show_progress']) ? true : false;
        $category = !empty($instance['category']) ? sanitize_text_field($instance['category']) : '';
        
        // Get product categories
        $categories = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false
        ));
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'autoparts-pro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php esc_html_e('Number of deals to show:', 'autoparts-pro'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" step="1" min="1" max="10" value="<?php echo esc_attr($number); ?>" size="4">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('category')); ?>"><?php esc_html_e('Category (optional):', 'autoparts-pro'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('category')); ?>" name="<?php echo esc_attr($this->get_field_name('category')); ?>">
                <option value=""><?php esc_html_e('All Categories', 'autoparts-pro'); ?></option>
                <?php foreach ($categories as $cat) : ?>
                    <option value="<?php echo esc_attr($cat->slug); ?>" <?php selected($category, $cat->slug); ?>><?php echo esc_html($cat->name); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <input class="checkbox" type="checkbox" id="<?php echo esc_attr($this->get_field_id('show_timer')); ?>" name="<?php echo esc_attr($this->get_field_name('show_timer')); ?>" <?php checked($show_timer); ?>>
            <label for="<?php echo esc_attr($this->get_field_id('show_timer')); ?>"><?php esc_html_e('Show countdown timer', 'autoparts-pro'); ?></label>
        </p>
        <p>
            <input class="checkbox" type="checkbox" id="<?php echo esc_attr($this->get_field_id('show_progress')); ?>" name="<?php echo esc_attr($this->get_field_name('show_progress')); ?>" <?php checked($show_progress); ?>>
            <label for="<?php echo esc_attr($this->get_field_id('show_progress')); ?>"><?php esc_html_e('Show progress bar', 'autoparts-pro'); ?></label>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['number'] = absint($new_instance['number']);
        $instance['show_timer'] = isset($new_instance['show_timer']) ? true : false;
        $instance['show_progress'] = isset($new_instance['show_progress']) ? true : false;
        $instance['category'] = sanitize_text_field($new_instance['category']);
        return $instance;
    }
}
