<?php
/**
 * Custom Taxonomies Registration
 * 
 * @package AutoParts_Pro
 * @since 1.0.0
 */

namespace AutoParts_Pro\Core;

defined('ABSPATH') || exit;

class Taxonomy_Registration {
    
    public function __construct() {
        add_action('init', [$this, 'register_vehicle_taxonomies']);
        add_action('init', [$this, 'register_product_taxonomies']);
        add_action('init', [$this, 'register_documentation_taxonomy']);
    }
    
    /**
     * Vehicle Make/Model/Year Taxonomies for WooCommerce products
     */
    public function register_vehicle_taxonomies() {
        // Vehicle Year
        $year_labels = [
            'name'              => __('Years', 'autoparts-pro'),
            'singular_name'     => __('Year', 'autoparts-pro'),
            'search_items'      => __('Search Years', 'autoparts-pro'),
            'all_items'         => __('All Years', 'autoparts-pro'),
            'edit_item'         => __('Edit Year', 'autoparts-pro'),
            'update_item'       => __('Update Year', 'autoparts-pro'),
            'add_new_item'      => __('Add New Year', 'autoparts-pro'),
            'new_item_name'     => __('New Year', 'autoparts-pro'),
            'menu_name'         => __('Years', 'autoparts-pro'),
        ];
        
        register_taxonomy('vehicle_year', ['product'], [
            'labels'            => $year_labels,
            'hierarchical'      => false,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'vehicle-year'],
            'show_in_rest'      => true,
            'public'            => true,
        ]);
        
        // Vehicle Make
        $make_labels = [
            'name'              => __('Makes', 'autoparts-pro'),
            'singular_name'     => __('Make', 'autoparts-pro'),
            'search_items'      => __('Search Makes', 'autoparts-pro'),
            'all_items'         => __('All Makes', 'autoparts-pro'),
            'edit_item'         => __('Edit Make', 'autoparts-pro'),
            'update_item'       => __('Update Make', 'autoparts-pro'),
            'add_new_item'      => __('Add New Make', 'autoparts-pro'),
            'new_item_name'     => __('New Make', 'autoparts-pro'),
            'menu_name'         => __('Makes', 'autoparts-pro'),
        ];
        
