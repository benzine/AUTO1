<?php
/**
 * Custom Database Tables
 */
if (!defined('ABSPATH')) exit;

class AutoParts_Pro_Database {
    
    public static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        // Vehicle Garage table
        $table_garage = $wpdb->prefix . 'autoparts_garage';
        $sql_garage = "CREATE TABLE $table_garage (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            vehicle_year int(4) NOT NULL,
            vehicle_make varchar(100) NOT NULL,
            vehicle_model varchar(100) NOT NULL,
            vehicle_engine varchar(100) DEFAULT '',
            is_primary tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        
        // Wishlist table
        $table_wishlist = $wpdb->prefix . 'autoparts_wishlist';
        $sql_wishlist = "CREATE TABLE $table_wishlist (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT 0,
            session_id varchar(64) DEFAULT '',
            product_id bigint(20) NOT NULL,
            added_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY session_id (session_id),
            KEY product_id (product_id)
        ) $charset_collate;";
        
        // Form submissions table
        $table_forms = $wpdb->prefix . 'autoparts_form_submissions';
        $sql_forms = "CREATE TABLE $table_forms (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            form_name varchar(100) NOT NULL,
            form_data longtext NOT NULL,
            ip_address varchar(45) DEFAULT '',
            user_agent text,
            spam_score int(1) DEFAULT 0,
            status varchar(20) DEFAULT 'new',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // Compatibility table
        $table_compat = $wpdb->prefix . 'autoparts_compatibility';
        $sql_compat = "CREATE TABLE $table_compat (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            product_id bigint(20) NOT NULL,
            year_from int(4) NOT NULL,
            year_to int(4) DEFAULT 0,
            make varchar(100) NOT NULL,
            model varchar(100) NOT NULL,
            engine varchar(100) DEFAULT '',
            notes text,
            PRIMARY KEY (id),
            KEY product_id (product_id),
            KEY make_model (make(10), model(10))
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_garage);
        dbDelta($sql_wishlist);
        dbDelta($sql_forms);
        dbDelta($sql_compat);
        
        update_option('autoparts_pro_db_version', AUTOPARTS_PRO_VERSION);
    }
    
    public static function init() {
        if (get_option('autoparts_pro_db_version') !== AUTOPARTS_PRO_VERSION) {
            self::create_tables();
        }
    }
    
    public static function get_garage($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'autoparts_garage';
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE user_id = %d ORDER BY is_primary DESC, created_at DESC", $user_id));
    }
    
    public static function add_to_garage($user_id, $vehicle) {
        global $wpdb;
        $table = $wpdb->prefix . 'autoparts_garage';
        return $wpdb->insert($table, array(
            'user_id' => $user_id,
            'vehicle_year' => $vehicle['year'],
            'vehicle_make' => $vehicle['make'],
            'vehicle_model' => $vehicle['model'],
            'vehicle_engine' => isset($vehicle['engine']) ? $vehicle['engine'] : '',
            'is_primary' => 0
        ));
    }
    
    public static function get_wishlist_items($identifier, $is_user = true) {
        global $wpdb;
        $table = $wpdb->prefix . 'autoparts_wishlist';
        if ($is_user) {
            return $wpdb->get_results($wpdb->prepare("SELECT w.*, p.post_title, p.guid as product_url FROM $table w LEFT JOIN {$wpdb->posts} p ON w.product_id = p.ID WHERE w.user_id = %d ORDER BY w.added_at DESC", $identifier));
        } else {
            return $wpdb->get_results($wpdb->prepare("SELECT w.*, p.post_title, p.guid as product_url FROM $table w LEFT JOIN {$wpdb->posts} p ON w.product_id = p.ID WHERE w.session_id = %s ORDER BY w.added_at DESC", $identifier));
        }
    }
    
    public static function add_to_wishlist($product_id, $user_id = 0, $session_id = '') {
        global $wpdb;
        $table = $wpdb->prefix . 'autoparts_wishlist';
        return $wpdb->insert($table, array(
            'user_id' => $user_id,
            'session_id' => $session_id,
            'product_id' => $product_id
        ));
    }
    
    public static function remove_from_wishlist($product_id, $user_id = 0, $session_id = '') {
        global $wpdb;
        $table = $wpdb->prefix . 'autoparts_wishlist';
        if ($user_id) {
            return $wpdb->delete($table, array('user_id' => $user_id, 'product_id' => $product_id));
        } else {
            return $wpdb->delete($table, array('session_id' => $session_id, 'product_id' => $product_id));
        }
    }
    
    public static function save_form_submission($form_name, $form_data, $spam_score = 0) {
        global $wpdb;
        $table = $wpdb->prefix . 'autoparts_form_submissions';
        return $wpdb->insert($table, array(
            'form_name' => $form_name,
            'form_data' => json_encode($form_data),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'spam_score' => $spam_score
        ));
    }
}
