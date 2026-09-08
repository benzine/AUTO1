<?php
/**
 * Template Tags for AutoParts Pro
 * Reusable functions for templates
 *
 * @package AutoParts_Pro
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Display site logo
 */
function autoparts_pro_the_logo($type = 'default') {
    $logo_id = autoparts_pro_get_option('logo_' . $type, '');
    $logo_text = autoparts_pro_get_option('site_title', get_bloginfo('name'));
    $logo_tagline = autoparts_pro_get_option('site_tagline', get_bloginfo('description'));
    
    if ($logo_id && wp_attachment_is_image($logo_id)) {
        echo wp_get_attachment_image($logo_id, 'full', false, array(
            'class' => 'logo-image',
            'alt' => $logo_text
        ));
    } else {
        ?>
        <div class="logo-text">
            <?php echo esc_html($logo_text); ?>
            <span>Pro</span>
        </div>
        <?php
    }
}

/**
 * Display vehicle selector
 */
function autoparts_pro_vehicle_selector() {
    if (!autoparts_pro_get_option('vehicle_selector_enabled', true)) {
        return;
    }
    
    $years = range(date('Y'), 1990);
    ?>
    <div class="vehicle-selector" id="vehicle-selector">
        <h4 class="vehicle-selector-title"><?php esc_html_e('Find Parts For Your Vehicle', 'autoparts-pro'); ?></h4>
        <form class="vehicle-form" id="vehicle-form">
            <select id="vehicle-year" class="form-select" aria-label="<?php esc_attr_e('Select Year', 'autoparts-pro'); ?>">
                <option value=""><?php esc_html_e('Select Year', 'autoparts-pro'); ?></option>
                <?php foreach ($years as $year) : ?>
                    <option value="<?php echo esc_attr($year); ?>"><?php echo esc_html($year); ?></option>
                <?php endforeach; ?>
            </select>
            <select id="vehicle-make" class="form-select" disabled aria-label="<?php esc_attr_e('Select Make', 'autoparts-pro'); ?>">
                <option value=""><?php esc_html_e('Select Make', 'autoparts-pro'); ?></option>
            </select>
            <select id="vehicle-model" class="form-select" disabled aria-label="<?php esc_attr_e('Select Model', 'autoparts-pro'); ?>">
                <option value=""><?php esc_html_e('Select Model', 'autoparts-pro'); ?></option>
            </select>
            <select id="vehicle-engine" class="form-select" disabled aria-label="<?php esc_attr_e('Select Engine', 'autoparts-pro'); ?>">
                <option value=""><?php esc_html_e('Select Engine', 'autoparts-pro'); ?></option>
            </select>
        </form>
        <button type="button" class="btn btn-primary save-vehicle-btn" style="margin-top: 1rem; width: 100%;">
            <?php esc_html_e('Save to Garage', 'autoparts-pro'); ?>
        </button>
    </div>
    <?php
}

/**
 * Display breadcrumbs
 */
