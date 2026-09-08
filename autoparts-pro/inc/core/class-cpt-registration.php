<?php
/**
 * Custom Post Types Registration
 * 
 * @package AutoParts_Pro
 * @since 1.0.0
 */

namespace AutoParts_Pro\Core;

defined('ABSPATH') || exit;

class CPT_Registration {
    
    public function __construct() {
        add_action('init', [$this, 'register_vehicle_cpt']);
        add_action('init', [$this, 'register_documentation_cpt']);
        add_action('init', [$this, 'register_store_location_cpt']);
        add_action('init', [$this, 'register_form_submission_cpt']);
    }
    
    /**
     * Vehicle Garage CPT - For saved user vehicles
     */
    public function register_vehicle_cpt() {
        $labels = [
            'name'               => __('Vehicles', 'autoparts-pro'),
            'singular_name'      => __('Vehicle', 'autoparts-pro'),
            'menu_name'          => __('Vehicle Garage', 'autoparts-pro'),
            'add_new'            => __('Add Vehicle', 'autoparts-pro'),
            'add_new_item'       => __('Add New Vehicle', 'autoparts-pro'),
            'edit_item'          => __('Edit Vehicle', 'autoparts-pro'),
            'new_item'           => __('New Vehicle', 'autoparts-pro'),
            'view_item'          => __('View Vehicle', 'autoparts-pro'),
            'search_items'       => __('Search Vehicles', 'autoparts-pro'),
            'not_found'          => __('No vehicles found', 'autoparts-pro'),
            'not_found_in_trash' => __('No vehicles found in trash', 'autoparts-pro'),
        ];
        
        $args = [
            'labels'             => $labels,
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => false,
            'rewrite'            => false,
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'          => 'dashicons-car',
            'supports'           => ['title'],
            'show_in_rest'       => true,
        ];
        
        register_post_type('vehicle', $args);
        
        // Add meta boxes for vehicle details
        add_action('add_meta_boxes_vehicle', [$this, 'vehicle_meta_boxes']);
        add_action('save_post_vehicle', [$this, 'save_vehicle_meta'], 10, 2);
    }
    
    public function vehicle_meta_boxes() {
        add_meta_box(
            'vehicle_details',
            __('Vehicle Details', 'autoparts-pro'),
            [$this, 'vehicle_details_callback'],
            'vehicle',
            'normal',
            'high'
        );
    }
    
    public function vehicle_details_callback($post) {
        wp_nonce_field('vehicle_details_nonce', 'vehicle_details_nonce');
        
        $year = get_post_meta($post->ID, '_vehicle_year', true);
        $make = get_post_meta($post->ID, '_vehicle_make', true);
        $model = get_post_meta($post->ID, '_vehicle_model', true);
        $engine = get_post_meta($post->ID, '_vehicle_engine', true);
        $vin = get_post_meta($post->ID, '_vehicle_vin', true);
        ?>
        <table class="form-table">
            <tr>
                <th><label for="vehicle_year"><?php _e('Year', 'autoparts-pro'); ?></label></th>
                <td><input type="number" id="vehicle_year" name="vehicle_year" value="<?php echo esc_attr($year); ?>" min="1900" max="<?php echo date('Y') + 1; ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="vehicle_make"><?php _e('Make', 'autoparts-pro'); ?></label></th>
                <td><input type="text" id="vehicle_make" name="vehicle_make" value="<?php echo esc_attr($make); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="vehicle_model"><?php _e('Model', 'autoparts-pro'); ?></label></th>
                <td><input type="text" id="vehicle_model" name="vehicle_model" value="<?php echo esc_attr($model); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="vehicle_engine"><?php _e('Engine', 'autoparts-pro'); ?></label></th>
                <td><input type="text" id="vehicle_engine" name="vehicle_engine" value="<?php echo esc_attr($engine); ?>" class="regular-text" placeholder="e.g., V6 3.5L"></td>
            </tr>
            <tr>
                <th><label for="vehicle_vin"><?php _e('VIN (Optional)', 'autoparts-pro'); ?></label></th>
                <td><input type="text" id="vehicle_vin" name="vehicle_vin" value="<?php echo esc_attr($vin); ?>" class="regular-text" maxlength="17"></td>
            </tr>
        </table>
        <?php
    }
    
