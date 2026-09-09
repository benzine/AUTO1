<?php
/**
 * Template part for displaying product cards in loops
 *
 * @package AutoParts_Pro
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

global $product;

if (!$product instanceof WC_Product) {
    return;
}

// Get compatibility data
$compatibility = autoparts_get_product_compatibility($product->get_id());
$is_on_sale = $product->is_on_sale();
$sale_percent = 0;
if ($is_on_sale) {
    $regular_price = $product->get_regular_price();
    $sale_price = $product->get_sale_price();
    if ($regular_price && $sale_price) {
        $sale_percent = round((($regular_price - $sale_price) / $regular_price) * 100);
    }
}

// Stock status
$stock_status = $product->get_stock_status();
$stock_quantity = $product->get_stock_quantity();
$is_low_stock = $stock_quantity && $stock_quantity <= 5;

?>
<article <?php post_class('product-card'); ?> data-product-id="<?php echo esc_attr($product->get_id()); ?>">
    
    <?php do_action('autoparts_product_card_before', $product); ?>
    
    <div class="product-card-image">
        <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>" class="product-link">
            <?php echo $product->get_image('woocommerce_thumbnail', array('loading' => 'lazy')); ?>
        </a>
        
        <?php if ($is_on_sale) : ?>
            <span class="product-badge sale-badge">
                <span class="badge-text">-<?php echo esc_html($sale_percent); ?>%</span>
                <span class="badge-label"><?php esc_html_e('SALE', 'autoparts-pro'); ?></span>
            </span>
        <?php endif; ?>
        
        <?php if ('outofstock' === $stock_status) : ?>
            <span class="product-badge out-of-stock-badge">
                <?php esc_html_e('OUT OF STOCK', 'autoparts-pro'); ?>
            </span>
        <?php elseif ($is_low_stock) : ?>
            <span class="product-badge low-stock-badge">
                <?php 
                printf(
                    wp_kses_post(__('Only %d left!', 'autoparts-pro')),
                    esc_html($stock_quantity)
                );
                ?>
            </span>
        <?php endif; ?>
        
        <?php if ($compatibility && $compatibility['fits']) : ?>
            <span class="compatibility-badge fits-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <?php esc_html_e('Fits Your Vehicle', 'autoparts-pro'); ?>
            </span>
        <?php endif; ?>
        
        <div class="product-card-actions">
            <button class="action-button wishlist-button" 
                    data-product-id="<?php echo esc_attr($product->get_id()); ?>" 
                    aria-label="<?php esc_attr_e('Add to wishlist', 'autoparts-pro'); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
            </button>
            
            <button class="action-button quick-view-button" 
                    data-product-id="<?php echo esc_attr($product->get_id()); ?>"
                    aria-label="<?php esc_attr_e('Quick view', 'autoparts-pro'); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
            </button>
            
            <?php if (function_exists('YITH_WCWL') && YITH_WCWL()) : ?>
                <button class="action-button compare-button"
                        data-product-id="<?php echo esc_attr($product->get_id()); ?>"
                        aria-label="<?php esc_attr_e('Compare', 'autoparts-pro'); ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </button>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="product-card-content">
        <?php do_action('autoparts_product_card_content_before', $product); ?>
        
        <div class="product-category">
            <?php 
            $categories = $product->get_category_ids();
            if ($categories) {
                $category = get_term($categories[0], 'product_cat');
                if ($category && !is_wp_error($category)) {
                    echo '<a href="' . esc_url(get_term_link($category)) . '">' . esc_html($category->name) . '</a>';
                }
            }
            ?>
        </div>
        
        <h3 class="product-title">
            <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>">
                <?php echo esc_html($product->get_name()); ?>
            </a>
        </h3>
        
        <?php if ($product->get_sku()) : ?>
            <div class="product-sku">
                <?php 
                printf(
                    wp_kses_post(__('Part #: %s', 'autoparts-pro')),
                    esc_html($product->get_sku())
                );
                ?>
            </div>
        <?php endif; ?>
        
        <?php if ($product->get_rating_count() > 0) : ?>
            <div class="product-rating">
                <?php echo wc_get_rating_html($product->get_average_rating(), $product->get_rating_count()); ?>
                <span class="rating-count">(<?php echo esc_html($product->get_rating_count()); ?>)</span>
            </div>
        <?php endif; ?>
        
        <div class="product-price">
            <?php echo $product->get_price_html(); ?>
        </div>
        
        <?php if ($product->is_purchasable() && $product->is_in_stock()) : ?>
            <button class="button add-to-cart-button" 
                    data-product-id="<?php echo esc_attr($product->get_id()); ?>"
                    data-quantity="1">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                <?php esc_html_e('Add to Cart', 'autoparts-pro'); ?>
            </button>
        <?php elseif (!$product->is_in_stock()) : ?>
            <button class="button notify-me-button" disabled>
                <?php esc_html_e('Notify When Available', 'autoparts-pro'); ?>
            </button>
        <?php endif; ?>
        
        <?php do_action('autoparts_product_card_content_after', $product); ?>
    </div>
    
    <?php do_action('autoparts_product_card_after', $product); ?>
</article>
