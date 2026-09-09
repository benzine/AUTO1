<?php
/**
 * AutoParts Pro - Vehicle Filter Widget (My Garage)
 * Allows users to select their vehicle for compatibility filtering
 *
 * @package AutoParts_Pro
 * @since 1.0.0
 */

namespace AutoPartsPro\Widgets;

class Vehicle_Filter_Widget extends \WP_Widget {

    public function __construct() {
        parent::__construct(
            'autoparts_vehicle_filter',
            __('AutoParts: Vehicle Filter', 'autoparts-pro'),
            array(
                'description' => __('Year/Make/Model/Engine selector for parts compatibility', 'autoparts-pro'),
                'classname' => 'widget-autoparts-vehicle-filter'
            )
        );
    }

    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('My Garage', 'autoparts-pro');
        $show_saved = !empty($instance['show_saved']) ? true : false;
        $auto_redirect = !empty($instance['auto_redirect']) ? true : false;

        // Get saved vehicles if user is logged in
        $saved_vehicles = array();
        if (is_user_logged_in() && $show_saved) {
            $saved_vehicles = get_user_meta(get_current_user_id(), '_autoparts_garage', true);
            if (!is_array($saved_vehicles)) {
                $saved_vehicles = array();
            }
        }

        echo $args['before_widget'];
        
        if ($title) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }

        ?>
        <div class="vehicle-filter-widget" data-auto-redirect="<?php echo esc_attr($auto_redirect ? 'true' : 'false'); ?>">
            <form class="vehicle-selector-form" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <input type="hidden" name="s" value="">
                <input type="hidden" name="post_type" value="product">
                
                <div class="vehicle-field">
                    <label for="vehicle-year"><?php esc_html_e('Year', 'autoparts-pro'); ?></label>
                    <select id="vehicle-year" name="vehicle_year" class="vehicle-select" required>
                        <option value=""><?php esc_html_e('Select Year', 'autoparts-pro'); ?></option>
                        <?php
                        $current_year = date('Y');
                        for ($year = $current_year + 1; $year >= 1950; $year--) {
                            echo '<option value="' . esc_attr($year) . '">' . esc_html($year) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="vehicle-field">
                    <label for="vehicle-make"><?php esc_html_e('Make', 'autoparts-pro'); ?></label>
                    <select id="vehicle-make" name="vehicle_make" class="vehicle-select" required disabled>
                        <option value=""><?php esc_html_e('Select Year First', 'autoparts-pro'); ?></option>
                    </select>
                </div>

                <div class="vehicle-field">
                    <label for="vehicle-model"><?php esc_html_e('Model', 'autoparts-pro'); ?></label>
                    <select id="vehicle-model" name="vehicle_model" class="vehicle-select" required disabled>
                        <option value=""><?php esc_html_e('Select Make First', 'autoparts-pro'); ?></option>
                    </select>
                </div>

                <div class="vehicle-field">
                    <label for="vehicle-engine"><?php esc_html_e('Engine', 'autoparts-pro'); ?></label>
                    <select id="vehicle-engine" name="vehicle_engine" class="vehicle-select" required disabled>
                        <option value=""><?php esc_html_e('Select Model First', 'autoparts-pro'); ?></option>
                    </select>
                </div>

                <button type="submit" class="button vehicle-filter-submit">
                    <?php esc_html_e('Filter Parts', 'autoparts-pro'); ?>
                </button>
            </form>

            <?php if (!empty($saved_vehicles)) : ?>
                <div class="saved-vehicles">
                    <h4><?php esc_html_e('Saved Vehicles', 'autoparts-pro'); ?></h4>
                    <ul class="garage-list">
                        <?php foreach ($saved_vehicles as $index => $vehicle) : ?>
                            <li class="garage-item" data-vehicle-index="<?php echo esc_attr($index); ?>">
                                <span class="vehicle-name">
                                    <?php 
                                    printf(
                                        '%d %s %s %s',
                                        esc_html($vehicle['year']),
                                        esc_html($vehicle['make']),
                                        esc_html($vehicle['model']),
                                        esc_html($vehicle['engine'])
                                    );
                                    ?>
                                </span>
                                <button class="load-vehicle" data-vehicle-index="<?php echo esc_attr($index); ?>">
                                    <?php esc_html_e('Load', 'autoparts-pro'); ?>
                                </button>
                                <button class="remove-vehicle" data-vehicle-index="<?php echo esc_attr($index); ?>" aria-label="<?php esc_attr_e('Remove vehicle', 'autoparts-pro'); ?>">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="widget-actions">
                <?php if (is_user_logged_in()) : ?>
                    <button class="button button-small save-to-garage">
                        <?php esc_html_e('Save to Garage', 'autoparts-pro'); ?>
                    </button>
                <?php else : ?>
                    <p class="login-prompt">
                        <?php 
                        printf(
                            wp_kses_post(__('Want to save your vehicle? <a href="%s">Login</a>', 'autoparts-pro')),
                            esc_url(wp_login_url(get_permalink()))
                        );
                        ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <?php

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('My Garage', 'autoparts-pro');
        $show_saved = !empty($instance['show_saved']) ? true : false;
        $auto_redirect = !empty($instance['auto_redirect']) ? false : false;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'autoparts-pro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <input class="checkbox" type="checkbox" id="<?php echo esc_attr($this->get_field_id('show_saved')); ?>" name="<?php echo esc_attr($this->get_field_name('show_saved')); ?>" <?php checked($show_saved); ?>>
            <label for="<?php echo esc_attr($this->get_field_id('show_saved')); ?>"><?php esc_html_e('Show saved vehicles (My Garage)', 'autoparts-pro'); ?></label>
        </p>
        <p>
            <input class="checkbox" type="checkbox" id="<?php echo esc_attr($this->get_field_id('auto_redirect')); ?>" name="<?php echo esc_attr($this->get_field_name('auto_redirect')); ?>" <?php checked($auto_redirect); ?>>
            <label for="<?php echo esc_attr($this->get_field_id('auto_redirect')); ?>"><?php esc_html_e('Auto-redirect on selection', 'autoparts-pro'); ?></label>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['show_saved'] = isset($new_instance['show_saved']) ? true : false;
        $instance['auto_redirect'] = isset($new_instance['auto_redirect']) ? true : false;
        return $instance;
    }
}
