<?php
/**
 * Front page template
 *
 * @package AutoParts_Pro
 */

get_header();
?>

<main id="primary" class="site-main front-page">
    <?php
    // 3D Exploded View Hero
    get_template_part('template-parts/hero/hero-3d-exploded');
    
    // Vehicle Selector Section
    if (get_option('autoparts_show_vehicle_selector', true)) :
    ?>
    <section class="vehicle-selector-section">
        <div class="container">
            <?php 
            get_template_part('template-parts/forms/vehicle-selector', null, array(
                'style' => 'horizontal',
                'show_title' => true,
                'title' => __('Find the Right Parts for Your Vehicle', 'autoparts-pro'),
            )); 
            ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Featured Categories -->
    <section class="featured-categories-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php esc_html_e('Shop by Category', 'autoparts-pro'); ?></h2>
                <p class="section-subtitle"><?php esc_html_e('Browse our extensive catalog of premium auto parts', 'autoparts-pro'); ?></p>
            </div>
            
            <div class="categories-grid">
                <?php
                $categories = get_terms(array(
                    'taxonomy' => 'product_cat',
                    'hide_empty' => true,
                    'number' => 8,
                    'parent' => 0,
                ));
                
                if (!empty($categories) && !is_wp_error($categories)) :
                    foreach ($categories as $category) :
                        $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                        $image_url = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : get_template_directory_uri() . '/assets/images/category-placeholder.jpg';
                ?>
                <a href="<?php echo esc_url(get_term_link($category)); ?>" class="category-card">
                    <div class="category-image">
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($category->name); ?>" loading="lazy">
                        <div class="category-overlay">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                    <div class="category-info">
                        <h3><?php echo esc_html($category->name); ?></h3>
                        <span class="category-count"><?php echo sprintf(_n('%d Product', '%d Products', $category->count, 'autoparts-pro'), $category->count); ?></span>
                    </div>
                </a>
                <?php 
                    endforeach;
                endif; 
                ?>
            </div>
        </div>
    </section>

    <!-- Flash Deals -->
    <?php if (get_option('autoparts_show_flash_deals', true)) : ?>
    <section class="flash-deals-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php esc_html_e("Today's Flash Deals", 'autoparts-pro'); ?></h2>
                <div class="countdown-timer" data-end-time="<?php echo esc_attr(strtotime('tomorrow 00:00:00')); ?>">
                    <div class="time-segment">
                        <span class="time-value hours">00</span>
                        <span class="time-label"><?php esc_html_e('Hours', 'autoparts-pro'); ?></span>
                    </div>
                    <div class="time-segment">
                        <span class="time-value minutes">00</span>
                        <span class="time-label"><?php esc_html_e('Minutes', 'autoparts-pro'); ?></span>
                    </div>
                    <div class="time-segment">
                        <span class="time-value seconds">00</span>
                        <span class="time-label"><?php esc_html_e('Seconds', 'autoparts-pro'); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="products-grid" id="flash-deals-grid">
                <?php
                // Query products with sale prices
                $sale_products = wc_get_products(array(
                    'limit' => 4,
                    'status' => 'publish',
                    'on_sale' => true,
                ));
                
                if (!empty($sale_products)) :
                    foreach ($sale_products as $product) :
                        get_template_part('template-parts/product/product-card', null, array('product' => $product));
                    endforeach;
                else :
                ?>
                <p><?php esc_html_e('No flash deals available at the moment.', 'autoparts-pro'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Why Choose Us / Features -->
    <section class="features-section">
        <div class="container">
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                    <h3><?php esc_html_e('Genuine Quality', 'autoparts-pro'); ?></h3>
                    <p><?php esc_html_e('OEM and premium aftermarket parts backed by manufacturer warranties.', 'autoparts-pro'); ?></p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                            <line x1="1" y1="10" x2="23" y2="10"/>
                        </svg>
                    </div>
                    <h3><?php esc_html_e('Fast Shipping', 'autoparts-pro'); ?></h3>
                    <p><?php esc_html_e('Same-day dispatch for orders before 2PM. Free shipping over $99.', 'autoparts-pro'); ?></p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                        </svg>
                    </div>
                    <h3><?php esc_html_e('Expert Support', 'autoparts-pro'); ?></h3>
                    <p><?php esc_html_e('Our automotive specialists are here to help you find the right parts.', 'autoparts-pro'); ?></p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="23 4 23 10 17 10"/>
                            <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                        </svg>
                    </div>
                    <h3><?php esc_html_e('Easy Returns', 'autoparts-pro'); ?></h3>
                    <p><?php esc_html_e('30-day hassle-free returns. Wrong part? We will cover the exchange.', 'autoparts-pro'); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Brands Section -->
    <?php if (get_option('autoparts_show_brands', true)) : ?>
    <section class="brands-section">
        <div class="container">
            <h2 class="section-title"><?php esc_html_e('Trusted Brands', 'autoparts-pro'); ?></h2>
            <div class="brands-carousel">
                <?php
                $brands = get_terms(array(
                    'taxonomy' => 'product_brand',
                    'hide_empty' => true,
                    'number' => 10,
                ));
                
                if (!empty($brands) && !is_wp_error($brands)) :
                    foreach ($brands as $brand) :
                        $logo_id = get_term_meta($brand->term_id, 'brand_logo', true);
                        $logo_url = $logo_id ? wp_get_attachment_url($logo_id) : get_template_directory_uri() . '/assets/images/brand-placeholder.svg';
                ?>
                <div class="brand-item">
                    <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($brand->name); ?>" loading="lazy">
                </div>
                <?php 
                    endforeach;
                endif; 
                ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Testimonials -->
    <?php if (get_option('autoparts_show_testimonials', true)) : ?>
    <section class="testimonials-section">
        <div class="container">
            <h2 class="section-title"><?php esc_html_e('What Our Customers Say', 'autoparts-pro'); ?></h2>
            
            <div class="testimonials-slider">
                <?php
                $testimonials = get_posts(array(
                    'post_type' => 'testimonial',
                    'posts_per_page' => 3,
                    'post_status' => 'publish',
                ));
                
                if (!empty($testimonials)) :
                    foreach ($testimonials as $testimonial) :
                        $rating = get_post_meta($testimonial->ID, '_testimonial_rating', true) ?: 5;
                        $customer_name = get_post_meta($testimonial->ID, '_customer_name', true);
                        $vehicle_info = get_post_meta($testimonial->ID, '_customer_vehicle', true);
                ?>
                <div class="testimonial-card">
                    <div class="testimonial-rating">
                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                        <svg class="star <?php echo $i <= $rating ? 'filled' : ''; ?>" viewBox="0 0 24 24" fill="currentColor">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                        <?php endfor; ?>
                    </div>
                    <blockquote class="testimonial-content">
                        <?php echo wp_kses_post(get_the_content(null, false, $testimonial)); ?>
                    </blockquote>
                    <footer class="testimonial-author">
                        <cite><?php echo esc_html($customer_name); ?></cite>
                        <?php if ($vehicle_info) : ?>
                        <span class="testimonial-vehicle"><?php echo esc_html($vehicle_info); ?></span>
                        <?php endif; ?>
                    </footer>
                </div>
                <?php 
                    endforeach;
                endif; 
                ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2><?php esc_html_e('Need Help Finding the Right Part?', 'autoparts-pro'); ?></h2>
                <p><?php esc_html_e('Our experts are standing by to assist you. Contact us or use our live chat.', 'autoparts-pro'); ?></p>
                <div class="cta-buttons">
                    <a href="<?php echo esc_url(get_option('autoparts_contact_page_url', '/contact/')); ?>" class="btn btn-primary">
                        <?php esc_html_e('Contact Us', 'autoparts-pro'); ?>
                    </a>
                    <a href="<?php echo esc_url(get_option('autoparts_shop_page_url', '/shop/')); ?>" class="btn btn-outline">
                        <?php esc_html_e('Browse Catalog', 'autoparts-pro'); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();
