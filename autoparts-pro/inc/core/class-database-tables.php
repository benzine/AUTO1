<?php
/**
 * Database Tables Creation and Management
 * 
 * @package AutoParts_Pro
 * @since 1.0.0
 */

namespace AutoParts_Pro\Core;

defined('ABSPATH') || exit;

class Database_Tables {
    
    private static $tables = [
        'vehicle_garage',
        'wishlist',
        'form_submissions',
        'product_compatibility',
        'page_builder_sections',
        'chatbot_conversations',
    ];
    
    public static function init() {
        add_action('plugins_loaded', [__CLASS__, 'check_tables']);
    }
    
    public static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // Vehicle Garage table - stores user saved vehicles
        $table_garage = $wpdb->prefix . 'autoparts_vehicle_garage';
        $sql_garage = "CREATE TABLE $table_garage (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            vehicle_year int(4) NOT NULL,
            vehicle_make varchar(100) NOT NULL,
            vehicle_model varchar(100) NOT NULL,
            vehicle_engine varchar(100) DEFAULT '',
            vehicle_vin varchar(17) DEFAULT '',
            is_primary tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY is_primary (is_primary)
        ) $charset_collate;";
        dbDelta($sql_garage);
        
        // Wishlist table - stores user wishlists
        $table_wishlist = $wpdb->prefix . 'autoparts_wishlist';
        $sql_wishlist = "CREATE TABLE $table_wishlist (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT NULL,
            session_id varchar(64) DEFAULT '',
            product_id bigint(20) NOT NULL,
            quantity int(11) DEFAULT 1,
            added_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY user_product (user_id, product_id),
            KEY session_id (session_id),
            KEY product_id (product_id)
        ) $charset_collate;";
        dbDelta($sql_wishlist);
        
        // Form submissions table
        $table_forms = $wpdb->prefix . 'autoparts_form_submissions';
        $sql_forms = "CREATE TABLE $table_forms (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            form_id varchar(100) NOT NULL,
            form_name varchar(255) NOT NULL,
            submitter_name varchar(255) DEFAULT '',
            submitter_email varchar(255) NOT NULL,
            submitter_phone varchar(50) DEFAULT '',
            submission_data longtext NOT NULL,
            ip_address varchar(45) DEFAULT '',
            user_agent text DEFAULT '',
            spam_score int(11) DEFAULT 0,
            is_spam tinyint(1) DEFAULT 0,
            status varchar(20) DEFAULT 'new',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY form_id (form_id),
            KEY submitter_email (submitter_email),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";
        dbDelta($sql_forms);
        
        // Product compatibility table
        $table_compat = $wpdb->prefix . 'autoparts_product_compatibility';
        $sql_compat = "CREATE TABLE $table_compat (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            product_id bigint(20) NOT NULL,
            vehicle_year int(4) NOT NULL,
            vehicle_make varchar(100) NOT NULL,
            vehicle_model varchar(100) NOT NULL,
            vehicle_engine varchar(100) DEFAULT '',
            fitment_type varchar(50) DEFAULT 'direct',
            notes text DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY product_id (product_id),
            KEY vehicle_year (vehicle_year),
            KEY vehicle_make (vehicle_make),
            KEY vehicle_model (vehicle_model),
            UNIQUE KEY product_vehicle (product_id, vehicle_year, vehicle_make, vehicle_model, vehicle_engine)
        ) $charset_collate;";
        dbDelta($sql_compat);
        
        // Page builder sections table
        $table_sections = $wpdb->prefix . 'autoparts_pb_sections';
        $sql_sections = "CREATE TABLE $table_sections (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            page_id bigint(20) DEFAULT NULL,
            section_id varchar(64) NOT NULL UNIQUE,
            section_type varchar(100) NOT NULL,
            section_order int(11) DEFAULT 0,
            section_data longtext NOT NULL,
            is_active tinyint(1) DEFAULT 1,
            device_visibility varchar(50) DEFAULT 'all',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY page_id (page_id),
            KEY section_type (section_type),
            KEY section_order (section_order),
            KEY is_active (is_active)
        ) $charset_collate;";
        dbDelta($sql_sections);
        
        // Chatbot conversations table
        $table_chat = $wpdb->prefix . 'autoparts_chatbot_conv';
        $sql_chat = "CREATE TABLE $table_chat (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            session_id varchar(64) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            message_role varchar(20) NOT NULL,
            message_content text NOT NULL,
            vehicle_context json DEFAULT NULL,
            product_context bigint(20) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY session_id (session_id),
            KEY user_id (user_id),
            KEY created_at (created_at)
        ) $charset_collate;";
        dbDelta($sql_chat);
        
        update_option('autoparts_pro_db_version', AUTOPARTS_PRO_VERSION);
    }
    
    public static function check_tables() {
        $db_version = get_option('autoparts_pro_db_version');
        if ($db_version !== AUTOPARTS_PRO_VERSION) {
            self::create_tables();
        }
    }
    
    public static function get_table_name($table) {
        global $wpdb;
        return $wpdb->prefix . 'autoparts_' . $table;
    }
    
    public static function drop_tables() {
        global $wpdb;
        
        foreach (self::$tables as $table) {
            $table_name = self::get_table_name($table);
            $wpdb->query("DROP TABLE IF EXISTS $table_name");
        }
        
        delete_option('autoparts_pro_db_version');
    }
}

Database_Tables::init();
