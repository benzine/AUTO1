<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package AutoParts_Pro
 */

get_header();
?>

<main id="primary" class="site-main error-404-template">
    <div class="container">
        <section class="error-404-content">
            <div class="error-code-display">
                <span class="error-code">404</span>
            </div>
            
            <header class="page-header">
                <h1 class="page-title"><?php esc_html_e('Page Not Found', 'autoparts-pro'); ?></h1>
                <p class="error-message"><?php esc_html_e('Oops! The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'autoparts-pro'); ?></p>
            </header>

            <div class="error-actions">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    </svg>
                    <?php esc_html_e('Back to Home', 'autoparts-pro'); ?>
                </a>
                
                <div class="error-search">
                    <p><?php esc_html_e('Or try searching our site:', 'autoparts-pro'); ?></p>
                    <?php get_search_form(); ?>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="error-quick-links">
                <h3><?php esc_html_e('Popular Categories', 'autoparts-pro'); ?></h3>
                <div class="quick-links-grid">
                    <?php
                    $categories = get_terms(array(
                        'taxonomy' => 'product_cat',
                        'hide_empty' => true,
                        'number' => 4,
                    ));
                    
                    if (!empty($categories) && !is_wp_error($categories)) :
                        foreach ($categories as $category) :
                    ?>
                    <a href="<?php echo esc_url(get_term_link($category)); ?>" class="quick-link-card">
                        <span class="quick-link-name"><?php echo esc_html($category->name); ?></span>
                        <span class="quick-link-count"><?php echo sprintf(_n('%d product', '%d products', $category->count, 'autoparts-pro'), $category->count); ?></span>
                    </a>
                    <?php 
                        endforeach;
                    endif; 
                    ?>
                </div>
            </div>

            <!-- Contact Support -->
            <div class="error-support">
                <h3><?php esc_html_e('Need Help?', 'autoparts-pro'); ?></h3>
                <p><?php esc_html_e('Our automotive experts are here to assist you.', 'autoparts-pro'); ?></p>
                <div class="support-buttons">
                    <a href="<?php echo esc_url(get_option('autoparts_contact_page_url', '/contact/')); ?>" class="btn btn-outline">
                        <?php esc_html_e('Contact Us', 'autoparts-pro'); ?>
                    </a>
                    <a href="tel:<?php echo esc_attr(get_option('autoparts_phone_number', '')); ?>" class="btn btn-outline">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                        <?php esc_html_e('Call Us', 'autoparts-pro'); ?>
                    </a>
                </div>
            </div>
        </section>
    </div>
</main>

<?php
get_footer();
