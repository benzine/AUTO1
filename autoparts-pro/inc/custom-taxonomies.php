<?php
/**
 * Register Custom Taxonomies
 *
 * @package AutoParts_Pro
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register custom taxonomies
 */
function autoparts_pro_register_taxonomies() {
    // Vehicle Makes
    register_taxonomy('vehicle_make', array('product', 'vehicle'), array(
        'labels' => array(
            'name'                       => esc_html__('Vehicle Makes', 'autoparts-pro'),
            'singular_name'              => esc_html__('Vehicle Make', 'autoparts-pro'),
            'menu_name'                  => esc_html__('Makes', 'autoparts-pro'),
            'all_items'                  => esc_html__('All Makes', 'autoparts-pro'),
            'edit_item'                  => esc_html__('Edit Make', 'autoparts-pro'),
            'view_item'                  => esc_html__('View Make', 'autoparts-pro'),
            'update_item'                => esc_html__('Update Make', 'autoparts-pro'),
            'add_new_item'               => esc_html__('Add New Make', 'autoparts-pro'),
            'new_item_name'              => esc_html__('New Make Name', 'autoparts-pro'),
            'parent_item'                => esc_html__('Parent Make', 'autoparts-pro'),
            'parent_item_colon'          => esc_html__('Parent Make:', 'autoparts-pro'),
            'search_items'               => esc_html__('Search Makes', 'autoparts-pro'),
            'popular_items'              => esc_html__('Popular Makes', 'autoparts-pro'),
            'separate_items_with_commas' => esc_html__('Separate makes with commas', 'autoparts-pro'),
            'add_or_remove_items'        => esc_html__('Add or remove makes', 'autoparts-pro'),
            'choose_from_most_used'      => esc_html__('Choose from the most used makes', 'autoparts-pro'),
            'not_found'                  => esc_html__('No makes found', 'autoparts-pro'),
        ),
        'hierarchical'       => false,
        'public'             => true,
        'show_ui'            => true,
        'show_admin_column'  => true,
        'show_in_nav_menus'  => true,
        'show_tagcloud'      => false,
        'rewrite'            => array('slug' => 'vehicle-make'),
        'show_in_rest'       => true,
    ));

    // Vehicle Models
    register_taxonomy('vehicle_model', array('product', 'vehicle'), array(
        'labels' => array(
            'name'                       => esc_html__('Vehicle Models', 'autoparts-pro'),
            'singular_name'              => esc_html__('Vehicle Model', 'autoparts-pro'),
            'menu_name'                  => esc_html__('Models', 'autoparts-pro'),
            'all_items'                  => esc_html__('All Models', 'autoparts-pro'),
            'edit_item'                  => esc_html__('Edit Model', 'autoparts-pro'),
            'view_item'                  => esc_html__('View Model', 'autoparts-pro'),
            'update_item'                => esc_html__('Update Model', 'autoparts-pro'),
            'add_new_item'               => esc_html__('Add New Model', 'autoparts-pro'),
            'new_item_name'              => esc_html__('New Model Name', 'autoparts-pro'),
            'parent_item'                => esc_html__('Parent Model', 'autoparts-pro'),
            'parent_item_colon'          => esc_html__('Parent Model:', 'autoparts-pro'),
            'search_items'               => esc_html__('Search Models', 'autoparts-pro'),
            'popular_items'              => esc_html__('Popular Models', 'autoparts-pro'),
            'separate_items_with_commas' => esc_html__('Separate models with commas', 'autoparts-pro'),
            'add_or_remove_items'        => esc_html__('Add or remove models', 'autoparts-pro'),
            'choose_from_most_used'      => esc_html__('Choose from the most used models', 'autoparts-pro'),
            'not_found'                  => esc_html__('No models found', 'autoparts-pro'),
        ),
        'hierarchical'       => true,
        'public'             => true,
        'show_ui'            => true,
        'show_admin_column'  => true,
        'show_in_nav_menus'  => true,
        'show_tagcloud'      => false,
        'rewrite'            => array('slug' => 'vehicle-model'),
        'show_in_rest'       => true,
    ));

    // Vehicle Years
    register_taxonomy('vehicle_year', array('product', 'vehicle'), array(
        'labels' => array(
            'name'              => esc_html__('Vehicle Years', 'autoparts-pro'),
            'singular_name'     => esc_html__('Vehicle Year', 'autoparts-pro'),
            'menu_name'         => esc_html__('Years', 'autoparts-pro'),
            'all_items'         => esc_html__('All Years', 'autoparts-pro'),
            'search_items'      => esc_html__('Search Years', 'autoparts-pro'),
            'popular_items'     => esc_html__('Popular Years', 'autoparts-pro'),
            'add_or_remove_items' => esc_html__('Add or remove years', 'autoparts-pro'),
        ),
        'hierarchical'       => false,
        'public'             => true,
        'show_ui'            => true,
        'show_admin_column'  => true,
        'show_in_nav_menus'  => false,
        'show_tagcloud'      => false,
        'rewrite'            => array('slug' => 'vehicle-year'),
        'show_in_rest'       => true,
    ));

    // Brands taxonomy for products
    register_taxonomy('product_brand', array('product'), array(
        'labels' => array(
            'name'                       => esc_html__('Brands', 'autoparts-pro'),
            'singular_name'              => esc_html__('Brand', 'autoparts-pro'),
            'menu_name'                  => esc_html__('Brands', 'autoparts-pro'),
            'all_items'                  => esc_html__('All Brands', 'autoparts-pro'),
            'edit_item'                  => esc_html__('Edit Brand', 'autoparts-pro'),
            'view_item'                  => esc_html__('View Brand', 'autoparts-pro'),
            'update_item'                => esc_html__('Update Brand', 'autoparts-pro'),
            'add_new_item'               => esc_html__('Add New Brand', 'autoparts-pro'),
            'new_item_name'              => esc_html__('New Brand Name', 'autoparts-pro'),
            'parent_item'                => esc_html__('Parent Brand', 'autoparts-pro'),
            'parent_item_colon'          => esc_html__('Parent Brand:', 'autoparts-pro'),
            'search_items'               => esc_html__('Search Brands', 'autoparts-pro'),
            'popular_items'              => esc_html__('Popular Brands', 'autoparts-pro'),
            'separate_items_with_commas' => esc_html__('Separate brands with commas', 'autoparts-pro'),
            'add_or_remove_items'        => esc_html__('Add or remove brands', 'autoparts-pro'),
            'choose_from_most_used'      => esc_html__('Choose from the most used brands', 'autoparts-pro'),
            'not_found'                  => esc_html__('No brands found', 'autoparts-pro'),
        ),
        'hierarchical'       => true,
        'public'             => true,
        'show_ui'            => true,
        'show_admin_column'  => true,
        'show_in_nav_menus'  => true,
        'show_tagcloud'      => true,
        'rewrite'            => array('slug' => 'brand'),
        'show_in_rest'       => true,
    ));

    // Part Type taxonomy
    register_taxonomy('part_type', array('product'), array(
        'labels' => array(
            'name'              => esc_html__('Part Types', 'autoparts-pro'),
            'singular_name'     => esc_html__('Part Type', 'autoparts-pro'),
            'menu_name'         => esc_html__('Part Types', 'autoparts-pro'),
            'all_items'         => esc_html__('All Part Types', 'autoparts-pro'),
            'edit_item'         => esc_html__('Edit Part Type', 'autoparts-pro'),
            'view_item'         => esc_html__('View Part Type', 'autoparts-pro'),
            'update_item'       => esc_html__('Update Part Type', 'autoparts-pro'),
            'add_new_item'      => esc_html__('Add New Part Type', 'autoparts-pro'),
            'new_item_name'     => esc_html__('New Part Type Name', 'autoparts-pro'),
            'parent_item'       => esc_html__('Parent Part Type', 'autoparts-pro'),
            'parent_item_colon' => esc_html__('Parent Part Type:', 'autoparts-pro'),
            'search_items'      => esc_html__('Search Part Types', 'autoparts-pro'),
            'not_found'         => esc_html__('No part types found', 'autoparts-pro'),
        ),
        'hierarchical'       => true,
        'public'             => true,
        'show_ui'            => true,
        'show_admin_column'  => true,
        'show_in_nav_menus'  => true,
        'show_tagcloud'      => false,
        'rewrite'            => array('slug' => 'part-type'),
        'show_in_rest'       => true,
    ));

    // Compatibility taxonomy for products
    register_taxonomy('compatibility', array('product'), array(
        'labels' => array(
            'name'              => esc_html__('Compatibility', 'autoparts-pro'),
            'singular_name'     => esc_html__('Compatibility', 'autoparts-pro'),
            'menu_name'         => esc_html__('Compatibility', 'autoparts-pro'),
            'all_items'         => esc_html__('All Compatibilities', 'autoparts-pro'),
            'search_items'      => esc_html__('Search Compatibilities', 'autoparts-pro'),
            'not_found'         => esc_html__('No compatibilities found', 'autoparts-pro'),
        ),
        'hierarchical'       => false,
        'public'             => false,
        'show_ui'            => false,
        'show_admin_column'  => false,
        'show_in_nav_menus'  => false,
        'show_tagcloud'      => false,
        'rewrite'            => false,
        'show_in_rest'       => true,
    ));
}
add_action('init', 'autoparts_pro_register_taxonomies');

/**
 * Add custom fields to vehicle make/model terms
 */
function autoparts_pro_vehicle_taxonomy_meta() {
    ?>
    <div class="form-field term-vehicle-years-wrap">
        <label for="term-vehicle-years"><?php esc_html_e('Years', 'autoparts-pro'); ?></label>
        <input type="text" name="term_vehicle_years" id="term-vehicle-years" value="" />
        <p class="description"><?php esc_html_e('Comma-separated list of years (e.g., 2015, 2016, 2017)', 'autoparts-pro'); ?></p>
    </div>
    <?php
}
add_action('vehicle_make_add_form_fields', 'autoparts_pro_vehicle_taxonomy_meta');
add_action('vehicle_model_add_form_fields', 'autoparts_pro_vehicle_taxonomy_meta');
