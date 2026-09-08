<?php
/**
 * AutoParts Pro Theme
 * Premium Automotive Spare Parts WordPress Theme
 * 
 * @package AutoParts_Pro
 * @version 1.0.0
 * @author AutoParts Pro Team
 * @license GPL v2 or later
 * @link https://autopartspro.com
 */

if (!defined('ABSPATH')) {
    exit;
}

define('AUTOPARTS_PRO_VERSION', '1.0.0');
define('AUTOPARTS_PRO_DIR', get_template_directory());
define('AUTOPARTS_PRO_URI', get_template_directory_uri());
define('AUTOPARTS_PRO_INC_DIR', AUTOPARTS_PRO_DIR . '/inc');

// Core Setup
require_once AUTOPARTS_PRO_INC_DIR . '/core/theme-setup.php';
require_once AUTOPARTS_PRO_INC_DIR . '/core/enqueue-scripts.php';
require_once AUTOPARTS_PRO_INC_DIR . '/core/custom-post-types.php';
require_once AUTOPARTS_PRO_INC_DIR . '/core/custom-taxonomies.php';
require_once AUTOPARTS_PRO_INC_DIR . '/core/database-tables.php';
require_once AUTOPARTS_PRO_INC_DIR . '/core/template-functions.php';
require_once AUTOPARTS_PRO_INC_DIR . '/core/template-tags.php';

// Widgets
require_once AUTOPARTS_PRO_INC_DIR . '/widgets/class-product-categories-widget.php';
require_once AUTOPARTS_PRO_INC_DIR . '/widgets/class-vehicle-filter-widget.php';
require_once AUTOPARTS_PRO_INC_DIR . '/widgets/class-brand-logos-widget.php';
require_once AUTOPARTS_PRO_INC_DIR . '/widgets/class-flash-deals-widget.php';
require_once AUTOPARTS_PRO_INC_DIR . '/widgets/class-store-locator-widget.php';
require_once AUTOPARTS_PRO_INC_DIR . '/widgets/class-compatibility-checker-widget.php';
require_once AUTOPARTS_PRO_INC_DIR . '/widgets/class-wishlist-widget.php';

// Customizer
require_once AUTOPARTS_PRO_INC_DIR . '/customizer/customizer-settings.php';
require_once AUTOPARTS_PRO_INC_DIR . '/customizer/customizer-controls.php';
require_once AUTOPARTS_PRO_INC_DIR . '/customizer/customizer-sanitization.php';

// Backoffice (3-Rail Builder)
require_once AUTOPARTS_PRO_INC_DIR . '/backoffice/class-theme-options.php';
require_once AUTOPARTS_PRO_INC_DIR . '/backoffice/class-page-builder.php';
require_once AUTOPARTS_PRO_INC_DIR . '/backoffice/class-module-library.php';
require_once AUTOPARTS_PRO_INC_DIR . '/backoffice/class-section-manager.php';
require_once AUTOPARTS_PRO_INC_DIR . '/backoffice/class-style-inspector.php';
require_once AUTOPARTS_PRO_INC_DIR . '/backoffice/class-state-manager.php';
require_once AUTOPARTS_PRO_INC_DIR . '/backoffice/class-language-switcher.php';
require_once AUTOPARTS_PRO_INC_DIR . '/backoffice/class-accessibility-panel.php';

// REST API
require_once AUTOPARTS_PRO_INC_DIR . '/api/class-rest-api-controller.php';
require_once AUTOPARTS_PRO_INC_DIR . '/api/class-wishlist-endpoint.php';
require_once AUTOPARTS_PRO_INC_DIR . '/api/class-fitment-endpoint.php';
require_once AUTOPARTS_PRO_INC_DIR . '/api/class-chatbot-endpoint.php';
require_once AUTOPARTS_PRO_INC_DIR . '/api/class-form-endpoint.php';
require_once AUTOPARTS_PRO_INC_DIR . '/api/class-builder-endpoint.php';

// WooCommerce Integration
if (class_exists('WooCommerce')) {
    require_once AUTOPARTS_PRO_INC_DIR . '/woocommerce/class-woo-integration.php';
    require_once AUTOPARTS_PRO_INC_DIR . '/woocommerce/class-woo-templates.php';
    require_once AUTOPARTS_PRO_INC_DIR . '/woocommerce/class-woo-compatibility.php';
    require_once AUTOPARTS_PRO_INC_DIR . '/woocommerce/class-woo-wishlist.php';
    require_once AUTOPARTS_PRO_INC_DIR . '/woocommerce/class-woo-ajax.php';
    require_once AUTOPARTS_PRO_INC_DIR . '/woocommerce/class-woo-order-tracking.php';
}

