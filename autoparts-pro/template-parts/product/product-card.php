<?php
/**
 * Template part for displaying product cards in grids
 *
 * @package AutoParts_Pro
 */

if (!isset($product) || !$product) {
    return;
}

$product_id = $product->get_id();
$sale_price = $product->get_sale_price();
$regular_price = $product->get_regular_price();
$is_in_stock = $product->is_in_stock();
$stock_quantity = $product->get_stock_quantity();
$average_rating = $product->get_average_rating();
$rating_count = $product->get_rating_count();
$discount_percentage = 0;

if ($sale_price && $regular_price) {
    $discount_percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
}

// Get compatibility badge
$compatibility_badge = '';
$user_vehicle = get_user_meta(get_current_user_id(), '_selected_vehicle', true);
if ($user_vehicle) {
    $fits = autoparts_check_compatibility($product_id, $user_vehicle);
    if ($fits) {
        $compatibility_badge = '<span class="compatibility-badge fits"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>' . __('Fits Your Vehicle', 'autoparts-pro') . '</span>';
    }
}
?>

<div class="product-card" data-product-id="<?php echo esc_attr($product_id); ?>">
    <!-- Badges -->
    <div class="product-badges">
        <?php if ($discount_percentage > 0) : ?>
        <span class="badge discount-badge">-<?php echo esc_html($discount_percentage); ?>%</span>
        <?php endif; ?>
        
        <?php if ($product->is_featured()) : ?>
        <span class="badge featured-badge"><?php esc_html_e('Featured', 'autoparts-pro'); ?></span>
        <?php endif; ?>
        
        <?php if ($is_in_stock && $stock_quantity && $stock_quantity < 10) : ?>
        <span class="badge low-stock-badge"><?php esc_html_e('Low Stock', 'autoparts-pro'); ?></span>
        <?php endif; ?>
        
        <?php echo $compatibility_badge; ?>
    </div>

    <!-- Image Container -->
    <div class="product-card-image">
        <a href="<?php echo esc_url($product->get_permalink()); ?>">
            <?php 
            $main_image = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'medium');
            if ($main_image) :
            ?>
            <img src="<?php echo esc_url($main_image[0]); ?>" 
                 alt="<?php echo esc_attr(get_the_title()); ?>"
                 loading="lazy"
                 width="<?php echo esc_attr($main_image[1]); ?>"
                 height="<?php echo esc_attr($main_image[2]); ?>">
            <?php else : ?>
            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/placeholder.png'); ?>" 
                 alt="<?php echo esc_attr(get_the_title()); ?>"
                 loading="lazy"
                 class="placeholder-image">
            <?php endif; ?>
        </a>

        <!-- Quick Actions -->
        <div class="product-card-actions">
            <button type="button" 
                    class="action-btn wishlist-btn" 
                    data-product-id="<?php echo esc_attr($product_id); ?>"
                    aria-label="<?php esc_attr_e('Add to Wishlist', 'autoparts-pro'); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
            </button>
            
            <button type="button" 
                    class="action-btn quick-view-btn" 
                    data-product-id="<?php echo esc_attr($product_id); ?>"
                    aria-label="<?php esc_attr_e('Quick View', 'autoparts-pro'); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
            </button>
            
            <?php if ($is_in_stock) : ?>
            <button type="button" 
                    class="action-btn add-to-cart-btn" 
                    data-product-id="<?php echo esc_attr($product_id); ?>"
                    aria-label="<?php esc_attr_e('Add to Cart', 'autoparts-pro'); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="21" r="1"/>
                    <circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Content -->
    <div class="product-card-content">
        <!-- Category -->
        <?php 
        $categories = $product->get_category_ids();
        if (!empty($categories)) :
            $first_category = get_term($categories[0], 'product_cat');
            if ($first_category && !is_wp_error($first_category)) :
        ?>
        <span class="product-category">
            <a href="<?php echo esc_url(get_term_link($first_category)); ?>">
                <?php echo esc_html($first_category->name); ?>
            </a>
        </span>
        <?php 
            endif;
        endif;
        ?>

        <!-- Title -->
        <h3 class="product-card-title">
            <a href="<?php echo esc_url($product->get_permalink()); ?>">
                <?php echo esc_html(get_the_title()); ?>
            </a>
        </h3>

        <!-- Rating -->
        <?php if ($rating_count > 0) : ?>
        <div class="product-card-rating">
            <div class="star-rating-mini">
                <?php for ($i = 1; $i <= 5; $i++) : ?>
                <svg class="star <?php echo $i <= $average_rating ? 'filled' : ''; ?>" viewBox="0 0 24 24" fill="currentColor">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
                <?php endfor; ?>
            </div>
            <span class="rating-count">(<?php echo esc_html($rating_count); ?>)</span>
        </div>
        <?php endif; ?>

        <!-- Price -->
        <div class="product-card-price">
            <?php if ($sale_price) : ?>
            <span class="regular-price"><del><?php echo wc_price($regular_price); ?></del></span>
            <span class="sale-price"><?php echo wc_price($sale_price); ?></span>
            <?php else : ?>
            <span class="current-price"><?php echo wc_price($regular_price); ?></span>
            <?php endif; ?>
        </div>

        <!-- Stock Status -->
        <div class="product-card-stock stock-status-<?php echo $is_in_stock ? 'in-stock' : 'out-of-stock'; ?>">
            <?php if ($is_in_stock) : ?>
                <?php if ($stock_quantity && $stock_quantity < 10) : ?>
                <span class="stock-text low-stock">
                    <?php printf(__('Only %d left!', 'autoparts-pro'), $stock_quantity); ?>
                </span>
                <?php else : ?>
                <span class="stock-text in-stock"><?php esc_html_e('In Stock', 'autoparts-pro'); ?></span>
                <?php endif; ?>
            <?php else : ?>
            <span class="stock-text out-of-stock"><?php esc_html_e('Out of Stock', 'autoparts-pro'); ?></span>
            <?php endif; ?>
        </div>

        <!-- Part Number (if available) -->
        <?php 
        $sku = $product->get_sku();
        if ($sku) :
        ?>
        <span class="product-sku-mini"><?php printf(__('SKU: %s', 'autoparts-pro'), esc_html($sku)); ?></span>
        <?php endif; ?>
    </div>
</div>
