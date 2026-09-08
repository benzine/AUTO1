<?php
/**
 * The template for displaying archive pages
 *
 * @package AutoParts_Pro
 */

get_header();
?>

<main id="primary" class="site-main archive-template">
    <header class="archive-header">
        <div class="container">
            <?php autoparts_breadcrumbs(); ?>
            
            <?php
            the_archive_title('<h1 class="archive-title">', '</h1>');
            the_archive_description('<div class="archive-description">', '</div>');
            ?>
        </div>
    </header>

    <div class="container">
        <?php if (have_posts()) : ?>
        
        <div class="posts-grid archive-grid">
            <?php while (have_posts()) : the_post(); ?>
                <?php get_template_part('template-parts/content/content', get_post_type()); ?>
            <?php endwhile; ?>
        </div>

        <?php
        the_posts_pagination(array(
            'mid_size'  => 2,
            'prev_text' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>' . __('Previous', 'autoparts-pro'),
            'next_text' => __('Next', 'autoparts-pro') . '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>',
        ));
        ?>

        <?php else : ?>
            <?php get_template_part('template-parts/content/content', 'none'); ?>
        <?php endif; ?>
    </div>
</main>

<?php
get_footer();