// Helpers
require_once AUTOPARTS_PRO_INC_DIR . '/helpers/helpers.php';
require_once AUTOPARTS_PRO_INC_DIR . '/helpers/spam-protection.php';
require_once AUTOPARTS_PRO_INC_DIR . '/helpers/image-handling.php';
require_once AUTOPARTS_PRO_INC_DIR . '/helpers/cache-helper.php';

// Template Functions
require_once AUTOPARTS_PRO_INC_DIR . '/template-functions/mega-menu.php';
require_once AUTOPARTS_PRO_INC_DIR . '/template-functions/slider-system.php';
require_once AUTOPARTS_PRO_INC_DIR . '/template-functions/hero-3d.php';
require_once AUTOPARTS_PRO_INC_DIR . '/template-functions/compatibility-system.php';
require_once AUTOPARTS_PRO_INC_DIR . '/template-functions/wishlist-functions.php';
require_once AUTOPARTS_PRO_INC_DIR . '/template-functions/chatbot-functions.php';
require_once AUTOPARTS_PRO_INC_DIR . '/template-functions/form-builder.php';
require_once AUTOPARTS_PRO_INC_DIR . '/template-functions/dual-slider.php';
require_once AUTOPARTS_PRO_INC_DIR . '/template-functions/custom-cursor.php';
require_once AUTOPARTS_PRO_INC_DIR . '/template-functions/flash-deals.php';
require_once AUTOPARTS_PRO_INC_DIR . '/template-functions/google-maps.php';
require_once AUTOPARTS_PRO_INC_DIR . '/template-functions/social-sharing.php';
require_once AUTOPARTS_PRO_INC_DIR . '/template-functions/brand-showcase.php';
require_once AUTOPARTS_PRO_INC_DIR . '/template-functions/documentation-hub.php';

// Classes
if (file_exists(AUTOPARTS_PRO_INC_DIR . '/classes/class-vehicle-garage.php')) {
    require_once AUTOPARTS_PRO_INC_DIR . '/classes/class-vehicle-garage.php';
}
if (file_exists(AUTOPARTS_PRO_INC_DIR . '/classes/class-currency-switcher.php')) {
    require_once AUTOPARTS_PRO_INC_DIR . '/classes/class-currency-switcher.php';
}
if (file_exists(AUTOPARTS_PRO_INC_DIR . '/classes/class-order-tracking.php')) {
    require_once AUTOPARTS_PRO_INC_DIR . '/classes/class-order-tracking.php';
}
if (file_exists(AUTOPARTS_PRO_INC_DIR . '/classes/class-walker-nav-menu.php')) {
    require_once AUTOPARTS_PRO_INC_DIR . '/classes/class-walker-nav-menu.php';
}

// Activation
register_activation_hook(__FILE__, 'autoparts_pro_activate');
function autoparts_pro_activate() {
    AutoParts_Pro_Database::create_tables();
    flush_rewrite_rules(true);
}

register_deactivation_hook(__FILE__, 'autoparts_pro_deactivate');
function autoparts_pro_deactivate() {
    flush_rewrite_rules();
}

function autoparts_pro_init_components() {
    if (class_exists('AutoParts_Pro_Database')) {
        AutoParts_Pro_Database::init();
    }
    if (class_exists('AutoParts_Pro_REST_Controller')) {
        new AutoParts_Pro_REST_Controller();
    }
    if (function_exists('autoparts_pro_wishlist_init')) {
        autoparts_pro_wishlist_init();
    }
    if (class_exists('AutoParts_Pro_Vehicle_Garage')) {
        new AutoParts_Pro_Vehicle_Garage();
    }
    if (function_exists('autoparts_pro_chatbot_init')) {
        autoparts_pro_chatbot_init();
    }
    if (function_exists('autoparts_pro_form_builder_init')) {
        autoparts_pro_form_builder_init();
    }
    if (function_exists('autoparts_pro_slider_init')) {
        autoparts_pro_slider_init();
    }
    if (function_exists('autoparts_pro_mega_menu_init')) {
        autoparts_pro_mega_menu_init();
    }
    do_action('autoparts_pro_loaded');
}
add_action('after_setup_theme', 'autoparts_pro_init_components', 11);
