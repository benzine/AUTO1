<?php
/**
 * Register Custom Post Types
 *
 * @package AutoParts_Pro
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register custom post types
 */
function autoparts_pro_register_post_types() {
    // Vehicle Compatibility CPT
    register_post_type('vehicle', array(
        'labels' => array(
            'name'               => esc_html__('Vehicles', 'autoparts-pro'),
            'singular_name'      => esc_html__('Vehicle', 'autoparts-pro'),
            'menu_name'          => esc_html__('Vehicles', 'autoparts-pro'),
            'add_new'            => esc_html__('Add New', 'autoparts-pro'),
            'add_new_item'       => esc_html__('Add New Vehicle', 'autoparts-pro'),
            'edit_item'          => esc_html__('Edit Vehicle', 'autoparts-pro'),
            'new_item'           => esc_html__('New Vehicle', 'autoparts-pro'),
            'view_item'          => esc_html__('View Vehicle', 'autoparts-pro'),
            'search_items'       => esc_html__('Search Vehicles', 'autoparts-pro'),
            'not_found'          => esc_html__('No vehicles found', 'autoparts-pro'),
            'not_found_in_trash' => esc_html__('No vehicles found in trash', 'autoparts-pro'),
        ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'vehicle'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 20,
        'menu_icon'          => 'dashicons-car',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'show_in_rest'       => true,
    ));

    // Documentation CPT
    register_post_type('documentation', array(
        'labels' => array(
            'name'               => esc_html__('Documentation', 'autoparts-pro'),
            'singular_name'      => esc_html__('Documentation', 'autoparts-pro'),
            'menu_name'          => esc_html__('Documentation', 'autoparts-pro'),
            'add_new'            => esc_html__('Add New', 'autoparts-pro'),
            'add_new_item'       => esc_html__('Add New Documentation', 'autoparts-pro'),
            'edit_item'          => esc_html__('Edit Documentation', 'autoparts-pro'),
            'new_item'           => esc_html__('New Documentation', 'autoparts-pro'),
            'view_item'          => esc_html__('View Documentation', 'autoparts-pro'),
            'search_items'       => esc_html__('Search Documentation', 'autoparts-pro'),
            'not_found'          => esc_html__('No documentation found', 'autoparts-pro'),
            'not_found_in_trash' => esc_html__('No documentation found in trash', 'autoparts-pro'),
        ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'documentation'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => true,
        'menu_position'      => 21,
        'menu_icon'          => 'dashicons-media-document',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'comments', 'custom-fields'),
        'show_in_rest'       => true,
    ));

    // Store Locations CPT
    register_post_type('store', array(
        'labels' => array(
            'name'               => esc_html__('Store Locations', 'autoparts-pro'),
            'singular_name'      => esc_html__('Store Location', 'autoparts-pro'),
            'menu_name'          => esc_html__('Store Locations', 'autoparts-pro'),
            'add_new'            => esc_html__('Add New', 'autoparts-pro'),
            'add_new_item'       => esc_html__('Add New Store', 'autoparts-pro'),
            'edit_item'          => esc_html__('Edit Store', 'autoparts-pro'),
            'new_item'           => esc_html__('New Store', 'autoparts-pro'),
            'view_item'          => esc_html__('View Store', 'autoparts-pro'),
            'search_items'       => esc_html__('Search Stores', 'autoparts-pro'),
            'not_found'          => esc_html__('No stores found', 'autoparts-pro'),
            'not_found_in_trash' => esc_html__('No stores found in trash', 'autoparts-pro'),
        ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'store'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 22,
        'menu_icon'          => 'dashicons-location-alt',
        'supports'           => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'show_in_rest'       => true,
    ));

    // Form Submissions CPT
    register_post_type('form_submission', array(
        'labels' => array(
            'name'               => esc_html__('Form Submissions', 'autoparts-pro'),
            'singular_name'      => esc_html__('Form Submission', 'autoparts-pro'),
            'menu_name'          => esc_html__('Form Submissions', 'autoparts-pro'),
            'add_new'            => esc_html__('Add New', 'autoparts-pro'),
            'add_new_item'       => esc_html__('View Submission', 'autoparts-pro'),
            'edit_item'          => esc_html__('Edit Submission', 'autoparts-pro'),
            'new_item'           => esc_html__('New Submission', 'autoparts-pro'),
            'view_item'          => esc_html__('View Submission', 'autoparts-pro'),
            'search_items'       => esc_html__('Search Submissions', 'autoparts-pro'),
            'not_found'          => esc_html__('No submissions found', 'autoparts-pro'),
            'not_found_in_trash' => esc_html__('No submissions found in trash', 'autoparts-pro'),
        ),
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => false,
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 23,
        'menu_icon'          => 'dashicons-feedback',
        'supports'           => array('title', 'custom-fields'),
        'show_in_rest'       => false,
    ));
}
add_action('init', 'autoparts_pro_register_post_types');

/**
 * Flush rewrite rules on theme activation
 */
function autoparts_pro_flush_rewrite_rules() {
    autoparts_pro_register_post_types();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'autoparts_pro_flush_rewrite_rules');