    public function save_vehicle_meta($post_id, $post) {
        if (!isset($_POST['vehicle_details_nonce']) || !wp_verify_nonce($_POST['vehicle_details_nonce'], 'vehicle_details_nonce')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;
        
        $fields = ['vehicle_year', 'vehicle_make', 'vehicle_model', 'vehicle_engine', 'vehicle_vin'];
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . str_replace('vehicle_', '', $field), sanitize_text_field($_POST[$field]));
            }
        }
    }
    
    /**
     * Documentation CPT - Installation guides, tutorials
     */
    public function register_documentation_cpt() {
        $labels = [
            'name'               => __('Documentation', 'autoparts-pro'),
            'singular_name'      => __('Document', 'autoparts-pro'),
            'menu_name'          => __('Documentation Hub', 'autoparts-pro'),
            'add_new'            => __('Add Document', 'autoparts-pro'),
            'add_new_item'       => __('Add New Document', 'autoparts-pro'),
            'edit_item'          => __('Edit Document', 'autoparts-pro'),
            'all_items'          => __('All Documentation', 'autoparts-pro'),
        ];
        
        $args = [
            'labels'             => $labels,
            'public'             => true,
            'show_in_menu'       => true,
            'menu_position'      => 21,
            'menu_icon'          => 'dashicons-media-document',
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
            'has_archive'        => true,
            'rewrite'            => ['slug' => 'documentation'],
            'show_in_rest'       => true,
            'taxonomies'         => ['doc_category'],
        ];
        
        register_post_type('documentation', $args);
    }
    
    /**
     * Store Location CPT - For store locator
     */
    public function register_store_location_cpt() {
        $labels = [
            'name'               => __('Store Locations', 'autoparts-pro'),
            'singular_name'      => __('Store Location', 'autoparts-pro'),
            'menu_name'          => __('Store Locations', 'autoparts-pro'),
            'add_new'            => __('Add Store', 'autoparts-pro'),
            'add_new_item'       => __('Add New Store', 'autoparts-pro'),
            'edit_item'          => __('Edit Store', 'autoparts-pro'),
        ];
        
        $args = [
            'labels'             => $labels,
            'public'             => true,
            'show_in_menu'       => true,
            'menu_position'      => 22,
            'menu_icon'          => 'dashicons-location',
            'supports'           => ['title', 'editor', 'thumbnail'],
            'has_archive'        => false,
            'rewrite'            => ['slug' => 'stores'],
            'show_in_rest'       => true,
        ];
        
        register_post_type('store_location', $args);
        
        add_action('add_meta_boxes_store_location', [$this, 'store_meta_boxes']);
        add_action('save_post_store_location', [$this, 'save_store_meta'], 10, 2);
    }
    
    public function store_meta_boxes() {
        add_meta_box(
            'store_details',
            __('Store Details', 'autoparts-pro'),
            [$this, 'store_details_callback'],
            'store_location',
            'normal',
            'high'
        );
    }
    
    public function store_details_callback($post) {
        wp_nonce_field('store_details_nonce', 'store_details_nonce');
        
        $address = get_post_meta($post->ID, '_store_address', true);
        $phone = get_post_meta($post->ID, '_store_phone', true);
        $email = get_post_meta($post->ID, '_store_email', true);
        $latitude = get_post_meta($post->ID, '_store_lat', true);
        $longitude = get_post_meta($post->ID, '_store_lng', true);
        $hours = get_post_meta($post->ID, '_store_hours', true);
        ?>
        <table class="form-table">
            <tr>
                <th><label for="store_address"><?php _e('Address', 'autoparts-pro'); ?></label></th>
                <td><textarea id="store_address" name="store_address" rows="3" class="large-text"><?php echo esc_textarea($address); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="store_phone"><?php _e('Phone', 'autoparts-pro'); ?></label></th>
                <td><input type="tel" id="store_phone" name="store_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="store_email"><?php _e('Email', 'autoparts-pro'); ?></label></th>
                <td><input type="email" id="store_email" name="store_email" value="<?php echo esc_attr($email); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="store_lat"><?php _e('Latitude', 'autoparts-pro'); ?></label></th>
                <td><input type="text" id="store_lat" name="store_lat" value="<?php echo esc_attr($latitude); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="store_lng"><?php _e('Longitude', 'autoparts-pro'); ?></label></th>
                <td><input type="text" id="store_lng" name="store_lng" value="<?php echo esc_attr($longitude); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="store_hours"><?php _e('Opening Hours', 'autoparts-pro'); ?></label></th>
                <td><textarea id="store_hours" name="store_hours" rows="4" class="large-text" placeholder="Monday-Friday: 9AM-6PM&#10;Saturday: 10AM-4PM&#10;Sunday: Closed"><?php echo esc_textarea($hours); ?></textarea></td>
            </tr>
        </table>
        <?php
    }
    
    public function save_store_meta($post_id, $post) {
        if (!isset($_POST['store_details_nonce']) || !wp_verify_nonce($_POST['store_details_nonce'], 'store_details_nonce')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;
        
        $fields = [
            'store_address' => '_store_address',
            'store_phone'   => '_store_phone',
            'store_email'   => '_store_email',
            'store_lat'     => '_store_lat',
            'store_lng'     => '_store_lng',
            'store_hours'   => '_store_hours',
        ];
        
        foreach ($fields as $field => $meta_key) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$field]));
            }
        }
    }
    
    /**
     * Form Submission CPT - For contact form submissions
     */
    public function register_form_submission_cpt() {
        $labels = [
            'name'               => __('Form Submissions', 'autoparts-pro'),
            'singular_name'      => __('Submission', 'autoparts-pro'),
            'menu_name'          => __('Form Submissions', 'autoparts-pro'),
            'all_items'          => __('All Submissions', 'autoparts-pro'),
            'view_item'          => __('View Submission', 'autoparts-pro'),
            'search_items'       => __('Search Submissions', 'autoparts-pro'),
        ];
        
        $args = [
            'labels'             => $labels,
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_position'      => 23,
            'menu_icon'          => 'dashicons-feedback',
            'supports'           => ['title'],
            'capability_type'    => 'post',
            'capabilities'       => [
                'edit_post'          => 'read',
                'read_post'          => 'read',
                'delete_post'        => 'delete_posts',
                'edit_posts'         => 'read',
                'publish_posts'      => 'read',
            ],
            'map_meta_cap'       => true,
        ];
        
        register_post_type('form_submission', $args);
    }
}

new CPT_Registration();
