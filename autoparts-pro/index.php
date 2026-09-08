<?php
/**
 * Main template file for AutoParts Pro
 * 
 * @package AutoParts_Pro
 */

get_header();

if (is_front_page()) {
    get_template_part('template-parts/hero/hero-3d');
}

if (have_posts()) :
    while (have_posts()) :
        the_post();
        get_template_part('template-parts/page/content', get_post_type());
    endwhile;
else :
    get_template_part('template-parts/page/content', 'none');
endif;

get_footer();
