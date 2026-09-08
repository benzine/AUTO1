<?php
/**
 * AutoParts Pro Theme
 *
 * @package           AutoParts_Pro
 * @author            AutoParts Pro Team
 * @copyright         2024 AutoParts Pro
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       AutoParts Pro
 * Plugin URI:        https://autopartspro.com
 * Description:       Premium Automotive Spare Parts WordPress Theme
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            AutoParts Pro Team
 * Author URI:        https://autopartspro.com
 * Text Domain:       autoparts-pro
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define theme constants
define('AUTOPARTS_PRO_VERSION', '1.0.0');
define('AUTOPARTS_PRO_DIR', get_template_directory());
define('AUTOPARTS_PRO_URI', get_template_directory_uri());
define('AUTOPARTS_PRO_ASSETS', AUTOPARTS_PRO_URI . '/assets/');
define('AUTOPARTS_PRO_INC', AUTOPARTS_PRO_DIR . '/inc/');

/**
 * Autoloader for theme classes
 */
spl_autoload_register(function ($class) {
    $prefix = 'AutoParts_Pro\\';
    $base_dir = AUTOPARTS_PRO_INC;
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', strtolower($relative_class)) . '.class.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

/**
 * Initialize theme components
 */
function autoparts_pro_init() {
    // Load text domain
    load_theme_textdomain('autoparts-pro', AUTOPARTS_PRO_DIR . '/languages');
    
    // Include required files
    require_once AUTOPARTS_PRO_INC . 'core-setup.php';
    require_once AUTOPARTS_PRO_INC . 'enqueue-scripts.php';
    require_once AUTOPARTS_PRO_INC . 'custom-post-types.php';
    require_once AUTOPARTS_PRO_INC . 'custom-taxonomies.php';
    require_once AUTOPARTS_PRO_INC . 'template-functions.php';
    require_once AUTOPARTS_PRO_INC . 'template-tags/template-tags.php';
    require_once AUTOPARTS_PRO_INC . 'widgets/widgets.php';
    require_once AUTOPARTS_PRO_INC . 'customizer/customizer-settings.php';
    
    // Backoffice
    require_once AUTOPARTS_PRO_INC . 'backoffice/theme-options.php';
    require_once AUTOPARTS_PRO_INC . 'backoffice/3rail-builder.php';
    
    // REST API & AJAX
    require_once AUTOPARTS_PRO_INC . 'rest-api.php';
    
    // WooCommerce support
    if (class_exists('WooCommerce')) {
        require_once AUTOPARTS_PRO_INC . 'woocommerce/integration.php';
    }
}
add_action('after_setup_theme', 'autoparts_pro_init');

/**
 * Check if WooCommerce is active
 */
function autoparts_pro_is_woocommerce_active() {
    return class_exists('WooCommerce');
}

/**
 * Get theme option
 */
function autoparts_pro_get_option($option_name, $default = '') {
    $options = get_option('autoparts_pro_options', array());
    return isset($options[$option_name]) ? $options[$option_name] : $default;
}

/**
 * Update theme option
 */
function autoparts_pro_update_option($option_name, $value) {
    $options = get_option('autoparts_pro_options', array());
    $options[$option_name] = $value;
    update_option('autoparts_pro_options', $options);
}
