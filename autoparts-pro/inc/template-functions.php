<?php
/**
 * Template functions for the theme
 *
 * @package AutoParts_Pro
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Display site logo
 */
function autoparts_pro_logo($args = array()) {
    $defaults = array(
        'logo'         => '',
        'logo_retina'  => '',
        'logo_width'   => '',
        'logo_height'  => '',
        'site_title'   => get_bloginfo('name'),
        'site_desc'    => get_bloginfo('description'),
        'class'        => '',
    );
    
    $args = wp_parse_args($args, $defaults);
    
    $has_logo = has_custom_logo();
    $logo_class = $args['class'] ? ' ' . esc_attr($args['class']) : '';
    
    echo '<div class="site-logo' . esc_attr($logo_class) . '">';
    
    if ($has_logo) {
        the_custom_logo();
    } else {
        echo '<a href="' . esc_url(home_url('/')) . '" rel="home" class="logo-text">';
        echo '<span class="logo-text__title">' . esc_html($args['site_title']) . '</span>';
        if ($args['site_desc']) {
            echo '<span class="logo-text__desc">' . esc_html($args['site_desc']) . '</span>';
        }
        echo '</a>';
    }
    
    echo '</div>';
}

/**
 * Display breadcrumb navigation
 */
function autoparts_pro_breadcrumb() {
    if (is_front_page()) {
        return;
    }
    
    echo '<nav class="breadcrumb" aria-label="' . esc_attr__('Breadcrumb', 'autoparts-pro') . '">';
    echo '<ul class="breadcrumb__list">';
    echo '<li class="breadcrumb__item"><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'autoparts-pro') . '</a></li>';
    
    if (is_category() || is_single()) {
        echo '<li class="breadcrumb__item">' . esc_html__('Shop', 'autoparts-pro') . '</li>';
        if (is_single()) {
            $categories = get_the_category();
            if ($categories) {
                echo '<li class="breadcrumb__item"><a href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . esc_html($categories[0]->name) . '</a></li>';
            }
        }
    } elseif (is_page()) {
        global $post;
        $ancestors = get_post_ancestors($post);
        if ($ancestors) {
            $ancestors = array_reverse($ancestors);
            foreach ($ancestors as $ancestor) {
                echo '<li class="breadcrumb__item"><a href="' . esc_url(get_permalink($ancestor)) . '">' . esc_html(get_the_title($ancestor)) . '</a></li>';
            }
        }
    } elseif (is_archive()) {
        echo '<li class="breadcrumb__item">' . post_type_archive_title('', false) . '</li>';
    } elseif (is_search()) {
        echo '<li class="breadcrumb__item">' . esc_html__('Search Results', 'autoparts-pro') . '</li>';
    }
    
    if (is_singular()) {
        echo '<li class="breadcrumb__item is-active">' . get_the_title() . '</li>';
    } elseif (is_page()) {
        echo '<li class="breadcrumb__item is-active">' . get_the_title() . '</li>';
    } elseif (is_category()) {
        echo '<li class="breadcrumb__item is-active">' . single_cat_title('', false) . '</li>';
    } elseif (is_search()) {
        echo '<li class="breadcrumb__item is-active">' . sprintf(esc_html__('Search: %s', 'autoparts-pro'), get_search_query()) . '</li>';
    }
    
    echo '</ul>';
    echo '</nav>';
}

/**
 * Get vehicle compatibility data for a product
 */
function autoparts_pro_get_product_compatibility($product_id = null) {
    if (!$product_id) {
        $product_id = get_the_ID();
    }
    
    $compatibility = array(
        'makes'   => get_the_terms($product_id, 'vehicle_make'),
        'models'  => get_the_terms($product_id, 'vehicle_model'),
        'years'   => get_the_terms($product_id, 'vehicle_year'),
    );
    
    return $compatibility;
}

/**
 * Check if product fits selected vehicle
 */