        register_taxonomy('vehicle_make', ['product'], [
            'labels'            => $make_labels,
            'hierarchical'      => false,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'vehicle-make'],
            'show_in_rest'      => true,
            'public'            => true,
        ]);
        
        // Vehicle Model
        $model_labels = [
            'name'              => __('Models', 'autoparts-pro'),
            'singular_name'     => __('Model', 'autoparts-pro'),
            'search_items'      => __('Search Models', 'autoparts-pro'),
            'all_items'         => __('All Models', 'autoparts-pro'),
            'edit_item'         => __('Edit Model', 'autoparts-pro'),
            'update_item'       => __('Update Model', 'autoparts-pro'),
            'add_new_item'      => __('Add New Model', 'autoparts-pro'),
            'new_item_name'     => __('New Model', 'autoparts-pro'),
            'menu_name'         => __('Models', 'autoparts-pro'),
        ];
        
        register_taxonomy('vehicle_model', ['product'], [
            'labels'            => $model_labels,
            'hierarchical'      => false,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'vehicle-model'],
            'show_in_rest'      => true,
            'public'            => true,
        ]);
        
        // Vehicle Engine
        $engine_labels = [
            'name'              => __('Engines', 'autoparts-pro'),
            'singular_name'     => __('Engine', 'autoparts-pro'),
            'search_items'      => __('Search Engines', 'autoparts-pro'),
            'all_items'         => __('All Engines', 'autoparts-pro'),
            'edit_item'         => __('Edit Engine', 'autoparts-pro'),
            'update_item'       => __('Update Engine', 'autoparts-pro'),
            'add_new_item'      => __('Add New Engine', 'autoparts-pro'),
            'new_item_name'     => __('New Engine', 'autoparts-pro'),
            'menu_name'         => __('Engines', 'autoparts-pro'),
        ];
        
        register_taxonomy('vehicle_engine', ['product'], [
            'labels'            => $engine_labels,
            'hierarchical'      => false,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'vehicle-engine'],
            'show_in_rest'      => true,
            'public'            => true,
        ]);
    }
    
    /**
     * Product-specific taxonomies
     */
    public function register_product_taxonomies() {
        // Product Brand (separate from WooCommerce brands)
        $brand_labels = [
            'name'              => __('Brands', 'autoparts-pro'),
            'singular_name'     => __('Brand', 'autoparts-pro'),
            'search_items'      => __('Search Brands', 'autoparts-pro'),
            'all_items'         => __('All Brands', 'autoparts-pro'),
            'edit_item'         => __('Edit Brand', 'autoparts-pro'),
            'update_item'       => __('Update Brand', 'autoparts-pro'),
            'add_new_item'      => __('Add New Brand', 'autoparts-pro'),
            'new_item_name'     => __('New Brand', 'autoparts-pro'),
            'menu_name'         => __('Brands', 'autoparts-pro'),
        ];
        
        register_taxonomy('product_brand', ['product'], [
            'labels'            => $brand_labels,
            'hierarchical'      => false,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'brand'],
            'show_in_rest'      => true,
            'public'            => true,
        ]);
        
        // Part Type
        $part_labels = [
            'name'              => __('Part Types', 'autoparts-pro'),
            'singular_name'     => __('Part Type', 'autoparts-pro'),
            'search_items'      => __('Search Part Types', 'autoparts-pro'),
            'all_items'         => __('All Part Types', 'autoparts-pro'),
            'edit_item'         => __('Edit Part Type', 'autoparts-pro'),
            'update_item'       => __('Update Part Type', 'autoparts-pro'),
            'add_new_item'      => __('Add New Part Type', 'autoparts-pro'),
            'new_item_name'     => __('New Part Type', 'autoparts-pro'),
            'menu_name'         => __('Part Types', 'autoparts-pro'),
        ];
        
        register_taxonomy('part_type', ['product'], [
            'labels'            => $part_labels,
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'part-type'],
            'show_in_rest'      => true,
            'public'            => true,
        ]);
        
        // OEM vs Aftermarket
        $origin_labels = [
            'name'              => __('Origin', 'autoparts-pro'),
            'singular_name'     => __('Origin', 'autoparts-pro'),
            'search_items'      => __('Search Origins', 'autoparts-pro'),
            'all_items'         => __('All Origins', 'autoparts-pro'),
            'menu_name'         => __('Origin', 'autoparts-pro'),
        ];
        
        register_taxonomy('product_origin', ['product'], [
            'labels'            => $origin_labels,
            'hierarchical'      => false,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'origin'],
            'show_in_rest'      => true,
            'public'            => true,
        ]);
    }
    
    /**
     * Documentation taxonomy
     */
    public function register_documentation_taxonomy() {
        $doc_cat_labels = [
            'name'              => __('Document Categories', 'autoparts-pro'),
            'singular_name'     => __('Document Category', 'autoparts-pro'),
            'search_items'      => __('Search Categories', 'autoparts-pro'),
            'all_items'         => __('All Categories', 'autoparts-pro'),
            'edit_item'         => __('Edit Category', 'autoparts-pro'),
            'update_item'       => __('Update Category', 'autoparts-pro'),
            'add_new_item'      => __('Add New Category', 'autoparts-pro'),
            'new_item_name'     => __('New Category', 'autoparts-pro'),
            'menu_name'         => __('Categories', 'autoparts-pro'),
        ];
        
        register_taxonomy('doc_category', ['documentation'], [
            'labels'            => $doc_cat_labels,
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'doc-category'],
            'show_in_rest'      => true,
            'public'            => true,
        ]);
    }
}

new Taxonomy_Registration();