function autoparts_pro_breadcrumbs() {
    if (is_front_page()) {
        return;
    }
    
    echo '<nav class="breadcrumbs" aria-label="' . esc_attr__('Breadcrumb', 'autoparts-pro') . '">';
    echo '<div class="container">';
    echo '<ul itemscope itemtype="https://schema.org/BreadcrumbList">';
    
    // Home
    echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
    echo '<a itemprop="item" href="' . esc_url(home_url('/')) . '">';
    echo '<span itemprop="name">' . esc_html__('Home', 'autoparts-pro') . '</span>';
    echo '</a>';
    echo '<meta itemprop="position" content="1" />';
    echo '</li>';
    
    // Blog or Shop
    if (is_shop() || is_product_category() || is_product_tag()) {
        echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        echo '<a itemprop="item" href="' . esc_url(get_permalink(wc_get_page_id('shop'))) . '">';
        echo '<span itemprop="name">' . esc_html__('Shop', 'autoparts-pro') . '</span>';
        echo '</a>';
        echo '<meta itemprop="position" content="2" />';
        echo '</li>';
        
        if (is_product_category() || is_product_tag()) {
            echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            echo '<span itemprop="name">' . esc_html(get_queried_object()->name) . '</span>';
            echo '<meta itemprop="position" content="3" />';
            echo '</li>';
        }
    } elseif (is_home()) {
        echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        echo '<span itemprop="name">' . esc_html__('Blog', 'autoparts-pro') . '</span>';
        echo '<meta itemprop="position" content="2" />';
        echo '</li>';
    } elseif (is_archive() || is_search()) {
        echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        echo '<span itemprop="name">' . esc_html(get_the_archive_title()) . '</span>';
        echo '<meta itemprop="position" content="2" />';
        echo '</li>';
    } elseif (is_singular()) {
        $post_type = get_post_type();
        if ($post_type !== 'page') {
            $post_type_object = get_post_type_object($post_type);
            echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            echo '<a itemprop="item" href="' . esc_url(get_post_type_archive_link($post_type)) . '">';
            echo '<span itemprop="name">' . esc_html($post_type_object->labels->name) . '</span>';
            echo '</a>';
            echo '<meta itemprop="position" content="2" />';
            echo '</li>';
        }
        echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        echo '<span itemprop="name">' . esc_html(get_the_title()) . '</span>';
        echo '<meta itemprop="position" content="' . ($post_type === 'page' ? '2' : '3') . '" />';
        echo '</li>';
    }
    
    echo '</ul>';
    echo '</div>';
    echo '</nav>';
}

/**
 * Display wishlist button
 */
function autoparts_pro_wishlist_button($product_id = null) {
    if (!autoparts_pro_get_option('wishlist_enabled', true)) {
        return;
    }
    
    if (!$product_id) {
        $product_id = get_the_ID();
    }
    
    ?>
    <button class="action-btn wishlist add-to-wishlist" 
            data-product-id="<?php echo esc_attr($product_id); ?>" 
            aria-label="<?php esc_attr_e('Add to Wishlist', 'autoparts-pro'); ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
    </button>
    <?php
}

/**
 * Display compatibility badge
 */
function autoparts_pro_compatibility_badge($product_id = null) {
    if (!$product_id) {
        $product_id = get_the_ID();
    }
    
    ?>
    <div class="compatibility-badge" data-product-id="<?php echo esc_attr($product_id); ?>">
        <?php esc_html_e('Check Compatibility', 'autoparts-pro'); ?>
    </div>
    <?php
}

/**
 * Display quick view button
 */
function autoparts_pro_quick_view_button($product_id = null) {
    if (!$product_id) {
        $product_id = get_the_ID();
    }
    
    ?>
    <button class="action-btn quick-view-btn" 
            data-product-id="<?php echo esc_attr($product_id); ?>" 
            aria-label="<?php esc_attr_e('Quick View', 'autoparts-pro'); ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>
    </button>
    <?php
}

/**
 * Display flash deals banner
 */
