<?php
/**
 * Custom Post Types
 *
 * @package AutoParts_Pro
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register custom post types.
 */
function autoparts_pro_register_post_types() {
	// Vehicle CPT.
	register_post_type(
		'vehicle',
		array(
			'labels'       => array(
				'name'               => __( 'Vehicles', 'autoparts-pro' ),
				'singular_name'      => __( 'Vehicle', 'autoparts-pro' ),
				'menu_name'          => __( 'Vehicles', 'autoparts-pro' ),
				'add_new'            => __( 'Add New', 'autoparts-pro' ),
				'add_new_item'       => __( 'Add New Vehicle', 'autoparts-pro' ),
				'edit_item'          => __( 'Edit Vehicle', 'autoparts-pro' ),
				'new_item'           => __( 'New Vehicle', 'autoparts-pro' ),
				'view_item'          => __( 'View Vehicle', 'autoparts-pro' ),
				'search_items'       => __( 'Search Vehicles', 'autoparts-pro' ),
				'not_found'          => __( 'No vehicles found', 'autoparts-pro' ),
				'not_found_in_trash' => __( 'No vehicles found in trash', 'autoparts-pro' ),
			),
			'public'       => true,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-car',
			'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
			'has_archive'  => true,
			'rewrite'      => array( 'slug' => 'vehicles' ),
		)
	);

	// Documentation CPT.
	register_post_type(
		'documentation',
		array(
			'labels'       => array(
				'name'               => __( 'Documentation', 'autoparts-pro' ),
				'singular_name'      => __( 'Document', 'autoparts-pro' ),
				'menu_name'          => __( 'Documentation', 'autoparts-pro' ),
				'add_new'            => __( 'Add New', 'autoparts-pro' ),
				'add_new_item'       => __( 'Add New Document', 'autoparts-pro' ),
				'edit_item'          => __( 'Edit Document', 'autoparts-pro' ),
				'new_item'           => __( 'New Document', 'autoparts-pro' ),
				'view_item'          => __( 'View Document', 'autoparts-pro' ),
				'search_items'       => __( 'Search Documents', 'autoparts-pro' ),
				'not_found'          => __( 'No documents found', 'autoparts-pro' ),
				'not_found_in_trash' => __( 'No documents found in trash', 'autoparts-pro' ),
			),
			'public'       => true,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-media-document',
			'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'comments' ),
			'has_archive'  => true,
			'hierarchical' => true,
			'rewrite'      => array( 'slug' => 'documentation' ),
		)
	);

	// Store Location CPT.
	register_post_type(
		'store_location',
		array(
			'labels'       => array(
				'name'               => __( 'Store Locations', 'autoparts-pro' ),
				'singular_name'      => __( 'Store Location', 'autoparts-pro' ),
				'menu_name'          => __( 'Store Locations', 'autoparts-pro' ),
				'add_new'            => __( 'Add New', 'autoparts-pro' ),
				'add_new_item'       => __( 'Add New Store Location', 'autoparts-pro' ),
				'edit_item'          => __( 'Edit Store Location', 'autoparts-pro' ),
				'new_item'           => __( 'New Store Location', 'autoparts-pro' ),
				'view_item'          => __( 'View Store Location', 'autoparts-pro' ),
				'search_items'       => __( 'Search Store Locations', 'autoparts-pro' ),
				'not_found'          => __( 'No store locations found', 'autoparts-pro' ),
				'not_found_in_trash' => __( 'No store locations found in trash', 'autoparts-pro' ),
			),
			'public'       => true,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-location',
			'supports'     => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
			'has_archive'  => false,
			'rewrite'      => array( 'slug' => 'store-locations' ),
		)
	);

	// Form Submission CPT (for contact forms).
	register_post_type(
		'form_submission',
		array(
			'labels'       => array(
				'name'               => __( 'Form Submissions', 'autoparts-pro' ),
				'singular_name'      => __( 'Form Submission', 'autoparts-pro' ),
				'menu_name'          => __( 'Form Submissions', 'autoparts-pro' ),
				'add_new'            => __( 'Add New', 'autoparts-pro' ),
				'add_new_item'       => __( 'Add New Submission', 'autoparts-pro' ),
				'edit_item'          => __( 'Edit Submission', 'autoparts-pro' ),
				'new_item'           => __( 'New Submission', 'autoparts-pro' ),
				'view_item'          => __( 'View Submission', 'autoparts-pro' ),
				'search_items'       => __( 'Search Submissions', 'autoparts-pro' ),
				'not_found'          => __( 'No submissions found', 'autoparts-pro' ),
				'not_found_in_trash' => __( 'No submissions found in trash', 'autoparts-pro' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => false,
			'menu_icon'    => 'dashicons-feedback',
			'supports'     => array( 'title', 'custom-fields' ),
			'capability_type' => 'post',
			'capabilities' => array(
				'edit_post'          => 'edit_posts',
				'read_post'          => 'read_posts',
				'delete_post'        => 'delete_posts',
				'edit_posts'         => 'edit_posts',
				'publish_posts'      => 'publish_posts',
				'read_private_posts' => 'read_private_posts',
			),
			'rewrite'      => false,
		)
	);

	// Wishlist Page (virtual - we'll create a page option).
	register_post_type(
		'autoparts_wishlist',
		array(
			'labels'       => array(
				'name'          => __( 'Wishlists', 'autoparts-pro' ),
				'singular_name' => __( 'Wishlist', 'autoparts-pro' ),
			),
			'public'       => false,
			'show_ui'      => false,
			'show_in_menu' => false,
			'show_in_rest' => true,
			'supports'     => array( 'title' ),
			'rewrite'      => false,
		)
	);
}
add_action( 'init', 'autoparts_pro_register_post_types' );

/**
 * Register meta boxes for custom post types.
 */
function autoparts_pro_register_meta() {
	// Vehicle meta.
	register_post_meta(
		'vehicle',
		'_vehicle_year',
		array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	register_post_meta(
		'vehicle',
		'_vehicle_make',
		array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	register_post_meta(
		'vehicle',
		'_vehicle_model',
		array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	register_post_meta(
		'vehicle',
		'_vehicle_engine',
		array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	// Store location meta.
	register_post_meta(
		'store_location',
		'_store_address',
		array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	register_post_meta(
		'store_location',
		'_store_phone',
		array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	register_post_meta(
		'store_location',
		'_store_email',
		array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_email',
		)
	);

	register_post_meta(
		'store_location',
		'_store_hours',
		array(
			'show_in_rest'      => true,
			'single'            => false,
			'type'              => 'array',
			'sanitize_callback' => 'wp_kses_post',
		)
	);

	register_post_meta(
		'store_location',
		'_store_latitude',
		array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'number',
			'sanitize_callback' => 'floatval',
		)
	);

	register_post_meta(
		'store_location',
		'_store_longitude',
		array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'number',
			'sanitize_callback' => 'floatval',
		)
	);

	// Documentation meta.
	register_post_meta(
		'documentation',
		'_doc_type',
		array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	register_post_meta(
		'documentation',
		'_doc_video_url',
		array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	register_post_meta(
		'documentation',
		'_doc_download_url',
		array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
}
add_action( 'init', 'autoparts_pro_register_meta' );
