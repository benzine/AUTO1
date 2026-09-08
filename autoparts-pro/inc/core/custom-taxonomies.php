<?php
/**
 * Custom Taxonomies
 *
 * @package AutoParts_Pro
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register custom taxonomies.
 */
function autoparts_pro_register_taxonomies() {
	// Vehicle Make taxonomy.
	register_taxonomy(
		'vehicle_make',
		array( 'product', 'vehicle' ),
		array(
			'labels'       => array(
				'name'                       => __( 'Vehicle Makes', 'autoparts-pro' ),
				'singular_name'              => __( 'Vehicle Make', 'autoparts-pro' ),
				'menu_name'                  => __( 'Makes', 'autoparts-pro' ),
				'all_items'                  => __( 'All Makes', 'autoparts-pro' ),
				'edit_item'                  => __( 'Edit Make', 'autoparts-pro' ),
				'view_item'                  => __( 'View Make', 'autoparts-pro' ),
				'update_item'                => __( 'Update Make', 'autoparts-pro' ),
				'add_new_item'               => __( 'Add New Make', 'autoparts-pro' ),
				'new_item_name'              => __( 'New Make Name', 'autoparts-pro' ),
				'parent_item'                => __( 'Parent Make', 'autoparts-pro' ),
				'parent_item_colon'          => __( 'Parent Make:', 'autoparts-pro' ),
				'search_items'               => __( 'Search Makes', 'autoparts-pro' ),
				'popular_items'              => __( 'Popular Makes', 'autoparts-pro' ),
				'separate_items_with_commas' => __( 'Separate makes with commas', 'autoparts-pro' ),
				'add_or_remove_items'        => __( 'Add or remove makes', 'autoparts-pro' ),
				'choose_from_most_used'      => __( 'Choose from the most used makes', 'autoparts-pro' ),
				'not_found'                  => __( 'No makes found', 'autoparts-pro' ),
			),
			'public'       => true,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'hierarchical' => false,
			'show_admin_column' => true,
			'rewrite'      => array( 'slug' => 'vehicle-make' ),
		)
	);

	// Vehicle Model taxonomy.
	register_taxonomy(
		'vehicle_model',
		array( 'product', 'vehicle' ),
		array(
			'labels'       => array(
				'name'                       => __( 'Vehicle Models', 'autoparts-pro' ),
				'singular_name'              => __( 'Vehicle Model', 'autoparts-pro' ),
				'menu_name'                  => __( 'Models', 'autoparts-pro' ),
				'all_items'                  => __( 'All Models', 'autoparts-pro' ),
				'edit_item'                  => __( 'Edit Model', 'autoparts-pro' ),
				'view_item'                  => __( 'View Model', 'autoparts-pro' ),
				'update_item'                => __( 'Update Model', 'autoparts-pro' ),
				'add_new_item'               => __( 'Add New Model', 'autoparts-pro' ),
				'new_item_name'              => __( 'New Model Name', 'autoparts-pro' ),
				'parent_item'                => __( 'Parent Model', 'autoparts-pro' ),
				'parent_item_colon'          => __( 'Parent Model:', 'autoparts-pro' ),
				'search_items'               => __( 'Search Models', 'autoparts-pro' ),
				'popular_items'              => __( 'Popular Models', 'autoparts-pro' ),
				'separate_items_with_commas' => __( 'Separate models with commas', 'autoparts-pro' ),
				'add_or_remove_items'        => __( 'Add or remove models', 'autoparts-pro' ),
				'choose_from_most_used'      => __( 'Choose from the most used models', 'autoparts-pro' ),
				'not_found'                  => __( 'No models found', 'autoparts-pro' ),
			),
			'public'       => true,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'hierarchical' => true,
			'show_admin_column' => true,
			'rewrite'      => array( 'slug' => 'vehicle-model' ),
		)
	);

	// Vehicle Year taxonomy.
	register_taxonomy(
		'vehicle_year',
		array( 'product', 'vehicle' ),
		array(
			'labels'       => array(
				'name'              => __( 'Vehicle Years', 'autoparts-pro' ),
				'singular_name'     => __( 'Vehicle Year', 'autoparts-pro' ),
				'menu_name'         => __( 'Years', 'autoparts-pro' ),
				'all_items'         => __( 'All Years', 'autoparts-pro' ),
				'edit_item'         => __( 'Edit Year', 'autoparts-pro' ),
				'view_item'         => __( 'View Year', 'autoparts-pro' ),
				'update_item'       => __( 'Update Year', 'autoparts-pro' ),
				'add_new_item'      => __( 'Add New Year', 'autoparts-pro' ),
				'new_item_name'     => __( 'New Year Name', 'autoparts-pro' ),
				'search_items'      => __( 'Search Years', 'autoparts-pro' ),
				'popular_items'     => __( 'Popular Years', 'autoparts-pro' ),
				'not_found'         => __( 'No years found', 'autoparts-pro' ),
			),
			'public'       => true,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'hierarchical' => false,
			'show_admin_column' => true,
			'rewrite'      => array( 'slug' => 'vehicle-year' ),
		)
	);

	// Product Brand taxonomy.
	register_taxonomy(
		'product_brand',
		array( 'product' ),
		array(
			'labels'       => array(
				'name'                       => __( 'Product Brands', 'autoparts-pro' ),
				'singular_name'              => __( 'Product Brand', 'autoparts-pro' ),
				'menu_name'                  => __( 'Brands', 'autoparts-pro' ),
				'all_items'                  => __( 'All Brands', 'autoparts-pro' ),
				'edit_item'                  => __( 'Edit Brand', 'autoparts-pro' ),
				'view_item'                  => __( 'View Brand', 'autoparts-pro' ),
				'update_item'                => __( 'Update Brand', 'autoparts-pro' ),
				'add_new_item'               => __( 'Add New Brand', 'autoparts-pro' ),
				'new_item_name'              => __( 'New Brand Name', 'autoparts-pro' ),
				'parent_item'                => __( 'Parent Brand', 'autoparts-pro' ),
				'parent_item_colon'          => __( 'Parent Brand:', 'autoparts-pro' ),
				'search_items'               => __( 'Search Brands', 'autoparts-pro' ),
				'popular_items'              => __( 'Popular Brands', 'autoparts-pro' ),
				'separate_items_with_commas' => __( 'Separate brands with commas', 'autoparts-pro' ),
				'add_or_remove_items'        => __( 'Add or remove brands', 'autoparts-pro' ),
				'choose_from_most_used'      => __( 'Choose from the most used brands', 'autoparts-pro' ),
				'not_found'                  => __( 'No brands found', 'autoparts-pro' ),
			),
			'public'       => true,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'hierarchical' => false,
			'show_admin_column' => true,
			'rewrite'      => array( 'slug' => 'brand' ),
			'has_archive'  => true,
		)
	);

	// Part Type taxonomy.
	register_taxonomy(
		'part_type',
		array( 'product' ),
		array(
			'labels'       => array(
				'name'                       => __( 'Part Types', 'autoparts-pro' ),
				'singular_name'              => __( 'Part Type', 'autoparts-pro' ),
				'menu_name'                  => __( 'Part Types', 'autoparts-pro' ),
				'all_items'                  => __( 'All Part Types', 'autoparts-pro' ),
				'edit_item'                  => __( 'Edit Part Type', 'autoparts-pro' ),
				'view_item'                  => __( 'View Part Type', 'autoparts-pro' ),
				'update_item'                => __( 'Update Part Type', 'autoparts-pro' ),
				'add_new_item'               => __( 'Add New Part Type', 'autoparts-pro' ),
				'new_item_name'              => __( 'New Part Type Name', 'autoparts-pro' ),
				'parent_item'                => __( 'Parent Part Type', 'autoparts-pro' ),
				'parent_item_colon'          => __( 'Parent Part Type:', 'autoparts-pro' ),
				'search_items'               => __( 'Search Part Types', 'autoparts-pro' ),
				'popular_items'              => __( 'Popular Part Types', 'autoparts-pro' ),
				'separate_items_with_commas' => __( 'Separate part types with commas', 'autoparts-pro' ),
				'add_or_remove_items'        => __( 'Add or remove part types', 'autoparts-pro' ),
				'choose_from_most_used'      => __( 'Choose from the most used part types', 'autoparts-pro' ),
				'not_found'                  => __( 'No part types found', 'autoparts-pro' ),
			),
			'public'       => true,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'hierarchical' => true,
			'show_admin_column' => true,
			'rewrite'      => array( 'slug' => 'part-type' ),
		)
	);
}
add_action( 'init', 'autoparts_pro_register_taxonomies' );

/**
 * Register taxonomy meta.
 */
function autoparts_pro_taxonomy_meta() {
	// Brand logo.
	register_term_meta(
		'product_brand',
		'_brand_logo',
		array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
		)
	);

	// Brand website.
	register_term_meta(
		'product_brand',
		'_brand_website',
		array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	// Brand description.
	register_term_meta(
		'product_brand',
		'_brand_description',
		array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
			'sanitize_callback' => 'wp_kses_post',
		)
	);
}
add_action( 'init', 'autoparts_pro_taxonomy_meta' );
