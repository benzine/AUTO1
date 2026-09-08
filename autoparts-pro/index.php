<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 *
 * @package AutoParts_Pro
 */

get_header();
?>

<main id="primary" class="site-main">
    <?php
    // Display 3D Hero on front page only if enabled
    if (is_front_page()) {
        get_template_part('template-parts/hero/hero-3d-exploded');
    }

    if (have_posts()) :
        ?>
        <div class="posts-grid">
            <?php
            while (have_posts()) :
                the_post();
                get_template_part('template-parts/content/content', get_post_type());
            endwhile;
            ?>
        </div>

        <?php
        the_posts_pagination(array(
            'mid_size'  => 2,
            'prev_text' => __('Previous', 'autoparts-pro'),
            'next_text' => __('Next', 'autoparts-pro'),
        ));

    else :
        get_template_part('template-parts/content/content', 'none');
    endif;
    ?>
</main>

<?php
get_footer();