function autoparts_pro_check_fitment($product_id, $year, $make, $model) {
    $product_makes = get_the_terms($product_id, 'vehicle_make');
    $product_models = get_the_terms($product_id, 'vehicle_model');
    $product_years = get_the_terms($product_id, 'vehicle_year');
    
    $fits = true;
    
    if ($product_makes) {
        $make_ids = wp_list_pluck($product_makes, 'term_id');
        if (!in_array($make, $make_ids)) {
            $fits = false;
        }
    }
    
    if ($product_models && $fits) {
        $model_ids = wp_list_pluck($product_models, 'term_id');
        if (!in_array($model, $model_ids)) {
            $fits = false;
        }
    }
    
    if ($product_years && $fits) {
        $year_ids = wp_list_pluck($product_years, 'term_id');
        if (!in_array($year, $year_ids)) {
            $fits = false;
        }
    }
    
    return $fits;
}

/**
 * Display product part numbers
 */
function autoparts_pro_display_part_numbers($product_id = null) {
    if (!$product_id) {
        $product_id = get_the_ID();
    }
    
    $part_numbers = array(
        'oem'       => get_post_meta($product_id, '_oem_part_number', true),
        'manufacturer' => get_post_meta($product_id, '_manufacturer_part_number', true),
        'cross_ref' => get_post_meta($product_id, '_cross_reference_numbers', true),
    );
    
    if (!array_filter($part_numbers)) {
        return;
    }
    
    echo '<div class="part-numbers">';
    echo '<h4>' . esc_html__('Part Numbers', 'autoparts-pro') . '</h4>';
    echo '<ul class="part-numbers__list">';
    
    if ($part_numbers['oem']) {
        echo '<li><strong>OEM:</strong> ' . esc_html($part_numbers['oem']) . '</li>';
    }
    if ($part_numbers['manufacturer']) {
        echo '<li><strong>Manufacturer:</strong> ' . esc_html($part_numbers['manufacturer']) . '</li>';
    }
    if ($part_numbers['cross_ref']) {
        echo '<li><strong>Cross Reference:</strong> ' . esc_html($part_numbers['cross_ref']) . '</li>';
    }
    
    echo '</ul>';
    echo '</div>';
}

/**
 * Display stock status
 */
function autoparts_pro_stock_status($product_id = null) {
    if (class_exists('WooCommerce')) {
        $product = wc_get_product($product_id ? $product_id : get_the_ID());
        if ($product) {
            $stock_status = $product->get_stock_status();
            $stock_qty = $product->get_stock_quantity();
            
            echo '<div class="stock-status stock-status--' . esc_attr($stock_status) . '">';
            
            if ($stock_status === 'instock') {
                if ($stock_qty && $stock_qty < 10) {
                    echo '<span class="stock-status__text">' . sprintf(esc_html__('Only %d left in stock', 'autoparts-pro'), $stock_qty) . '</span>';
                } else {
                    echo '<span class="stock-status__text">' . esc_html__('In Stock', 'autoparts-pro') . '</span>';
                }
            } elseif ($stock_status === 'onbackorder') {
                echo '<span class="stock-status__text">' . esc_html__('Available on Backorder', 'autoparts-pro') . '</span>';
            } else {
                echo '<span class="stock-status__text">' . esc_html__('Out of Stock', 'autoparts-pro') . '</span>';
            }
            
            echo '</div>';
        }
    }
}

/**
 * Get related products by compatibility
 */
function autoparts_pro_get_compatible_products($product_id = null, $limit = 4) {
    if (!$product_id) {
        $product_id = get_the_ID();
    }
    
    $compatibility = autoparts_pro_get_product_compatibility($product_id);
    
    if (empty($compatibility['makes'])) {
        return wc_get_related_products($product_id, $limit);
    }
    
    $make_ids = wp_list_pluck($compatibility['makes'], 'term_id');
    
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => $limit,
        'post__not_in'   => array($product_id),
        'tax_query'      => array(
            array(
                'taxonomy' => 'vehicle_make',
                'field'    => 'term_id',
                'terms'    => $make_ids,
            ),
        ),
    );
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        return wp_list_pluck($query->posts, 'ID');
    }
    
    return wc_get_related_products($product_id, $limit);
}

/**
 * Display wishlist button
 */
