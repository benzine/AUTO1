<?php
/**
 * The template for displaying search results pages
 *
 * @package AutoParts_Pro
 */

get_header();
?>

<main id="primary" class="site-main search-template">
    <header class="search-header">
        <div class="container">
            <?php autoparts_breadcrumbs(); ?>
            
            <h1 class="search-title">
                <?php printf(__('Search Results for: %s', 'autoparts-pro'), '<span>' . get_search_query() . '</span>'); ?>
            </h1>
            
            <?php if (have_posts()) : ?>
            <p class="search-count">
                <?php printf(_n('%d result found', '%d results found', $wp_query->found_posts, 'autoparts-pro'), $wp_query->found_posts); ?>
            </p>
            <?php endif; ?>
        </div>
    </header>

    <div class="container">
        <?php if (have_posts()) : ?>
        
        <div class="posts-grid search-grid">
            <?php while (have_posts()) : the_post(); ?>
                <?php get_template_part('template-parts/content/content', get_post_type()); ?>
            <?php endwhile; ?>
        </div>

        <?php
        the_posts_pagination(array(
            'mid_size'  => 2,
            'prev_text' => __('Previous', 'autoparts-pro'),
            'next_text' => __('Next', 'autoparts-pro'),
        ));
        ?>

        <?php else : ?>
            <?php get_template_part('template-parts/content/content', 'none'); ?>
        <?php endif; ?>
    </div>
</main>

<?php
get_footer();
