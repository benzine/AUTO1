<?php
/**
 * Main template file for AutoParts Pro
 *
 * @package AutoParts_Pro
 * @since 1.0.0
 */

// Silence is golden - WordPress handles routing
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load the main template based on WordPress template hierarchy
get_header();

// Determine which template part to load
if ( is_front_page() && is_home() ) {
	// Default homepage
	get_template_part( 'template-parts/home', 'default' );
} elseif ( is_front_page() ) {
	// Static front page
	get_template_part( 'template-parts/front', 'page' );
} elseif ( is_home() ) {
	// Blog posts index
	get_template_part( 'template-parts/blog', 'index' );
} elseif ( is_archive() ) {
	// Archive pages
	get_template_part( 'template-parts/archive', get_post_type() );
} elseif ( is_search() ) {
	// Search results
	get_template_part( 'template-parts/search', 'results' );
} elseif ( is_404() ) {
	// 404 page
	get_template_part( 'template-parts/error', '404' );
} else {
	// Single post/page
	get_template_part( 'template-parts/single', get_post_type() );
}

get_footer();