function autoparts_pro_wishlist_button($product_id = null) {
    if (!$product_id) {
        $product_id = get_the_ID();
    }
    
    $is_in_wishlist = autoparts_pro_is_in_wishlist($product_id);
    $button_class = $is_in_wishlist ? 'wishlist-btn--active' : '';
    
    echo '<button class="wishlist-btn ' . esc_attr($button_class) . '" data-product-id="' . esc_attr($product_id) . '" aria-label="' . esc_attr__('Add to wishlist', 'autoparts-pro') . '">';
    echo '<svg class="wishlist-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">';
    echo '<path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>';
    echo '</svg>';
    echo '</button>';
}

/**
 * Check if product is in wishlist
 */
function autoparts_pro_is_in_wishlist($product_id) {
    if (is_user_logged_in()) {
        $wishlist = get_user_meta(get_current_user_id(), '_autoparts_pro_wishlist', true);
        return is_array($wishlist) && in_array($product_id, $wishlist);
    } else {
        if (isset($_COOKIE['autoparts_pro_guest_wishlist'])) {
            $wishlist = json_decode(stripslashes($_COOKIE['autoparts_pro_guest_wishlist']), true);
            return is_array($wishlist) && in_array($product_id, $wishlist);
        }
    }
    return false;
}

/**
 * Get theme mode (light/dark)
 */
function autoparts_pro_get_theme_mode() {
    $default_mode = autoparts_pro_get_option('default_theme_mode', 'dark');
    return get_query_var('theme_mode', $default_mode);
}

/**
 * Display AI chatbot widget
 */
function autoparts_pro_chatbot_widget() {
    $enabled = autoparts_pro_get_option('enable_chatbot', true);
    if (!$enabled) {
        return;
    }
    
    $api_key = autoparts_pro_get_option('chatbot_api_key', '');
    $greeting = autoparts_pro_get_option('chatbot_greeting', __('Hi! How can I help you find the right parts today?', 'autoparts-pro'));
    $avatar_url = autoparts_pro_get_option('chatbot_avatar', '');
    $primary_color = autoparts_pro_get_option('chatbot_primary_color', '#DC2626');
    
    ?>
    <div id="autoparts-chatbot" class="chatbot-widget" data-api-key="<?php echo esc_attr($api_key); ?>" data-greeting="<?php echo esc_attr($greeting); ?>">
        <button class="chatbot-toggle" aria-label="<?php esc_attr_e('Toggle chat', 'autoparts-pro'); ?>">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2"/>
            </svg>
        </button>
        <div class="chatbot-container">
            <div class="chatbot-header">
                <?php if ($avatar_url): ?>
                    <img src="<?php echo esc_url($avatar_url); ?>" alt="" class="chatbot-avatar">
                <?php endif; ?>
                <div class="chatbot-title">
                    <h4><?php esc_html_e('AutoParts Assistant', 'autoparts-pro'); ?></h4>
                    <span class="chatbot-status"><?php esc_html_e('Online', 'autoparts-pro'); ?></span>
                </div>
                <button class="chatbot-close" aria-label="<?php esc_attr_e('Close chat', 'autoparts-pro'); ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </button>
            </div>
            <div class="chatbot-messages">
                <div class="chatbot-message chatbot-message--bot">
                    <p><?php echo esc_html($greeting); ?></p>
                </div>
            </div>
            <div class="chatbot-input">
                <input type="text" placeholder="<?php esc_attr_e('Ask about parts...', 'autoparts-pro'); ?>">
                <button type="submit">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'autoparts_pro_chatbot_widget');

/**
 * Display back to top button
 */
function autoparts_pro_back_to_top() {
    $icon = autoparts_pro_get_option('back_to_top_icon', 'arrow');
    $icon_svg = '';
    
    switch ($icon) {
        case 'gear':
            $icon_svg = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>';
            break;
        case 'rocket':
            $icon_svg = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09zM12 15l-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/></svg>';
            break;
        default:
            $icon_svg = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M18 15l-6-6-6 6"/></svg>';
    }
    
    echo '<button id="back-to-top" class="back-to-top" aria-label="' . esc_attr__('Back to top', 'autoparts-pro') . '">';
    echo $icon_svg;
    echo '</button>';
}
add_action('wp_footer', 'autoparts_pro_back_to_top');
