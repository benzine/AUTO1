<?php
/**
 * AutoParts Pro - Store Locator Widget
 * Displays nearby store locations with map integration
 *
 * @package AutoParts_Pro
 * @since 1.0.0
 */

namespace AutoPartsPro\Widgets;

class Store_Locator_Widget extends \WP_Widget {

    public function __construct() {
        parent::__construct(
            'autoparts_store_locator',
            __('AutoParts: Store Locator', 'autoparts-pro'),
            array(
                'description' => __('Display store locations with map', 'autoparts-pro'),
                'classname' => 'widget-autoparts-store-locator'
            )
        );
    }

    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Our Stores', 'autoparts-pro');
        $number = !empty($instance['number']) ? absint($instance['number']) : 3;
        $show_map = !empty($instance['show_map']) ? true : false;
        $map_height = !empty($instance['map_height']) ? absint($instance['map_height']) : 250;
        $show_directions = !empty($instance['show_directions']) ? true : false;
        
        // Get Google Maps API key from theme options
        $api_key = get_option('autoparts_google_maps_api_key', '');

        // Query store locations
        $stores = new \WP_Query(array(
            'post_type' => 'store_location',
            'posts_per_page' => $number,
            'orderby' => 'title',
            'order' => 'ASC'
        ));

        echo $args['before_widget'];
        
        if ($title) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }

        if ($stores->have_posts()) :
            ?>
            <div class="store-locator-widget">
                <?php if ($show_map && $api_key) : ?>
                    <div class="store-map" style="height: <?php echo esc_attr($map_height); ?>px;" 
                         data-api-key="<?php echo esc_attr($api_key); ?>"
                         data-stores='<?php 
                            $store_data = array();
                            while ($stores->have_posts()) : $stores->the_post();
                                $lat = get_post_meta(get_the_ID(), '_store_latitude', true);
                                $lng = get_post_meta(get_the_ID(), '_store_longitude', true);
                                if ($lat && $lng) {
                                    $store_data[] = array(
                                        'id' => get_the_ID(),
                                        'title' => get_the_title(),
                                        'lat' => floatval($lat),
                                        'lng' => floatval($lng),
                                        'address' => get_post_meta(get_the_ID(), '_store_address', true),
                                        'phone' => get_post_meta(get_the_ID(), '_store_phone', true),
                                        'hours' => get_post_meta(get_the_ID(), '_store_hours', true)
                                    );
                                }
                            endwhile;
                            echo esc_attr(json_encode($store_data));
                         ?>'
                         id="store-locator-map"></div>
                    <?php wp_reset_postdata(); ?>
                <?php endif; ?>

                <div class="store-list">
                    <?php while ($stores->have_posts()) : $stores->the_post(); 
                        $lat = get_post_meta(get_the_ID(), '_store_latitude', true);
                        $lng = get_post_meta(get_the_ID(), '_store_longitude', true);
                        $address = get_post_meta(get_the_ID(), '_store_address', true);
                        $phone = get_post_meta(get_the_ID(), '_store_phone', true);
                        $hours = get_post_meta(get_the_ID(), '_store_hours', true);
                        $email = get_post_meta(get_the_ID(), '_store_email', true);
                    ?>
                        <div class="store-item" data-store-id="<?php the_ID(); ?>">
                            <h4 class="store-title"><?php the_title(); ?></h4>
                            
                            <?php if ($address) : ?>
                                <div class="store-address">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                    <span><?php echo nl2br(esc_html($address)); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($phone) : ?>
                                <div class="store-phone">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                    </svg>
                                    <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($email) : ?>
                                <div class="store-email">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                        <polyline points="22,6 12,13 2,6"></polyline>
                                    </svg>
                                    <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($hours) : ?>
                                <div class="store-hours">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                    <span><?php echo nl2br(esc_html($hours)); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($show_directions && $lat && $lng) : ?>
                                <div class="store-actions">
                                    <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo urlencode($address); ?>" 
                                       target="_blank" 
                                       rel="noopener noreferrer" 
                                       class="button button-small get-directions">
                                        <?php esc_html_e('Get Directions', 'autoparts-pro'); ?>
                                    </a>
                                    <button class="button button-small view-on-map" data-lat="<?php echo esc_attr($lat); ?>" data-lng="<?php echo esc_attr($lng); ?>">
                                        <?php esc_html_e('View on Map', 'autoparts-pro'); ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>

                <div class="store-locator-footer">
                    <a href="<?php echo esc_url(get_post_type_archive_link('store_location')); ?>" class="button button-small">
                        <?php esc_html_e('View All Stores', 'autoparts-pro'); ?>
                    </a>
                </div>
            </div>
            <?php
        else :
            ?>
            <p class="no-stores"><?php esc_html_e('No store locations found.', 'autoparts-pro'); ?></p>
            <?php
        endif;

        wp_reset_postdata();
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Our Stores', 'autoparts-pro');
        $number = !empty($instance['number']) ? absint($instance['number']) : 3;
        $show_map = !empty($instance['show_map']) ? true : false;
        $map_height = !empty($instance['map_height']) ? absint($instance['map_height']) : 250;
        $show_directions = !empty($instance['show_directions']) ? true : false;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'autoparts-pro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php esc_html_e('Number of stores to show:', 'autoparts-pro'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" step="1" min="1" max="10" value="<?php echo esc_attr($number); ?>" size="4">
        </p>
        <p>
            <input class="checkbox" type="checkbox" id="<?php echo esc_attr($this->get_field_id('show_map')); ?>" name="<?php echo esc_attr($this->get_field_name('show_map')); ?>" <?php checked($show_map); ?>>
            <label for="<?php echo esc_attr($this->get_field_id('show_map')); ?>"><?php esc_html_e('Show interactive map', 'autoparts-pro'); ?></label>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('map_height')); ?>"><?php esc_html_e('Map height (px):', 'autoparts-pro'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('map_height')); ?>" name="<?php echo esc_attr($this->get_field_name('map_height')); ?>" type="number" step="10" min="100" max="600" value="<?php echo esc_attr($map_height); ?>" size="4">
        </p>
        <p>
            <input class="checkbox" type="checkbox" id="<?php echo esc_attr($this->get_field_id('show_directions')); ?>" name="<?php echo esc_attr($this->get_field_name('show_directions')); ?>" <?php checked($show_directions); ?>>
            <label for="<?php echo esc_attr($this->get_field_id('show_directions')); ?>"><?php esc_html_e('Show directions button', 'autoparts-pro'); ?></label>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['number'] = absint($new_instance['number']);
        $instance['show_map'] = isset($new_instance['show_map']) ? true : false;
        $instance['map_height'] = absint($new_instance['map_height']);
        $instance['show_directions'] = isset($new_instance['show_directions']) ? true : false;
        return $instance;
    }
}
