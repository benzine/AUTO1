<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @package AutoParts_Pro
 */
?>

<section class="no-results not-found">
    <header class="page-header">
        <h1 class="page-title"><?php esc_html_e('Nothing Found', 'autoparts-pro'); ?></h1>
    </header>

    <div class="page-content">
        <?php if (is_search()) : ?>
        <p><?php esc_html_e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'autoparts-pro'); ?></p>
        <?php get_search_form(); ?>
        
        <?php elseif (is_404()) : ?>
        <div class="error-404-content">
            <div class="error-code">404</div>
            <h2><?php esc_html_e('Page Not Found', 'autoparts-pro'); ?></h2>
            <p><?php esc_html_e('The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'autoparts-pro'); ?></p>
            
            <div class="error-actions">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    </svg>
                    <?php esc_html_e('Back to Home', 'autoparts-pro'); ?>
                </a>
                <?php get_search_form(); ?>
            </div>
        </div>
        
        <?php else : ?>
        <p><?php esc_html_e('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'autoparts-pro'); ?></p>
        <?php get_search_form(); ?>
        <?php endif; ?>
    </div>
</section>
