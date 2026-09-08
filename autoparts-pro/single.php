<?php
/**
 * The template for displaying all single posts
 *
 * @package AutoParts_Pro
 */

get_header();
?>

<main id="primary" class="site-main single-post-template">
    <div class="container">
        <?php
        while (have_posts()) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('blog-post-single'); ?>>
                <header class="entry-header">
                    <?php autoparts_breadcrumbs(); ?>
                    
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                    
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
                        <?php
                        $categories = get_the_category();
                        if (!empty($categories)) :
                        ?>
                        <span class="cat-links">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                            </svg>
                            <?php
                            $category_links = array();
                            foreach ($categories as $category) {
                                $category_links[] = '<a href="' . esc_url(get_category_link($category)) . '">' . esc_html($category->name) . '</a>';
                            }
                            echo implode(', ', $category_links);
                            ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </header>

                <?php if (has_post_thumbnail()) : ?>
                <div class="featured-image">
                    <?php the_post_thumbnail('large'); ?>
                </div>
                <?php endif; ?>

                <div class="entry-content">
                    <?php
                    the_content();

                    wp_link_pages(array(
                        'before' => '<div class="page-links">' . __('Pages:', 'autoparts-pro'),
                        'after'  => '</div>',
                    ));
                    ?>
                </div>

                <footer class="entry-footer">
                    <?php
                    $tags = get_the_tags();
                    if ($tags) :
                    ?>
                    <div class="tags-links">
                        <span><?php esc_html_e('Tags:', 'autoparts-pro'); ?></span>
                        <?php
                        foreach ($tags as $tag) {
                            echo '<a href="' . esc_url(get_tag_link($tag)) . '">' . esc_html($tag->name) . '</a>';
                        }
                        ?>
                    </div>
                    <?php endif; ?>

                    <div class="post-share">
                        <span><?php esc_html_e('Share:', 'autoparts-pro'); ?></span>
                        <div class="share-buttons">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener" class="share-btn facebook">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" target="_blank" rel="noopener" class="share-btn twitter">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
                            </a>
                            <a href="mailto:?subject=<?php echo urlencode(get_the_title()); ?>&body=<?php echo urlencode(get_permalink()); ?>" class="share-btn email">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </footer>

                <?php
                // Author box
                if (get_the_author_meta('description')) :
                ?>
                <div class="author-box">
                    <div class="author-avatar">
                        <?php echo get_avatar(get_the_author_meta('ID'), 80); ?>
                    </div>
                    <div class="author-info">
                        <h3 class="author-name"><?php the_author(); ?></h3>
                        <p class="author-bio"><?php echo get_the_author_meta('description'); ?></p>
                        <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" class="author-link">
                            <?php esc_html_e('View all posts', 'autoparts-pro'); ?>
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Post navigation -->
                <nav class="post-navigation">
                    <div class="nav-previous">
                        <?php previous_post_link('%link', '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg><span class="nav-label">' . __('Previous', 'autoparts-pro') . '</span><span class="nav-title">%title</span>'); ?>
                    </div>
                    <div class="nav-next">
                        <?php next_post_link('%link', '<span class="nav-label">' . __('Next', 'autoparts-pro') . '</span><span class="nav-title">%title</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>'); ?>
                    </div>
                </nav>

                <?php
                // Related posts
                $related_posts = wp_get_recent_posts(array(
                    'numberposts' => 3,
                    'exclude' => array(get_the_ID()),
                    'post_status' => 'publish',
                ), OBJECT);

                if (!empty($related_posts)) :
                ?>
                <section class="related-posts">
                    <h2 class="section-title"><?php esc_html_e('Related Articles', 'autoparts-pro'); ?></h2>
                    <div class="related-posts-grid">
                        <?php foreach ($related_posts as $related_post) : ?>
                        <article class="related-post-card">
                            <?php if (has_post_thumbnail($related_post->ID)) : ?>
                            <a href="<?php echo get_permalink($related_post->ID); ?>">
                                <?php echo get_the_post_thumbnail($related_post->ID, 'medium'); ?>
                            </a>
                            <?php endif; ?>
                            <h3><a href="<?php echo get_permalink($related_post->ID); ?>"><?php echo esc_html(get_the_title($related_post->ID)); ?></a></h3>
                            <time datetime="<?php echo get_the_date('c', $related_post->ID); ?>"><?php echo get_the_date('', $related_post->ID); ?></time>
                        </article>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <?php
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;
                ?>
            </article>
            <?php
        endwhile;
        ?>
    </div>
</main>

<?php
get_footer();
