<?php
/**
 * AutoParts Pro Theme Functions
 *
 * @package AutoParts_Pro
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AUTOPARTS_PRO_VERSION', '1.0.0' );
define( 'AUTOPARTS_PRO_DIR', get_template_directory() );
define( 'AUTOPARTS_PRO_URI', get_template_directory_uri() );

/**
 * Autoload theme classes
 */
spl_autoload_register( function( $class ) {
	$prefix = 'AutoParts_Pro_';
	$base_dir = AUTOPARTS_PRO_DIR . '/inc/';
	
	$len = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		return;
	}
	
	$relative_class = substr( $class, $len );
	$file_path = $base_dir . str_replace( '_', '-', strtolower( $relative_class ) ) . '.php';
	
	if ( file_exists( $file_path ) ) {
		require_once $file_path;
	}
} );

/**
 * Core theme setup
 */
require_once AUTOPARTS_PRO_DIR . '/inc/core/theme-setup.php';

/**
 * Enqueue scripts and styles
 */
require_once AUTOPARTS_PRO_DIR . '/inc/core/enqueue-scripts.php';

/**
 * Custom Post Types
 */
require_once AUTOPARTS_PRO_DIR . '/inc/core/custom-post-types.php';

/**
 * Custom Taxonomies
 */
require_once AUTOPARTS_PRO_DIR . '/inc/core/custom-taxonomies.php';

/**
 * Template Functions
 */
require_once AUTOPARTS_PRO_DIR . '/inc/core/template-functions.php';

/**
 * Widgets
 */
require_once AUTOPARTS_PRO_DIR . '/inc/widgets/class-product-categories-widget.php';
require_once AUTOPARTS_PRO_DIR . '/inc/widgets/class-vehicle-filter-widget.php';
require_once AUTOPARTS_PRO_DIR . '/inc/widgets/class-brand-logos-widget.php';
require_once AUTOPARTS_PRO_DIR . '/inc/widgets/class-flash-deals-widget.php';
require_once AUTOPARTS_PRO_DIR . '/inc/widgets/class-store-locator-widget.php';

/**
 * REST API Endpoints
 */
require_once AUTOPARTS_PRO_DIR . '/inc/api/rest-api.php';

/**
 * Backoffice - 3-Rail Builder
 */
require_once AUTOPARTS_PRO_DIR . '/inc/backoffice/class-builder.php';
require_once AUTOPARTS_PRO_DIR . '/inc/backoffice/class-theme-options.php';

/**
 * WooCommerce Integration
 */
if ( class_exists( 'WooCommerce' ) ) {
	require_once AUTOPARTS_PRO_DIR . '/woocommerce/class-woocommerce-integration.php';
}

/**
 * Customizer Settings
 */
require_once AUTOPARTS_PRO_DIR . '/inc/customizer/class-customizer.php';

/**
 * Walker for Navigation Menu with Mega Menu Support
 */
require_once AUTOPARTS_PRO_DIR . '/inc/class-walker-nav-menu.php';

/**
 * Helper Functions
 */
require_once AUTOPARTS_PRO_DIR . '/inc/helpers.php';
