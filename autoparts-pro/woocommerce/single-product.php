<?php
/**
 * Single Product Template - WooCommerce Override
 *
 * @package AutoParts_Pro
 */

get_header();

global $product;

if (!$product) {
    return;
}

// Get product data
$product_id = $product->get_id();
$sku = $product->get_sku();
$oem_number = get_post_meta($product_id, '_oem_part_number', true);
$manufacturer_part = get_post_meta($product_id, '_manufacturer_part_number', true);
$cross_reference = get_post_meta($product_id, '_cross_reference_numbers', true);
$installation_guide = get_post_meta($product_id, '_installation_guide_url', true);
$compatibility_data = get_post_meta($product_id, '_vehicle_compatibility', true);
$bulk_pricing = get_post_meta($product_id, '_bulk_pricing_tiers', true);
$is_in_stock = $product->is_in_stock();
$stock_quantity = $product->get_stock_quantity();
$sale_price = $product->get_sale_price();
$regular_price = $product->get_regular_price();
$discount_percentage = 0;

if ($sale_price && $regular_price) {
    $discount_percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
}
?>

<article id="product-<?php echo esc_attr($product_id); ?>" <?php post_class('single-product-page'); ?>>
    <div class="product-breadcrumb-container">
        <?php autoparts_breadcrumbs(); ?>
    </div>

    <div class="product-main-grid">
        <!-- Left Column: Gallery -->
        <div class="product-gallery-section">
            <div class="product-gallery-wrapper" id="product-gallery-<?php echo esc_attr($product_id); ?>">
                <!-- Main Image -->
                <div class="product-main-image">
                    <?php 
                    $main_image = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'large');
                    if ($main_image) :
                    ?>
                    <img src="<?php echo esc_url($main_image[0]); ?>" 
                         alt="<?php echo esc_attr(get_the_title()); ?>"
                         id="product-main-img"
                         data-zoom="<?php echo esc_url($main_image[0]); ?>">
                    
                    <?php if (!empty($bulk_pricing)) : ?>
                    <div class="bulk-pricing-badge">
                        <span class="badge-icon">📦</span>
                        <span><?php esc_html_e('Bulk Discounts Available', 'autoparts-pro'); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Thumbnail Grid -->
                <div class="product-thumbnails">
                    <?php
                    $gallery_ids = $product->get_gallery_image_ids();
                    if (!empty($gallery_ids)) :
                        foreach ($gallery_ids as $index => $attachment_id) :
                            $image = wp_get_attachment_image_src($attachment_id, 'medium');
                            $full_image = wp_get_attachment_image_src($attachment_id, 'large');
                    ?>
                    <button type="button" 
                            class="product-thumbnail <?php echo $index === 0 ? 'active' : ''; ?>" 
                            data-src="<?php echo esc_url($full_image[0]); ?>"
                            data-index="<?php echo esc_attr($index); ?>">
                        <img src="<?php echo esc_url($image[0]); ?>" 
                             alt="<?php echo esc_attr(get_the_title() . ' - View ' . ($index + 1)); ?>">
                    </button>
                    <?php 
                        endforeach;
                    endif;
                    
                    // Add 360 view button if available
                    $has_360 = get_post_meta($product_id, '_has_360_view', true);
                    if ($has_360) :
                    ?>
                    <button type="button" class="product-thumbnail product-360-btn" id="open-360-viewer">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.3"/>
                        </svg>
                        <span><?php esc_html_e('360° View', 'autoparts-pro'); ?></span>
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Compatibility Badge -->
            <div class="product-compatibility-check" id="product-compatibility-section">
                <div class="compatibility-header">
                    <h3><?php esc_html_e('Will This Fit My Vehicle?', 'autoparts-pro'); ?></h3>
                    <button type="button" class="btn-text" id="check-fitment-btn">
                        <?php esc_html_e('Check Now', 'autoparts-pro'); ?>
                    </button>
                </div>
                <div class="compatibility-result" id="fitment-result" style="display: none;">
                    <div class="fitment-success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <p><strong><?php esc_html_e('Yes, this fits your vehicle!', 'autoparts-pro'); ?></strong></p>
                    </div>
                </div>
                <div class="compatibility-vehicle-display" id="selected-vehicle-display"></div>
            </div>
        </div>

        <!-- Right Column: Product Info -->
        <div class="product-info-section">
            <!-- Title & Ratings -->
            <div class="product-header">
                <?php if ($discount_percentage > 0) : ?>
                <span class="discount-badge">
                    -<?php echo esc_html($discount_percentage); ?>%
                </span>
                <?php endif; ?>
                
                <h1 class="product-title"><?php the_title(); ?></h1>
                
                <div class="product-meta">
                    <?php if ($sku) : ?>
                    <span class="product-sku"><?php printf(__('SKU: %s', 'autoparts-pro'), '<strong>' . esc_html($sku) . '</strong>'); ?></span>
                    <?php endif; ?>
                    
                    <?php if ($oem_number) : ?>
                    <span class="product-oem"><?php printf(__('OEM: %s', 'autoparts-pro'), '<strong>' . esc_html($oem_number) . '</strong>'); ?></span>
                    <?php endif; ?>
                </div>

                <!-- Star Rating -->
                <div class="product-rating-summary">
                    <?php 
                    $rating_count = $product->get_rating_count();
                    $average_rating = $product->get_average_rating();
                    ?>
                    <div class="star-rating-display">
                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                        <svg class="star <?php echo $i <= $average_rating ? 'filled' : ''; ?>" viewBox="0 0 24 24" fill="currentColor">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                        <?php endfor; ?>
                    </div>
                    <?php if ($rating_count > 0) : ?>
                    <span class="rating-count">
                        <?php printf(_n('%d review', '%d reviews', $rating_count, 'autoparts-pro'), $rating_count); ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Price -->
            <div class="product-price-section">
                <div class="price-display">
                    <?php if ($sale_price) : ?>
                    <span class="regular-price"><del><?php echo wc_price($regular_price); ?></del></span>
                    <span class="sale-price"><?php echo wc_price($sale_price); ?></span>
                    <?php else : ?>
                    <span class="current-price"><?php echo wc_price($regular_price); ?></span>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($bulk_pricing)) : ?>
                <div class="bulk-pricing-tiers">
                    <p class="tiers-title"><?php esc_html_e('Bulk Pricing:', 'autoparts-pro'); ?></p>
                    <ul class="pricing-tier-list">
                        <?php foreach ($bulk_pricing as $tier) : ?>
                        <li class="tier-item">
                            <span class="tier-quantity"><?php echo esc_html($tier['min_qty'] . '+' . ($tier['max_qty'] ? '-' . $tier['max_qty'] : '')); ?></span>
                            <span class="tier-price"><?php echo wc_price($tier['price']); ?></span>
                            <span class="tier-save"><?php printf(__('Save %s', 'autoparts-pro'), $tier['save_percent'] . '%'); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>

            <!-- Stock Status -->
            <div class="product-stock-status stock-status-<?php echo $is_in_stock ? 'in-stock' : 'out-of-stock'; ?>">
                <?php if ($is_in_stock) : ?>
                    <?php if ($stock_quantity && $stock_quantity < 10) : ?>
                    <span class="stock-indicator low-stock">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <?php printf(__('Only %d left in stock!', 'autoparts-pro'), $stock_quantity); ?>
                    </span>
                    <?php else : ?>
                    <span class="stock-indicator in-stock">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <?php esc_html_e('In Stock', 'autoparts-pro'); ?>
                    </span>
                    <?php endif; ?>
                <?php else : ?>
                <span class="stock-indicator out-of-stock">
                    <?php esc_html_e('Out of Stock', 'autoparts-pro'); ?>
                </span>
                <?php endif; ?>
            </div>

            <!-- Short Description -->
            <div class="product-short-description">
                <?php echo wp_kses_post($product->get_short_description()); ?>
            </div>

            <!-- Part Numbers -->
            <?php if ($manufacturer_part || $cross_reference) : ?>
            <div class="product-part-numbers">
                <?php if ($manufacturer_part) : ?>
                <p><strong><?php esc_html_e('Manufacturer Part:', 'autoparts-pro'); ?></strong> <?php echo esc_html($manufacturer_part); ?></p>
                <?php endif; ?>
                
                <?php if ($cross_reference) : ?>
                <p><strong><?php esc_html_e('Cross Reference:', 'autoparts-pro'); ?></strong> <?php echo esc_html($cross_reference); ?></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Add to Cart Form -->
            <form class="cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
                <div class="cart-actions-group">
                    <?php if ($product->is_sold_individually()) : ?>
                    <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product_id); ?>" />
                    <?php else : ?>
                    <div class="quantity-selector">
                        <label for="quantity-<?php echo esc_attr($product_id); ?>"><?php esc_html_e('Quantity:', 'autoparts-pro'); ?></label>
                        <div class="quantity-input-group">
                            <button type="button" class="qty-btn qty-minus" data-action="minus">-</button>
                            <input type="number" id="quantity-<?php echo esc_attr($product_id); ?>" 
                                   class="qty-input" 
                                   name="quantity" 
                                   value="1" 
                                   min="1" 
                                   max="<?php echo $stock_quantity ? esc_attr($stock_quantity) : '999'; ?>" 
                                   step="1">
                            <button type="button" class="qty-btn qty-plus" data-action="plus">+</button>
                        </div>
                    </div>
                    <?php endif; ?>

                    <button type="submit" 
                            name="add-to-cart" 
                            value="<?php echo esc_attr($product_id); ?>" 
                            class="btn btn-primary add-to-cart-btn single_add_to_cart_button"
                            <?php disabled($product->is_purchasable() && $product->is_in_stock(), false); ?>>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="9" cy="21" r="1"/>
                            <circle cx="20" cy="21" r="1"/>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                        </svg>
                        <span class="btn-text"><?php echo esc_html($product->single_add_to_cart_text()); ?></span>
                    </button>
                </div>
            </form>

            <!-- Wishlist & Share -->
            <div class="product-actions-secondary">
                <button type="button" class="btn btn-outline wishlist-btn" data-product-id="<?php echo esc_attr($product_id); ?>">
                    <svg class="heart-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                    <span class="wishlist-text"><?php esc_html_e('Add to Wishlist', 'autoparts-pro'); ?></span>
                </button>

                <button type="button" class="btn btn-outline share-btn" id="share-product-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="18" cy="5" r="3"/>
                        <circle cx="6" cy="12" r="3"/>
                        <circle cx="18" cy="19" r="3"/>
                        <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                        <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                    </svg>
                    <span><?php esc_html_e('Share', 'autoparts-pro'); ?></span>
                </button>
            </div>

            <!-- Installation Guide -->
            <?php if ($installation_guide) : ?>
            <div class="product-installation-guide">
                <a href="<?php echo esc_url($installation_guide); ?>" class="btn btn-link" target="_blank" rel="noopener">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                    <?php esc_html_e('Download Installation Guide (PDF)', 'autoparts-pro'); ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tabs Section -->
    <div class="product-tabs-container">
        <div class="product-tabs-nav">
            <button class="tab-btn active" data-tab="description"><?php esc_html_e('Description', 'autoparts-pro'); ?></button>
            <button class="tab-btn" data-tab="specifications"><?php esc_html_e('Specifications', 'autoparts-pro'); ?></button>
            <button class="tab-btn" data-tab="compatibility"><?php esc_html_e('Compatibility', 'autoparts-pro'); ?></button>
            <button class="tab-btn" data-tab="reviews"><?php esc_html_e('Reviews', 'autoparts-pro'); ?></button>
        </div>

        <div class="product-tabs-content">
            <!-- Description Tab -->
            <div class="tab-panel active" id="tab-description">
                <div class="tab-content-inner">
                    <?php the_content(); ?>
                </div>
            </div>

            <!-- Specifications Tab -->
            <div class="tab-panel" id="tab-specifications">
                <div class="tab-content-inner">
                    <?php
                    $specs = get_post_meta($product_id, '_product_specifications', true);
                    if ($specs) :
                    ?>
                    <table class="specs-table">
                        <?php foreach ($specs as $spec) : ?>
                        <tr>
                            <th><?php echo esc_html($spec['name']); ?></th>
                            <td><?php echo esc_html($spec['value']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                    <?php else : ?>
                    <p><?php esc_html_e('No specifications available.', 'autoparts-pro'); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Compatibility Tab -->
            <div class="tab-panel" id="tab-compatibility">
                <div class="tab-content-inner">
                    <?php if ($compatibility_data) : ?>
                    <div class="compatibility-list">
                        <?php foreach ($compatibility_data as $vehicle) : ?>
                        <div class="compatible-vehicle">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <span><?php echo esc_html($vehicle['year'] . ' ' . $vehicle['make'] . ' ' . $vehicle['model'] . ' - ' . $vehicle['engine']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else : ?>
                    <p><?php esc_html_e('Use the compatibility checker above to verify fitment.', 'autoparts-pro'); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Reviews Tab -->
            <div class="tab-panel" id="tab-reviews">
                <div class="tab-content-inner">
                    <?php comments_template(); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php 
    $related_products = wc_get_related_products($product_id, 4);
    if (!empty($related_products)) :
    ?>
    <section class="related-products-section">
        <h2 class="section-title"><?php esc_html_e('Related Parts', 'autoparts-pro'); ?></h2>
        <div class="products-grid">
            <?php
            foreach ($related_products as $related_id) :
                $related_product = wc_get_product($related_id);
                if (!$related_product) continue;
            ?>
            <div class="product-card">
                <!-- Product card content would be included via template part -->
                <?php get_template_part('template-parts/product/product-card', null, array('product' => $related_product)); ?>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</article>

<!-- 360 Viewer Modal -->
<div class="modal viewer-modal" id="viewer-360-modal" style="display: none;">
    <div class="modal-content modal-large">
        <button class="modal-close" id="close-360-viewer">&times;</button>
        <div class="viewer-360-container" id="viewer-360-canvas">
            <!-- 360 view canvas will be injected here -->
        </div>
    </div>
</div>

<!-- Share Modal -->
<div class="modal share-modal" id="share-modal" style="display: none;">
    <div class="modal-content modal-small">
        <button class="modal-close" id="close-share-modal">&times;</button>
        <h3><?php esc_html_e('Share This Product', 'autoparts-pro'); ?></h3>
        <div class="share-options">
            <button class="share-option" data-platform="facebook">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                Facebook
            </button>
            <button class="share-option" data-platform="twitter">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
                Twitter
            </button>
            <button class="share-option" data-platform="email">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                Email
            </button>
            <button class="share-option" id="copy-link-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                <?php esc_html_e('Copy Link', 'autoparts-pro'); ?>
            </button>
        </div>
        <div class="qr-code-container" id="share-qr-code"></div>
    </div>
</div>

<?php
get_footer();
