<?php
/**
 * The template for displaying all pages
 *
 * @package AutoParts_Pro
 */

get_header();
?>

<main id="primary" class="site-main page-template">
    <?php
    while (have_posts()) :
        the_post();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('page-content'); ?>>
            <?php if (!is_front_page()) : ?>
            <header class="entry-header">
                <div class="container">
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                </div>
            </header>
            <?php endif; ?>

            <div class="entry-content container">
                <?php
                the_content();

                wp_link_pages(array(
                    'before' => '<div class="page-links">' . __('Pages:', 'autoparts-pro'),
                    'after'  => '</div>',
                ));
                ?>
            </div>
        </article>
        <?php
        // If comments are open or we have at least one comment, load up the comment template.
        if (comments_open() || get_comments_number()) :
            comments_template();
        endif;

    endwhile;
    ?>
</main>

<?php
get_footer();
