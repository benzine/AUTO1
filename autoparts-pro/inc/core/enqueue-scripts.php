<?php
/**
 * Enqueue Scripts and Styles
 *
 * @package AutoParts_Pro
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue all theme scripts and styles.
 */
function autoparts_pro_scripts() {
	// Google Fonts.
	wp_enqueue_style(
		'autoparts-google-fonts',
		'https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap',
		array(),
		null
	);

	// Main stylesheet.
	wp_enqueue_style(
		'autoparts-style',
		get_stylesheet_uri(),
		array(),
		AUTOPARTS_PRO_VERSION
	);

	// Three.js for 3D rendering.
	wp_enqueue_script(
		'three-js',
		'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js',
		array(),
		'r128',
		true
	);

	// GSAP for animations.
	wp_enqueue_script(
		'gsap',
		'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js',
		array(),
		'3.12.2',
		true
	);

	// GSAP ScrollTrigger.
	wp_enqueue_script(
		'gsap-scrolltrigger',
		'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js',
		array( 'gsap' ),
		'3.12.2',
		true
	);

	// Theme main JavaScript.
	wp_enqueue_script(
		'autoparts-main',
		AUTOPARTS_PRO_URI . '/assets/js/main.js',
		array( 'jquery', 'gsap', 'gsap-scrolltrigger' ),
		AUTOPARTS_PRO_VERSION,
		true
	);

	// Localize script with AJAX URL and nonces.
	wp_localize_script(
		'autoparts-main',
		'autopartsConfig',
		array(
			'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
			'restUrl'          => rest_url( 'autoparts-pro/v1' ),
			'nonce'            => wp_create_nonce( 'wp_rest' ),
			'wishlistNonce'    => wp_create_nonce( 'autoparts_wishlist_nonce' ),
			'chatbotNonce'     => wp_create_nonce( 'autoparts_chatbot_nonce' ),
			'fitmentNonce'     => wp_create_nonce( 'autoparts_fitment_nonce' ),
			'siteUrl'          => home_url(),
			'themeUrl'         => AUTOPARTS_PRO_URI,
			'isUserLoggedIn'   => is_user_logged_in(),
			'userId'           => get_current_user_id(),
			'i18n'             => array(
				'addToCart'       => __( 'Add to Cart', 'autoparts-pro' ),
				'addedToCart'     => __( 'Added!', 'autoparts-pro' ),
				'addToWishlist'   => __( 'Add to Wishlist', 'autoparts-pro' ),
				'removeWishlist'  => __( 'Remove from Wishlist', 'autoparts-pro' ),
				'searchPlaceholder' => __( 'Search parts...', 'autoparts-pro' ),
				'vehicleSelect'   => __( 'Select your vehicle', 'autoparts-pro' ),
				'fitsVehicle'     => __( 'Fits your vehicle', 'autoparts-pro' ),
				'notFitsVehicle'  => __( 'Does not fit', 'autoparts-pro' ),
			),
		)
	);

	// Comment reply script.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'autoparts_pro_scripts' );

/**
 * Enqueue admin scripts and styles.
 */
function autoparts_pro_admin_scripts( $hook ) {
	// Only load on theme options and builder pages.
	$allowed_pages = array(
		'appearance_page_autoparts-options',
		'appearance_page_autoparts-builder',
	);

	if ( ! in_array( $hook, $allowed_pages, true ) ) {
		return;
	}

	// Admin CSS.
	wp_enqueue_style(
		'autoparts-admin',
		AUTOPARTS_PRO_URI . '/assets/css/admin.css',
		array(),
		AUTOPARTS_PRO_VERSION
	);

	// Admin JS.
	wp_enqueue_script(
		'autoparts-admin',
		AUTOPARTS_PRO_URI . '/assets/js/admin.js',
		array( 'jquery', 'wp-color-picker' ),
		AUTOPARTS_PRO_VERSION,
		true
	);

	// WordPress media uploader.
	wp_enqueue_media();

	// Localize with AJAX.
	wp_localize_script(
		'autoparts-admin',
		'autopartsAdminConfig',
		array(
			'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
			'nonce'         => wp_create_nonce( 'autoparts_admin_nonce' ),
			'restUrl'       => rest_url( 'autoparts-pro/v1' ),
			'restNonce'     => wp_create_nonce( 'wp_rest' ),
			'siteUrl'       => home_url(),
			'themeUrl'      => AUTOPARTS_PRO_URI,
			'builderPages'  => autoparts_get_builder_pages(),
			'i18n'          => array(
				'saveSuccess'     => __( 'Settings saved successfully!', 'autoparts-pro' ),
				'saveError'       => __( 'Error saving settings.', 'autoparts-pro' ),
				'deleteConfirm'   => __( 'Are you sure you want to delete this?', 'autoparts-pro' ),
				'uploadImage'     => __( 'Upload Image', 'autoparts-pro' ),
				'useImage'        => __( 'Use Image', 'autoparts-pro' ),
			),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'autoparts_pro_admin_scripts' );

/**
 * Add preload links for critical assets.
 */
function autoparts_pro_preload_assets() {
	// Preload hero font.
	echo '<link rel="preload" href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@700&display=swap" as="style">';
}
add_action( 'wp_head', 'autoparts_pro_preload_assets', 1 );
