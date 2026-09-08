<?php
/**
 * Core Theme Setup
 *
 * @package AutoParts_Pro
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function autoparts_pro_setup() {
	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Let WordPress manage the document title.
	add_theme_support( 'title-tag' );

	// Enable support for Post Thumbnails on posts and pages.
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'autoparts-card', 400, 300, true );
	add_image_size( 'autoparts-large', 800, 600, true );
	add_image_size( 'autoparts-hero', 1920, 1080, true );

	// Register nav menus.
	register_nav_menus(
		array(
			'primary' => esc_html__( 'Primary Menu', 'autoparts-pro' ),
			'footer'  => esc_html__( 'Footer Menu', 'autoparts-pro' ),
			'mobile'  => esc_html__( 'Mobile Menu', 'autoparts-pro' ),
		)
	);

	// Switch default core markup for search form, comment form, and comments to output valid HTML5.
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	// Add support for core custom logo feature.
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 100,
			'width'       => 400,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	// Add support for custom background.
	add_theme_support( 'custom-background' );

	// Add support for custom header.
	add_theme_support(
		'custom-header',
		array(
			'default-image'      => '',
			'default-text-color' => 'FFFFFF',
			'width'              => 1920,
			'height'             => 400,
			'flex-height'        => true,
			'flex-width'         => true,
		)
	);

	// Add WooCommerce support.
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	// Add Gutenberg alignment support.
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );

	// Add custom color palette for Gutenberg.
	add_theme_support(
		'editor-color-palette',
		array(
			array(
				'name'  => esc_html__( 'Racing Red', 'autoparts-pro' ),
				'slug'  => 'primary',
				'color' => '#DC2626',
			),
			array(
				'name'  => esc_html__( 'Obsidian Black', 'autoparts-pro' ),
				'slug'  => 'dark',
				'color' => '#0A0A0A',
			),
			array(
				'name'  => esc_html__( 'Charcoal Grey', 'autoparts-pro' ),
				'slug'  => 'mid-charcoal',
				'color' => '#2D2D2D',
			),
			array(
				'name'  => esc_html__( 'Steel Grey', 'autoparts-pro' ),
				'slug'  => 'mid-steel',
				'color' => '#6B7280',
			),
			array(
				'name'  => esc_html__( 'Pure White', 'autoparts-pro' ),
				'slug'  => 'white',
				'color' => '#FFFFFF',
			),
			array(
				'name'  => esc_html__( 'Burnt Orange', 'autoparts-pro' ),
				'slug'  => 'accent',
				'color' => '#EA580C',
			),
		)
	);

	// Load text domain.
	load_theme_textdomain( 'autoparts-pro', AUTOPARTS_PRO_DIR . '/languages' );
}
add_action( 'after_setup_theme', 'autoparts_pro_setup' );

/**
 * Set the content width in pixels.
 */
function autoparts_pro_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'autoparts_pro_content_width', 1400 );
}
add_action( 'after_setup_theme', 'autoparts_pro_content_width', 0 );

/**
 * Register widget areas.
 */
function autoparts_pro_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'autoparts-pro' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'autoparts-pro' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Shop Sidebar', 'autoparts-pro' ),
			'id'            => 'shop-sidebar',
			'description'   => esc_html__( 'Widgets for shop pages.', 'autoparts-pro' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer 1', 'autoparts-pro' ),
			'id'            => 'footer-1',
			'description'   => esc_html__( 'First footer widget area.', 'autoparts-pro' ),
			'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer 2', 'autoparts-pro' ),
			'id'            => 'footer-2',
			'description'   => esc_html__( 'Second footer widget area.', 'autoparts-pro' ),
			'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer 3', 'autoparts-pro' ),
			'id'            => 'footer-3',
			'description'   => esc_html__( 'Third footer widget area.', 'autoparts-pro' ),
			'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer 4', 'autoparts-pro' ),
			'id'            => 'footer-4',
			'description'   => esc_html__( 'Fourth footer widget area.', 'autoparts-pro' ),
			'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Header Right', 'autoparts-pro' ),
			'id'            => 'header-right',
			'description'   => esc_html__( 'Right side of header.', 'autoparts-pro' ),
			'before_widget' => '<div id="%1$s" class="header-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Product Detail Sidebar', 'autoparts-pro' ),
			'id'            => 'product-sidebar',
			'description'   => esc_html__( 'Sidebar for product detail pages.', 'autoparts-pro' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Blog Sidebar', 'autoparts-pro' ),
			'id'            => 'blog-sidebar',
			'description'   => esc_html__( 'Sidebar for blog posts.', 'autoparts-pro' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		)
	);
}
add_action( 'widgets_init', 'autoparts_pro_widgets_init' );

/**
 * Add preconnect for Google Fonts.
 */
function autoparts_pro_resource_hints() {
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
}
add_action( 'wp_head', 'autoparts_pro_resource_hints', 1 );

/**
 * Add pingback header.
 */
function autoparts_pro_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'autoparts_pro_pingback_header' );

/**
 * Remove unnecessary WordPress features for performance.
 */
function autoparts_pro_cleanup() {
	// Remove emoji scripts.
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );

	// Remove WP generator tag.
	remove_action( 'wp_head', 'wp_generator' );

	// Remove wlwmanifest link.
	remove_action( 'wp_head', 'wlwmanifest_link' );

	// Remove shortlink.
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );

	// Remove RSD link.
	remove_action( 'wp_head', 'rsd_link' );
}
add_action( 'after_setup_theme', 'autoparts_pro_cleanup' );

/**
 * Filter the except length to 20 words.
 */
function autoparts_pro_excerpt_length( $length ) {
	if ( is_admin() ) {
		return $length;
	}
	return 20;
}
add_filter( 'excerpt_length', 'autoparts_pro_excerpt_length', 999 );

/**
 * Add custom excerpt more.
 */
function autoparts_pro_excerpt_more( $more ) {
	if ( is_admin() ) {
		return $more;
	}
	return '&hellip;';
}
add_filter( 'excerpt_more', 'autoparts_pro_excerpt_more' );
