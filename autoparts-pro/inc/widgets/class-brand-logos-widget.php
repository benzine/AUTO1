<?php
/**
 * AutoParts Pro - Brand Logos Widget
 * Displays manufacturer/brand logos grid
 *
 * @package AutoParts_Pro
 * @since 1.0.0
 */

namespace AutoPartsPro\Widgets;

class Brand_Logos_Widget extends \WP_Widget {

    public function __construct() {
        parent::__construct(
            'autoparts_brand_logos',
            __('AutoParts: Brand Logos', 'autoparts-pro'),
            array(
                'description' => __('Display brand/manufacturer logos', 'autoparts-pro'),
                'classname' => 'widget-autoparts-brand-logos'
            )
        );
    }

    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Our Brands', 'autoparts-pro');
        $number = !empty($instance['number']) ? absint($instance['number']) : 12;
        $show_all_link = !empty($instance['show_all_link']) ? true : false;
        $grayscale = !empty($instance['grayscale']) ? true : false;
        $columns = !empty($instance['columns']) ? absint($instance['columns']) : 4;

        // Get brands from taxonomy
        $brands = get_terms(array(
            'taxonomy' => 'product_brand',
            'hide_empty' => true,
            'number' => $number,
            'orderby' => 'name',
            'order' => 'ASC'
        ));

        if (is_wp_error($brands) || empty($brands)) {
            return;
        }

        echo $args['before_widget'];
        
        if ($title) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }

        ?>
        <div class="brand-logos-widget" data-columns="<?php echo esc_attr($columns); ?>">
            <div class="brand-logos-grid <?php echo $grayscale ? 'grayscale' : ''; ?>">
                <?php foreach ($brands as $brand) : 
                    $logo_id = get_term_meta($brand->term_id, '_brand_logo_id', true);
                    $logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'medium') : '';
                    $brand_link = get_term_link($brand);
                ?>
                    <a href="<?php echo esc_url($brand_link); ?>" class="brand-logo-item" title="<?php echo esc_attr($brand->name); ?>">
                        <?php if ($logo_url) : ?>
                            <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($brand->name); ?>" loading="lazy">
                        <?php else : ?>
                            <span class="brand-name"><?php echo esc_html($brand->name); ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if ($show_all_link) : ?>
                <div class="brand-logos-footer">
                    <a href="<?php echo esc_url(get_term_link('product_brand')); ?>" class="button button-small">
                        <?php esc_html_e('View All Brands', 'autoparts-pro'); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <?php

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Our Brands', 'autoparts-pro');
        $number = !empty($instance['number']) ? absint($instance['number']) : 12;
        $show_all_link = !empty($instance['show_all_link']) ? true : false;
        $grayscale = !empty($instance['grayscale']) ? true : false;
        $columns = !empty($instance['columns']) ? absint($instance['columns']) : 4;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'autoparts-pro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php esc_html_e('Number of brands to show:', 'autoparts-pro'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" step="1" min="1" max="50" value="<?php echo esc_attr($number); ?>" size="4">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('columns')); ?>"><?php esc_html_e('Columns:', 'autoparts-pro'); ?></label>
            <select id="<?php echo esc_attr($this->get_field_id('columns')); ?>" name="<?php echo esc_attr($this->get_field_name('columns')); ?>">
                <?php for ($i = 2; $i <= 6; $i++) : ?>
                    <option value="<?php echo esc_attr($i); ?>" <?php selected($columns, $i); ?>><?php echo esc_html($i); ?></option>
                <?php endfor; ?>
            </select>
        </p>
        <p>
            <input class="checkbox" type="checkbox" id="<?php echo esc_attr($this->get_field_id('grayscale')); ?>" name="<?php echo esc_attr($this->get_field_name('grayscale')); ?>" <?php checked($grayscale); ?>>
            <label for="<?php echo esc_attr($this->get_field_id('grayscale')); ?>"><?php esc_html_e('Grayscale until hover', 'autoparts-pro'); ?></label>
        </p>
        <p>
            <input class="checkbox" type="checkbox" id="<?php echo esc_attr($this->get_field_id('show_all_link')); ?>" name="<?php echo esc_attr($this->get_field_name('show_all_link')); ?>" <?php checked($show_all_link); ?>>
            <label for="<?php echo esc_attr($this->get_field_id('show_all_link')); ?>"><?php esc_html_e('Show "View All" link', 'autoparts-pro'); ?></label>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['number'] = absint($new_instance['number']);
        $instance['show_all_link'] = isset($new_instance['show_all_link']) ? true : false;
        $instance['grayscale'] = isset($new_instance['grayscale']) ? true : false;
        $instance['columns'] = absint($new_instance['columns']);
        return $instance;
    }
}