function autoparts_pro_flash_deals_banner() {
    $end_date = autoparts_pro_get_option('flash_deals_end_date', '');
    $deal_text = autoparts_pro_get_option('flash_deals_text', __('Flash Sale - Up to 50% OFF!', 'autoparts-pro'));
    
    if (!$end_date) {
        return;
    }
    
    ?>
    <div class="flash-deals-banner">
        <div class="container">
            <div class="flash-deals-content">
                <span class="flash-label">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                    </svg>
                    <?php esc_html_e('Flash Deal', 'autoparts-pro'); ?>
                </span>
                <span><?php echo esc_html($deal_text); ?></span>
                <div class="countdown-timer" data-end-date="<?php echo esc_attr($end_date); ?>"></div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Display social share buttons
 */
function autoparts_pro_social_share() {
    ?>
    <div class="social-share">
        <span class="share-label"><?php esc_html_e('Share:', 'autoparts-pro'); ?></span>
        <button class="share-btn" data-platform="facebook" aria-label="<?php esc_attr_e('Share on Facebook', 'autoparts-pro'); ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
        </button>
        <button class="share-btn" data-platform="twitter" aria-label="<?php esc_attr_e('Share on Twitter', 'autoparts-pro'); ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
        </button>
        <button class="share-btn" data-platform="pinterest" aria-label="<?php esc_attr_e('Share on Pinterest', 'autoparts-pro'); ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M8 12a4 4 0 1 1 8 0c0 4-4 8-4 8s-4-4-4-8zm8-8a8 8 0 1 1-16 0 8 8 0 0 1 16 0z"/></svg>
        </button>
        <button class="share-btn" data-platform="email" aria-label="<?php esc_attr_e('Share via Email', 'autoparts-pro'); ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        </button>
        <button class="share-btn" data-platform="copy" aria-label="<?php esc_attr_e('Copy Link', 'autoparts-pro'); ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
        </button>
    </div>
    <?php
}

/**
 * Display store locator
 */
function autoparts_pro_store_locator() {
    $api_key = autoparts_pro_get_option('google_maps_api_key', '');
    $stores = get_posts(array(
        'post_type' => 'store_location',
        'posts_per_page' => -1,
        'fields' => 'ids'
    ));
    
    if (empty($stores) || !$api_key) {
        return;
    }
    
    ?>
    <div class="store-locator" id="store-locator">
        <div id="map" style="height: 400px; width: 100%;"></div>
        <script>
            function initMap() {
                var map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 10,
                    center: {lat: 40.7128, lng: -74.0060},
                    styles: [
                        {elementType: 'geometry', stylers: [{color: '#242f3e'}]},
                        {elementType: 'labels.text.stroke', stylers: [{color: '#242f3e'}]},
                        {elementType: 'labels.text.fill', stylers: [{color: '#746855'}]}
                    ]
                });
                
                // Add markers
                <?php foreach ($stores as $store_id) : 
                    $lat = get_post_meta($store_id, '_store_latitude', true);
                    $lng = get_post_meta($store_id, '_store_longitude', true);
                    $name = get_the_title($store_id);
                    if ($lat && $lng) :
                ?>
                new google.maps.Marker({
                    position: {lat: <?php echo floatval($lat); ?>, lng: <?php echo floatval($lng); ?>},
                    map: map,
                    title: '<?php echo esc_js($name); ?>'
                });
                <?php endif; endforeach; ?>
            }
        </script>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo esc_attr($api_key); ?>&callback=initMap"></script>
    </div>
    <?php
}

/**
 * Get mega menu content
 */
function autoparts_pro_mega_menu($location = 'primary') {
    $menu_items = wp_get_nav_menu_items(get_nav_menu_locations()[$location] ?? 0);
    
    if (!$menu_items) {
        return;
    }
    
    echo '<ul class="nav-menu">';
    
    foreach ($menu_items as $item) {
        if (!$item->menu_item_parent) {
            $children = array_filter($menu_items, function($child) use ($item) {
                return $child->menu_item_parent == $item->ID;
            });
            
            echo '<li class="nav-item ' . (empty($children) ? '' : 'menu-item-has-children') . '">';
            echo '<a class="nav-link" href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
            
            if (!empty($children)) {
                echo '<div class="mega-menu">';
                foreach ($children as $child) {
                    echo '<div class="mega-menu-column">';
                    echo '<h4 class="mega-menu-title">' . esc_html($child->title) . '</h4>';
                    
                    // Get category image if exists
                    $term_id = url_to_postid($child->url);
                    if ($term_id) {
                        $thumb = get_term_meta($term_id, 'category_image', true);
                        if ($thumb) {
                            echo wp_get_attachment_image($thumb, 'autoparts-mega-thumb', false, array('class' => 'mega-menu-thumb'));
                        }
                    }
                    
                    echo '</div>';
                }
                echo '</div>';
            }
            
            echo '</li>';
        }
    }
    
    echo '</ul>';
}
