<?php
/**
 * Template part for displaying posts
 *
 * @package AutoParts_Pro
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('blog-post-card'); ?>>
    <?php if (has_post_thumbnail()) : ?>
    <div class="post-thumbnail">
        <a href="<?php the_permalink(); ?>">
            <?php the_post_thumbnail('large', array('loading' => 'lazy')); ?>
        </a>
    </div>
    <?php endif; ?>

    <div class="post-content">
        <header class="entry-header">
            <?php
            the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
            
            if ('post' === get_post_type()) :
            ?>
            <div class="entry-meta">
                <span class="posted-on">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
                </span>
                <span class="byline">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <?php the_author_posts_link(); ?>
                </span>
            </div>
            <?php endif; ?>
        </header>

        <div class="entry-summary">
            <?php the_excerpt(); ?>
        </div>

        <footer class="entry-footer">
            <a href="<?php the_permalink(); ?>" class="btn btn-outline read-more-btn">
                <?php esc_html_e('Read More', 'autoparts-pro'); ?>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </a>
        </footer>
    </div>
</article>
