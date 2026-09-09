<?php
/**
 * AutoParts Pro Theme Options Panel
 * 
 * 3-Rail Headless Backoffice - Theme Configuration
 * Provides comprehensive settings for logos, colors, 3D hero, chatbot, maps, and UI elements.
 *
 * @package AutoParts_Pro
 * @since 1.0.0
 */

namespace AutoParts_Pro\Backoffice;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Theme_Options {
	
	/**
	 * Option group name
	 */
	const OPTION_GROUP = 'autoparts_pro_options';
	
	/**
	 * Option name
	 */
	const OPTION_NAME = 'autoparts_pro_theme_options';
	
	/**
	 * Initialize
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
	}
	
	/**
	 * Add admin menu
	 */
	public static function add_admin_menu() {
		add_theme_page(
			__( 'Theme Options', 'autoparts-pro' ),
			__( 'Theme Options', 'autoparts-pro' ),
			'manage_options',
			'autoparts-pro-options',
			array( __CLASS__, 'render_options_page' )
		);
		
		// Add submenu for 3-Rail Builder
		add_theme_page(
			__( 'Page Builder', 'autoparts-pro' ),
			__( 'Page Builder', 'autoparts-pro' ),
			'manage_options',
			'autoparts-pro-builder',
			array( __CLASS__, 'render_builder_page' )
		);
	}
	
	/**
	 * Register settings
	 */
	public static function register_settings() {
		register_setting(
			self::OPTION_GROUP,
			self::OPTION_NAME,
			array(
				'type'              => 'object',
				'sanitize_callback' => array( __CLASS__, 'sanitize_options' ),
				'default'           => self::get_default_options(),
			)
		);
		
		// Logo Settings Section
		add_settings_section(
			'autoparts_logo_section',
			__( 'Logo Settings', 'autoparts-pro' ),
			array( __CLASS__, 'render_logo_section_desc' ),
			'autoparts-pro-options'
		);
		
		add_settings_field(
			'logo_light',
			__( 'Light Mode Logo', 'autoparts-pro' ),
			array( __CLASS__, 'render_logo_field' ),
			'autoparts-pro-options',
			'autoparts_logo_section',
			array( 'field' => 'logo_light', 'mode' => 'light' )
		);
		
		add_settings_field(
			'logo_dark',
			__( 'Dark Mode Logo', 'autoparts-pro' ),
			array( __CLASS__, 'render_logo_field' ),
			'autoparts-pro-options',
			'autoparts_logo_section',
			array( 'field' => 'logo_dark', 'mode' => 'dark' )
		);
		
		add_settings_field(
			'logo_sticky',
			__( 'Sticky Header Logo', 'autoparts-pro' ),
			array( __CLASS__, 'render_logo_field' ),
			'autoparts-pro-options',
			'autoparts_logo_section',
			array( 'field' => 'logo_sticky', 'mode' => 'sticky' )
		);
		
		add_settings_field(
			'logo_mobile',
			__( 'Mobile Logo', 'autoparts-pro' ),
			array( __CLASS__, 'render_logo_field' ),
			'autoparts-pro-options',
			'autoparts_logo_section',
			array( 'field' => 'logo_mobile', 'mode' => 'mobile' )
		);
		
		add_settings_field(
			'logo_type',
			__( 'Logo Type', 'autoparts-pro' ),
			array( __CLASS__, 'render_logo_type_field' ),
			'autoparts-pro-options',
			'autoparts_logo_section',
			array( 'field' => 'logo_type' )
		);
		
		add_settings_field(
			'logo_text',
			__( 'Text Logo', 'autoparts-pro' ),
			array( __CLASS__, 'render_text_field' ),
			'autoparts-pro-options',
			'autoparts_logo_section',
			array( 'field' => 'logo_text', 'placeholder' => 'AutoParts Pro' )
		);
		
		// Color Settings Section
		add_settings_section(
			'autoparts_colors_section',
			__( 'Color Settings', 'autoparts-pro' ),
			array( __CLASS__, 'render_colors_section_desc' ),
			'autoparts-pro-options'
		);
		
		$colors = array(
			'primary_color'       => __( 'Primary Color (Racing Red)', 'autoparts-pro' ),
			'dark_bg_color'       => __( 'Dark Background (Obsidian Black)', 'autoparts-pro' ),
			'mid_gray_color'      => __( 'Mid Gray (Charcoal)', 'autoparts-pro' ),
			'steel_gray_color'    => __( 'Steel Gray', 'autoparts-pro' ),
			'accent_color'        => __( 'Accent Color (Burnt Orange)', 'autoparts-pro' ),
			'light_mode_bg'       => __( 'Light Mode Background', 'autoparts-pro' ),
			'light_mode_text'     => __( 'Light Mode Text', 'autoparts-pro' ),
			'dark_mode_text'      => __( 'Dark Mode Text', 'autoparts-pro' ),
		);
		
		foreach ( $colors as $key => $label ) {
			add_settings_field(
				$key,
				$label,
				array( __CLASS__, 'render_color_field' ),
				'autoparts-pro-options',
				'autoparts_colors_section',
				array( 'field' => $key )
			);
		}
		
		// Typography Section
		add_settings_section(
			'autoparts_typography_section',
			__( 'Typography', 'autoparts-pro' ),
			array( __CLASS__, 'render_typography_section_desc' ),
			'autoparts-pro-options'
		);
		
		add_settings_field(
			'heading_font',
			__( 'Heading Font', 'autoparts-pro' ),
			array( __CLASS__, 'render_select_field' ),
			'autoparts-pro-options',
			'autoparts_typography_section',
			array(
				'field'   => 'heading_font',
				'options' => array(
					'rajdhani'  => 'Rajdhani',
					'oswald'    => 'Oswald',
					'roboto'    => 'Roboto',
					'montserrat' => 'Montserrat',
				),
			)
		);
		
		add_settings_field(
			'body_font',
			__( 'Body Font', 'autoparts-pro' ),
			array( __CLASS__, 'render_select_field' ),
			'autoparts-pro-options',
			'autoparts_typography_section',
			array(
				'field'   => 'body_font',
				'options' => array(
					'inter'       => 'Inter',
					'roboto'      => 'Roboto',
					'open-sans'   => 'Open Sans',
					'lato'        => 'Lato',
				),
			)
		);
		
		add_settings_field(
			'numbers_font',
			__( 'Numbers/Prices Font', 'autoparts-pro' ),
			array( __CLASS__, 'render_select_field' ),
			'autoparts-pro-options',
			'autoparts_typography_section',
			array(
				'field'   => 'numbers_font',
				'options' => array(
					'jetbrains-mono' => 'JetBrains Mono',
					'fira-code'      => 'Fira Code',
					'roboto-mono'    => 'Roboto Mono',
				),
			)
		);
		
		// 3D Hero Section
		add_settings_section(
			'autoparts_hero_section',
			__( '3D Exploded View Hero', 'autoparts-pro' ),
			array( __CLASS__, 'render_hero_section_desc' ),
			'autoparts-pro-options'
		);
		
		add_settings_field(
			'hero_enabled',
			__( 'Enable 3D Hero', 'autoparts-pro' ),
			array( __CLASS__, 'render_checkbox_field' ),
			'autoparts-pro-options',
			'autoparts_hero_section',
			array( 'field' => 'hero_enabled' )
		);
		
		add_settings_field(
			'hero_model',
			__( '3D Model File (.glb/.gltf)', 'autoparts-pro' ),
			array( __CLASS__, 'render_file_upload_field' ),
			'autoparts-pro-options',
			'autoparts_hero_section',
			array( 'field' => 'hero_model', 'mime_type' => 'model/gltf-binary' )
		);
		
		add_settings_field(
			'hero_scroll_distance',
			__( 'Scroll Distance for Full Explosion (px)', 'autoparts-pro' ),
			array( __CLASS__, 'render_number_field' ),
			'autoparts-pro-options',
			'autoparts_hero_section',
			array( 'field' => 'hero_scroll_distance', 'default' => 800 )
		);
		
		add_settings_field(
			'hero_labels',
			__( 'Component Labels (JSON)', 'autoparts-pro' ),
			array( __CLASS__, 'render_textarea_field' ),
			'autoparts-pro-options',
			'autoparts_hero_section',
			array(
				'field'       => 'hero_labels',
				'placeholder' => '{"piston": "Piston Assembly", "crankshaft": "Crankshaft"}',
			)
		);
		
		// Chatbot Section
		add_settings_section(
			'autoparts_chatbot_section',
			__( 'AI Chatbot Settings', 'autoparts-pro' ),
			array( __CLASS__, 'render_chatbot_section_desc' ),
			'autoparts-pro-options'
		);
		
		add_settings_field(
			'chatbot_enabled',
			__( 'Enable Chatbot', 'autoparts-pro' ),
			array( __CLASS__, 'render_checkbox_field' ),
			'autoparts-pro-options',
			'autoparts_chatbot_section',
			array( 'field' => 'chatbot_enabled' )
		);
		
		add_settings_field(
			'chatbot_api_key',
			__( 'AI API Key', 'autoparts-pro' ),
			array( __CLASS__, 'render_text_field' ),
			'autoparts-pro-options',
			'autoparts_chatbot_section',
			array( 'field' => 'chatbot_api_key', 'type' => 'password' )
		);
		
		add_settings_field(
			'chatbot_api_endpoint',
			__( 'API Endpoint', 'autoparts-pro' ),
			array( __CLASS__, 'render_text_field' ),
			'autoparts-pro-options',
			'autoparts_chatbot_section',
			array( 'field' => 'chatbot_api_endpoint', 'default' => 'https://api.openai.com/v1/chat/completions' )
		);
		
		add_settings_field(
			'chatbot_avatar',
			__( 'Chatbot Avatar', 'autoparts-pro' ),
			array( __CLASS__, 'render_image_upload_field' ),
			'autoparts-pro-options',
			'autoparts_chatbot_section',
			array( 'field' => 'chatbot_avatar' )
		);
		
		add_settings_field(
			'chatbot_greeting',
			__( 'Greeting Message', 'autoparts-pro' ),
			array( __CLASS__, 'render_text_field' ),
			'autoparts-pro-options',
			'autoparts_chatbot_section',
			array(
				'field'       => 'chatbot_greeting',
				'default'     => 'Hi! I\'m your AutoParts assistant. How can I help you find the right parts today?',
			)
		);
		
		add_settings_field(
			'chatbot_auto_open',
			__( 'Auto-open Delay (seconds, 0 to disable)', 'autoparts-pro' ),
			array( __CLASS__, 'render_number_field' ),
			'autoparts-pro-options',
			'autoparts_chatbot_section',
			array( 'field' => 'chatbot_auto_open', 'default' => 4 )
		);
		
		// Google Maps Section
		add_settings_section(
			'autoparts_maps_section',
			__( 'Google Maps Settings', 'autoparts-pro' ),
			array( __CLASS__, 'render_maps_section_desc' ),
			'autoparts-pro-options'
		);
		
		add_settings_field(
			'google_maps_api_key',
			__( 'Google Maps API Key', 'autoparts-pro' ),
			array( __CLASS__, 'render_text_field' ),
			'autoparts-pro-options',
			'autoparts_maps_section',
			array( 'field' => 'google_maps_api_key' )
		);
		
		add_settings_field(
			'map_style_dark',
			__( 'Dark Mode Map Style (JSON)', 'autoparts-pro' ),
			array( __CLASS__, 'render_textarea_field' ),
			'autoparts-pro-options',
			'autoparts_maps_section',
			array( 'field' => 'map_style_dark' )
		);
		
		// UI Settings Section
		add_settings_section(
			'autoparts_ui_section',
			__( 'UI Settings', 'autoparts-pro' ),
			array( __CLASS__, 'render_ui_section_desc' ),
			'autoparts-pro-options'
		);
		
		add_settings_field(
			'custom_cursor_enabled',
			__( 'Enable Custom Cursor', 'autoparts-pro' ),
			array( __CLASS__, 'render_checkbox_field' ),
			'autoparts-pro-options',
			'autoparts_ui_section',
			array( 'field' => 'custom_cursor_enabled' )
		);
		
		add_settings_field(
			'back_to_top_icon',
			__( 'Back to Top Icon', 'autoparts-pro' ),
			array( __CLASS__, 'render_select_field' ),
			'autoparts-pro-options',
			'autoparts_ui_section',
			array(
				'field'   => 'back_to_top_icon',
				'options' => array(
					'arrow'  => 'Arrow',
					'gear'   => 'Gear',
					'rocket' => 'Rocket',
				),
			)
		);
		
		add_settings_field(
			'wishlist_enabled',
			__( 'Enable Wishlist', 'autoparts-pro' ),
			array( __CLASS__, 'render_checkbox_field' ),
			'autoparts-pro-options',
			'autoparts_ui_section',
			array( 'field' => 'wishlist_enabled' )
		);
		
		add_settings_field(
			'quick_view_enabled',
			__( 'Enable Quick View', 'autoparts-pro' ),
			array( __CLASS__, 'render_checkbox_field' ),
			'autoparts-pro-options',
			'autoparts_ui_section',
			array( 'field' => 'quick_view_enabled' )
		);
		
		add_settings_field(
			'compatibility_checker_enabled',
			__( 'Enable Vehicle Compatibility Checker', 'autoparts-pro' ),
			array( __CLASS__, 'render_checkbox_field' ),
			'autoparts-pro-options',
			'autoparts_ui_section',
			array( 'field' => 'compatibility_checker_enabled' )
		);
		
		add_settings_field(
			'mega_menu_enabled',
			__( 'Enable Mega Menu', 'autoparts-pro' ),
			array( __CLASS__, 'render_checkbox_field' ),
			'autoparts-pro-options',
			'autoparts_ui_section',
			array( 'field' => 'mega_menu_enabled' )
		);
	}
	
	/**
	 * Get default options
	 */
	public static function get_default_options() {
		return array(
			'logo_type'                 => 'text',
			'logo_text'                 => 'AutoParts Pro',
			'logo_light'                => '',
			'logo_dark'                 => '',
			'logo_sticky'               => '',
			'logo_mobile'               => '',
			'primary_color'             => '#DC2626',
			'dark_bg_color'             => '#0A0A0A',
			'mid_gray_color'            => '#2D2D2D',
			'steel_gray_color'          => '#6B7280',
			'accent_color'              => '#EA580C',
			'light_mode_bg'             => '#FFFFFF',
			'light_mode_text'           => '#0A0A0A',
			'dark_mode_text'            => '#F5F5F5',
			'heading_font'              => 'rajdhani',
			'body_font'                 => 'inter',
			'numbers_font'              => 'jetbrains-mono',
			'hero_enabled'              => true,
			'hero_model'                => '',
			'hero_scroll_distance'      => 800,
			'hero_labels'               => '',
			'chatbot_enabled'           => true,
			'chatbot_api_key'           => '',
			'chatbot_api_endpoint'      => 'https://api.openai.com/v1/chat/completions',
			'chatbot_avatar'            => '',
			'chatbot_greeting'          => 'Hi! I\'m your AutoParts assistant. How can I help you find the right parts today?',
			'chatbot_auto_open'         => 4,
			'google_maps_api_key'       => '',
			'map_style_dark'            => '',
			'custom_cursor_enabled'     => true,
			'back_to_top_icon'          => 'gear',
			'wishlist_enabled'          => true,
			'quick_view_enabled'        => true,
			'compatibility_checker_enabled' => true,
			'mega_menu_enabled'         => true,
		);
	}
	
	/**
	 * Sanitize options
	 */
	public static function sanitize_options( $input ) {
		$sanitized = array();
		$defaults  = self::get_default_options();
		
		foreach ( $defaults as $key => $default ) {
			if ( isset( $input[ $key ] ) ) {
				switch ( $key ) {
					case 'hero_enabled':
					case 'chatbot_enabled':
					case 'custom_cursor_enabled':
					case 'wishlist_enabled':
					case 'quick_view_enabled':
					case 'compatibility_checker_enabled':
					case 'mega_menu_enabled':
						$sanitized[ $key ] = (bool) $input[ $key ];
						break;
						
					case 'hero_scroll_distance':
					case 'chatbot_auto_open':
						$sanitized[ $key ] = absint( $input[ $key ] );
						break;
						
					case 'hero_labels':
					case 'map_style_dark':
						$sanitized[ $key ] = wp_kses_post( $input[ $key ] );
						break;
						
					case 'chatbot_api_key':
						$sanitized[ $key ] = sanitize_text_field( $input[ $key ] );
						break;
						
					default:
						$sanitized[ $key ] = sanitize_text_field( $input[ $key ] );
						break;
				}
			} else {
				$sanitized[ $key ] = $default;
			}
		}
		
		return $sanitized;
	}
	
	/**
	 * Get option value
	 */
	public static function get_option( $key, $default = null ) {
		$options = get_option( self::OPTION_NAME, self::get_default_options() );
		return isset( $options[ $key ] ) ? $options[ $key ] : $default;
	}
	
	/**
	 * Enqueue assets
	 */
	public static function enqueue_assets( $hook ) {
		if ( ! in_array( $hook, array( 'appearance_page_autoparts-pro-options', 'appearance_page_autoparts-pro-builder' ), true ) ) {
			return;
		}
		
		wp_enqueue_media();
		wp_enqueue_style(
			'autoparts-admin-options',
			get_template_directory_uri() . '/assets/css/admin-options.css',
			array(),
			'1.0.0'
		);
		
		wp_enqueue_script(
			'autoparts-admin-options',
			get_template_directory_uri() . '/assets/js/admin-options.js',
			array( 'jquery' ),
			'1.0.0',
			true
		);
		
		wp_localize_script(
			'autoparts-admin-options',
			'autopartsAdmin',
			array(
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
				'nonce'     => wp_create_nonce( 'autoparts_admin_nonce' ),
				'options'   => self::get_option( null ),
				'i18n'      => array(
					'saveSuccess' => __( 'Settings saved successfully!', 'autoparts-pro' ),
					'saveError'   => __( 'Error saving settings.', 'autoparts-pro' ),
				),
			)
		);
	}
	
	/**
	 * Render options page
	 */
	public static function render_options_page() {
		?>
		<div class="wrap autoparts-pro-options">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			
			<form method="post" action="options.php" class="autoparts-options-form">
				<?php
				settings_fields( self::OPTION_GROUP );
				do_settings_sections( 'autoparts-pro-options' );
				submit_button( __( 'Save Settings', 'autoparts-pro' ) );
				?>
			</form>
			
			<div class="autoparts-pro-preview-panel">
				<h2><?php esc_html_e( 'Live Preview', 'autoparts-pro' ); ?></h2>
				<iframe src="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php esc_attr_e( 'Site Preview', 'autoparts-pro' ); ?>"></iframe>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Render builder page
	 */
	public static function render_builder_page() {
		?>
		<div class="wrap autoparts-pro-builder">
			<div class="builder-header">
				<h1><?php esc_html_e( '3-Rail Page Builder', 'autoparts-pro' ); ?></h1>
				<div class="builder-actions">
					<button class="button button-primary" id="builder-undo"><?php esc_html_e( 'Undo', 'autoparts-pro' ); ?></button>
					<button class="button button-primary" id="builder-redo"><?php esc_html_e( 'Redo', 'autoparts-pro' ); ?></button>
					<button class="button button-secondary" id="builder-save"><?php esc_html_e( 'Save All', 'autoparts-pro' ); ?></button>
				</div>
			</div>
			
			<div class="builder-container">
				<!-- Left Rail -->
				<div class="builder-rail builder-rail-left">
					<div class="rail-header">
						<h3><?php esc_html_e( 'Sections & Modules', 'autoparts-pro' ); ?></h3>
						<div class="rail-controls">
							<button class="button" id="toggle-dark-mode"><?php esc_html_e( '🌙', 'autoparts-pro' ); ?></button>
							<select id="language-switcher">
								<option value="en">EN</option>
								<option value="fr">FR</option>
								<option value="es">ES</option>
								<option value="de">DE</option>
							</select>
						</div>
					</div>
					
					<div class="sections-list">
						<h4><?php esc_html_e( 'Page Sections', 'autoparts-pro' ); ?></h4>
						<ul class="section-items" data-sortable="true">
							<!-- Dynamically populated -->
						</ul>
					</div>
					
					<div class="module-library">
						<h4><?php esc_html_e( 'Module Library', 'autoparts-pro' ); ?></h4>
						<input type="text" class="module-search" placeholder="<?php esc_attr_e( 'Search modules...', 'autoparts-pro' ); ?>">
						<div class="module-categories">
							<button class="category-btn active" data-category="all"><?php esc_html_e( 'All', 'autoparts-pro' ); ?></button>
							<button class="category-btn" data-category="content"><?php esc_html_e( 'Content', 'autoparts-pro' ); ?></button>
							<button class="category-btn" data-category="media"><?php esc_html_e( 'Media', 'autoparts-pro' ); ?></button>
							<button class="category-btn" data-category="interactive"><?php esc_html_e( 'Interactive', 'autoparts-pro' ); ?></button>
						</div>
						<div class="modules-grid">
							<!-- 24 module types -->
							<div class="module-item" data-module="text" data-category="content">
								<span class="module-icon">📝</span>
								<span class="module-name"><?php esc_html_e( 'Text', 'autoparts-pro' ); ?></span>
							</div>
							<div class="module-item" data-module="heading" data-category="content">
								<span class="module-icon">🔤</span>
								<span class="module-name"><?php esc_html_e( 'Heading', 'autoparts-pro' ); ?></span>
							</div>
							<div class="module-item" data-module="button" data-category="interactive">
								<span class="module-icon">🔘</span>
								<span class="module-name"><?php esc_html_e( 'Button', 'autoparts-pro' ); ?></span>
							</div>
							<div class="module-item" data-module="image" data-category="media">
								<span class="module-icon">🖼️</span>
								<span class="module-name"><?php esc_html_e( 'Image', 'autoparts-pro' ); ?></span>
							</div>
							<div class="module-item" data-module="video" data-category="media">
								<span class="module-icon">🎬</span>
								<span class="module-name"><?php esc_html_e( 'Video', 'autoparts-pro' ); ?></span>
							</div>
							<div class="module-item" data-module="icon" data-category="media">
								<span class="module-icon">⭐</span>
								<span class="module-name"><?php esc_html_e( 'Icon', 'autoparts-pro' ); ?></span>
							</div>
							<div class="module-item" data-module="divider" data-category="content">
								<span class="module-icon">➖</span>
								<span class="module-name"><?php esc_html_e( 'Divider', 'autoparts-pro' ); ?></span>
							</div>
							<div class="module-item" data-module="spacer" data-category="content">
								<span class="module-icon">↕️</span>
								<span class="module-name"><?php esc_html_e( 'Spacer', 'autoparts-pro' ); ?></span>
							</div>
							<div class="module-item" data-module="form" data-category="interactive">
								<span class="module-icon">📋</span>
								<span class="module-name"><?php esc_html_e( 'Form', 'autoparts-pro' ); ?></span>
							</div>
							<div class="module-item" data-module="testimonials" data-category="content">
								<span class="module-icon">💬</span>
								<span class="module-name"><?php esc_html_e( 'Testimonials', 'autoparts-pro' ); ?></span>
							</div>
							<div class="module-item" data-module="team" data-category="content">
								<span class="module-icon">👥</span>
								<span class="module-name"><?php esc_html_e( 'Team', 'autoparts-pro' ); ?></span>
							</div>
							<div class="module-item" data-module="pricing" data-category="interactive">
								<span class="module-icon">💰</span>
								<span class="module-name"><?php esc_html_e( 'Pricing', 'autoparts-pro' ); ?></span>
							</div>
							<div class="module-item" data-module="blog" data-category="content">
								<span class="module-icon">📰</span>
								<span class="module-name"><?php esc_html_e( 'Blog', 'autoparts-pro' ); ?></span>
							</div>
							<div class="module-item" data-module="accordion" data-category="interactive">
								<span class="module-icon">📂</span>
								<span class="module-name"><?php esc_html_e( 'Accordion', 'autoparts-pro' ); ?></span>
							</div>
							<div class="module-item" data-module="features" data-category="content">
								<span class="module-icon">✨</span>
								<span class="module-name"><?php esc_html_e( 'Features', 'autoparts-pro' ); ?></span>
							</div>
							<div class="module-item" data-module="stats" data-category="content">
								<span class="module-icon">📊</span>
								<span class="module-name"><?php esc_html_e( 'Stats', 'autoparts-pro' ); ?></span>
							</div>
							<div class="module-item" data-module="progress" data-category="interactive">
								<span class="module-icon">📈</span>
								<span class="module-name"><?php esc_html_e( 'Progress Bars', 'autoparts-pro' ); ?></span>
							</div>
							<div class="module-item" data-module="timeline" data-category="content">
								<span class="module-icon">⏱️</span>
								<span class="module-name"><?php esc_html_e( 'Timeline', 'autoparts-pro' ); ?></span>
							</div>
							<div class="module-item" data-module="logo-carousel" data-category="media">
								<span class="module-icon">🎠</span>
								<span class="module-name"><?php esc_html_e( 'Logo Carousel', 'autoparts-pro' ); ?></span>
							</div>
							<div class="module-item" data-module="social-feed" data-category="interactive">
								<span class="module-icon">📱</span>
								<span class="module-name"><?php esc_html_e( 'Social Feed', 'autoparts-pro' ); ?></span>
							</div>
							<div class="module-item" data-module="search" data-category="interactive">
								<span class="module-icon">🔍</span>
								<span class="module-name"><?php esc_html_e( 'Search', 'autoparts-pro' ); ?></span>
							</div>
							<div class="module-item" data-module="breadcrumbs" data-category="interactive">
								<span class="module-icon">🍞</span>
								<span class="module-name"><?php esc_html_e( 'Breadcrumbs', 'autoparts-pro' ); ?></span>
							</div>
							<div class="module-item" data-module="custom-html" data-category="content">
								<span class="module-icon">&lt;/&gt;</span>
								<span class="module-name"><?php esc_html_e( 'Custom HTML', 'autoparts-pro' ); ?></span>
							</div>
							<div class="module-item" data-module="3d-hero" data-category="media">
								<span class="module-icon">🎮</span>
								<span class="module-name"><?php esc_html_e( '3D Hero', 'autoparts-pro' ); ?></span>
							</div>
						</div>
					</div>
					
					<div class="accessibility-panel">
						<h4><?php esc_html_e( 'Accessibility', 'autoparts-pro' ); ?></h4>
						<div class="accessibility-controls">
							<button class="a11y-btn" data-action="font-decrease">A-</button>
							<button class="a11y-btn" data-action="font-reset">A</button>
							<button class="a11y-btn" data-action="font-increase">A+</button>
							<label>
								<input type="checkbox" data-action="high-contrast">
								<?php esc_html_e( 'High Contrast', 'autoparts-pro' ); ?>
							</label>
							<label>
								<input type="checkbox" data-action="reduced-motion">
								<?php esc_html_e( 'Reduced Motion', 'autoparts-pro' ); ?>
							</label>
						</div>
					</div>
				</div>
				
				<!-- Center Stage -->
				<div class="builder-stage">
					<div class="stage-toolbar">
						<div class="device-preview">
							<button class="device-btn active" data-device="desktop" title="<?php esc_attr_e( 'Desktop', 'autoparts-pro' ); ?>">
								🖥️
							</button>
							<button class="device-btn" data-device="tablet" title="<?php esc_attr_e( 'Tablet', 'autoparts-pro' ); ?>">
								📱
							</button>
							<button class="device-btn" data-device="mobile" title="<?php esc_attr_e( 'Mobile', 'autoparts-pro' ); ?>">
								📲
							</button>
						</div>
						<div class="zoom-controls">
							<button class="button" id="zoom-out">−</button>
							<span id="zoom-level">100%</span>
							<button class="button" id="zoom-in">+</button>
						</div>
					</div>
					
					<div class="stage-canvas">
						<iframe id="builder-canvas" src="<?php echo esc_url( admin_url( 'admin-ajax.php?action=autoparts_builder_canvas' ) ); ?>"></iframe>
					</div>
				</div>
				
				<!-- Right Rail -->
				<div class="builder-rail builder-rail-right">
					<div class="rail-header">
						<h3><?php esc_html_e( 'Property Inspector', 'autoparts-pro' ); ?></h3>
					</div>
					
					<div class="property-tabs">
						<button class="tab-btn active" data-tab="layout"><?php esc_html_e( 'Layout', 'autoparts-pro' ); ?></button>
						<button class="tab-btn" data-tab="style"><?php esc_html_e( 'Style', 'autoparts-pro' ); ?></button>
						<button class="tab-btn" data-tab="advanced"><?php esc_html_e( 'Advanced', 'autoparts-pro' ); ?></button>
					</div>
					
					<div class="tab-content" id="tab-layout">
						<div class="property-group">
							<label><?php esc_html_e( 'Display', 'autoparts-pro' ); ?></label>
							<select class="property-select" data-property="display">
								<option value="block"><?php esc_html_e( 'Block', 'autoparts-pro' ); ?></option>
								<option value="flex"><?php esc_html_e( 'Flex', 'autoparts-pro' ); ?></option>
								<option value="grid"><?php esc_html_e( 'Grid', 'autoparts-pro' ); ?></option>
								<option value="none"><?php esc_html_e( 'None (Hide)', 'autoparts-pro' ); ?></option>
							</select>
						</div>
						
						<div class="property-group">
							<label><?php esc_html_e( 'Width', 'autoparts-pro' ); ?></label>
							<input type="text" class="property-input" data-property="width" placeholder="auto, 100%, 500px">
						</div>
						
						<div class="property-group">
							<label><?php esc_html_e( 'Height', 'autoparts-pro' ); ?></label>
							<input type="text" class="property-input" data-property="height" placeholder="auto, 100vh, 500px">
						</div>
						
						<div class="property-group">
							<label><?php esc_html_e( 'Padding', 'autoparts-pro' ); ?></label>
							<div class="spacing-inputs">
								<input type="text" class="property-input" data-property="paddingTop" placeholder="Top">
								<input type="text" class="property-input" data-property="paddingRight" placeholder="Right">
								<input type="text" class="property-input" data-property="paddingBottom" placeholder="Bottom">
								<input type="text" class="property-input" data-property="paddingLeft" placeholder="Left">
							</div>
						</div>
						
						<div class="property-group">
							<label><?php esc_html_e( 'Margin', 'autoparts-pro' ); ?></label>
							<div class="spacing-inputs">
								<input type="text" class="property-input" data-property="marginTop" placeholder="Top">
								<input type="text" class="property-input" data-property="marginRight" placeholder="Right">
								<input type="text" class="property-input" data-property="marginBottom" placeholder="Bottom">
								<input type="text" class="property-input" data-property="marginLeft" placeholder="Left">
							</div>
						</div>
					</div>
					
					<div class="tab-content" id="tab-style" style="display:none;">
						<div class="property-group">
							<label><?php esc_html_e( 'Background Color', 'autoparts-pro' ); ?></label>
							<input type="color" class="property-color" data-property="backgroundColor">
						</div>
						
						<div class="property-group">
							<label><?php esc_html_e( 'Background Image', 'autoparts-pro' ); ?></label>
							<div class="image-upload-wrapper">
								<img class="image-preview" src="" alt="">
								<button class="button upload-image-btn"><?php esc_html_e( 'Upload Image', 'autoparts-pro' ); ?></button>
								<button class="button remove-image-btn"><?php esc_html_e( 'Remove', 'autoparts-pro' ); ?></button>
								<input type="hidden" class="property-input" data-property="backgroundImage">
							</div>
						</div>
						
						<div class="property-group">
							<label><?php esc_html_e( 'Border Radius', 'autoparts-pro' ); ?></label>
							<input type="text" class="property-input" data-property="borderRadius" placeholder="0px, 50%, etc.">
						</div>
						
						<div class="property-group">
							<label><?php esc_html_e( 'Box Shadow', 'autoparts-pro' ); ?></label>
							<input type="text" class="property-input" data-property="boxShadow" placeholder="0 2px 10px rgba(0,0,0,0.1)">
						</div>
						
						<div class="property-group">
							<label><?php esc_html_e( 'Animation', 'autoparts-pro' ); ?></label>
							<select class="property-select" data-property="animation">
								<option value=""><?php esc_html_e( 'None', 'autoparts-pro' ); ?></option>
								<option value="fade-in"><?php esc_html_e( 'Fade In', 'autoparts-pro' ); ?></option>
								<option value="slide-up"><?php esc_html_e( 'Slide Up', 'autoparts-pro' ); ?></option>
								<option value="slide-down"><?php esc_html_e( 'Slide Down', 'autoparts-pro' ); ?></option>
								<option value="zoom-in"><?php esc_html_e( 'Zoom In', 'autoparts-pro' ); ?></option>
								<option value="mechanical-slide"><?php esc_html_e( 'Mechanical Slide', 'autoparts-pro' ); ?></option>
							</select>
						</div>
					</div>
					
					<div class="tab-content" id="tab-advanced" style="display:none;">
						<div class="property-group">
							<label><?php esc_html_e( 'CSS Class', 'autoparts-pro' ); ?></label>
							<input type="text" class="property-input" data-property="className" placeholder="my-custom-class">
						</div>
						
						<div class="property-group">
							<label><?php esc_html_e( 'Custom CSS', 'autoparts-pro' ); ?></label>
							<textarea class="property-textarea" data-property="customCSS" rows="5"></textarea>
						</div>
						
						<div class="property-group">
							<label><?php esc_html_e( 'Visibility', 'autoparts-pro' ); ?></label>
							<label class="checkbox-label">
								<input type="checkbox" data-property="visibleDesktop" checked>
								<?php esc_html_e( 'Desktop', 'autoparts-pro' ); ?>
							</label>
							<label class="checkbox-label">
								<input type="checkbox" data-property="visibleTablet" checked>
								<?php esc_html_e( 'Tablet', 'autoparts-pro' ); ?>
							</label>
							<label class="checkbox-label">
								<input type="checkbox" data-property="visibleMobile" checked>
								<?php esc_html_e( 'Mobile', 'autoparts-pro' ); ?>
							</label>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	
	// Section descriptions
	public static function render_logo_section_desc() {
		echo '<p>' . esc_html__( 'Configure logo settings for different modes and contexts.', 'autoparts-pro' ) . '</p>';
	}
	
	public static function render_colors_section_desc() {
		echo '<p>' . esc_html__( 'Define your brand color palette for light and dark modes.', 'autoparts-pro' ) . '</p>';
	}
	
	public static function render_typography_section_desc() {
		echo '<p>' . esc_html__( 'Select fonts for headings, body text, and numbers.', 'autoparts-pro' ) . '</p>';
	}
	
	public static function render_hero_section_desc() {
		echo '<p>' . esc_html__( 'Configure the 3D exploded view hero animation.', 'autoparts-pro' ) . '</p>';
	}
	
	public static function render_chatbot_section_desc() {
		echo '<p>' . esc_html__( 'Set up AI chatbot integration for 24/7 customer support.', 'autoparts-pro' ) . '</p>';
	}
	
	public static function render_maps_section_desc() {
		echo '<p>' . esc_html__( 'Configure Google Maps for store locator functionality.', 'autoparts-pro' ) . '</p>';
	}
	
	public static function render_ui_section_desc() {
		echo '<p>' . esc_html__( 'Toggle various UI features and customize their behavior.', 'autoparts-pro' ) . '</p>';
	}
	
	// Field renderers
	public static function render_logo_field( $args ) {
		$options = get_option( self::OPTION_NAME, self::get_default_options() );
		$value   = isset( $options[ $args['field'] ] ) ? $options[ $args['field'] ] : '';
		?>
		<div class="logo-upload-field">
			<img class="logo-preview" src="<?php echo esc_url( $value ); ?>" style="max-height: 60px; <?php echo $value ? '' : 'display:none;'; ?>">
			<button type="button" class="button upload-logo-btn" data-field="<?php echo esc_attr( $args['field'] ); ?>">
				<?php esc_html_e( 'Upload Logo', 'autoparts-pro' ); ?>
			</button>
			<button type="button" class="button remove-logo-btn" data-field="<?php echo esc_attr( $args['field'] ); ?>" style="<?php echo $value ? '' : 'display:none;'; ?>">
				<?php esc_html_e( 'Remove', 'autoparts-pro' ); ?>
			</button>
			<input type="hidden" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[<?php echo esc_attr( $args['field'] ); ?>]" value="<?php echo esc_attr( $value ); ?>" class="logo-url-input">
		</div>
		<?php
	}
	
	public static function render_logo_type_field( $args ) {
		$options = get_option( self::OPTION_NAME, self::get_default_options() );
		$value   = isset( $options[ $args['field'] ] ) ? $options[ $args['field'] ] : 'text';
		?>
		<select name="<?php echo esc_attr( self::OPTION_NAME ); ?>[<?php echo esc_attr( $args['field'] ); ?>]">
			<option value="text" <?php selected( $value, 'text' ); ?>><?php esc_html_e( 'Text Only', 'autoparts-pro' ); ?></option>
			<option value="image" <?php selected( $value, 'image' ); ?>><?php esc_html_e( 'Image Only', 'autoparts-pro' ); ?></option>
			<option value="image-text" <?php selected( $value, 'image-text' ); ?>><?php esc_html_e( 'Image + Text', 'autoparts-pro' ); ?></option>
		</select>
		<?php
	}
	
	public static function render_text_field( $args ) {
		$options = get_option( self::OPTION_NAME, self::get_default_options() );
		$value   = isset( $options[ $args['field'] ] ) ? $options[ $args['field'] ] : ( isset( $args['default'] ) ? $args['default'] : '' );
		$type    = isset( $args['type'] ) ? $args['type'] : 'text';
		?>
		<input type="<?php echo esc_attr( $type ); ?>" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[<?php echo esc_attr( $args['field'] ); ?>]" value="<?php echo esc_attr( $value ); ?>" class="regular-text" <?php echo isset( $args['placeholder'] ) ? 'placeholder="' . esc_attr( $args['placeholder'] ) . '"' : ''; ?>>
		<?php
	}
	
	public static function render_color_field( $args ) {
		$options = get_option( self::OPTION_NAME, self::get_default_options() );
		$value   = isset( $options[ $args['field'] ] ) ? $options[ $args['field'] ] : '';
		?>
		<input type="color" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[<?php echo esc_attr( $args['field'] ); ?>]" value="<?php echo esc_attr( $value ); ?>" class="color-picker">
		<span class="color-value"><?php echo esc_html( $value ); ?></span>
		<?php
	}
	
	public static function render_select_field( $args ) {
		$options = get_option( self::OPTION_NAME, self::get_default_options() );
		$value   = isset( $options[ $args['field'] ] ) ? $options[ $args['field'] ] : '';
		?>
		<select name="<?php echo esc_attr( self::OPTION_NAME ); ?>[<?php echo esc_attr( $args['field'] ); ?>]">
			<?php foreach ( $args['options'] as $key => $label ) : ?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $value, $key ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}
	
	public static function render_checkbox_field( $args ) {
		$options = get_option( self::OPTION_NAME, self::get_default_options() );
		$value   = isset( $options[ $args['field'] ] ) ? $options[ $args['field'] ] : false;
		?>
		<label>
			<input type="checkbox" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[<?php echo esc_attr( $args['field'] ); ?>]" value="1" <?php checked( $value, true ); ?>>
			<?php esc_html_e( 'Enabled', 'autoparts-pro' ); ?>
		</label>
		<?php
	}
	
	public static function render_number_field( $args ) {
		$options = get_option( self::OPTION_NAME, self::get_default_options() );
		$value   = isset( $options[ $args['field'] ] ) ? $options[ $args['field'] ] : ( isset( $args['default'] ) ? $args['default'] : 0 );
		?>
		<input type="number" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[<?php echo esc_attr( $args['field'] ); ?>]" value="<?php echo esc_attr( $value ); ?>" class="small-text">
		<?php
	}
	
	public static function render_textarea_field( $args ) {
		$options = get_option( self::OPTION_NAME, self::get_default_options() );
		$value   = isset( $options[ $args['field'] ] ) ? $options[ $args['field'] ] : '';
		?>
		<textarea name="<?php echo esc_attr( self::OPTION_NAME ); ?>[<?php echo esc_attr( $args['field'] ); ?>]" class="large-text code" rows="5" <?php echo isset( $args['placeholder'] ) ? 'placeholder="' . esc_attr( $args['placeholder'] ) . '"' : ''; ?>><?php echo esc_textarea( $value ); ?></textarea>
		<?php
	}
	
	public static function render_file_upload_field( $args ) {
		$options = get_option( self::OPTION_NAME, self::get_default_options() );
		$value   = isset( $options[ $args['field'] ] ) ? $options[ $args['field'] ] : '';
		?>
		<div class="file-upload-field">
			<input type="text" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[<?php echo esc_attr( $args['field'] ); ?>]" value="<?php echo esc_attr( $value ); ?>" class="regular-text file-url">
			<button type="button" class="button upload-file-btn" data-field="<?php echo esc_attr( $args['field'] ); ?>" data-mime="<?php echo esc_attr( isset( $args['mime_type'] ) ? $args['mime_type'] : '' ); ?>">
				<?php esc_html_e( 'Upload File', 'autoparts-pro' ); ?>
			</button>
		</div>
		<?php
	}
	
	public static function render_image_upload_field( $args ) {
		$options = get_option( self::OPTION_NAME, self::get_default_options() );
		$value   = isset( $options[ $args['field'] ] ) ? $options[ $args['field'] ] : '';
		?>
		<div class="image-upload-field">
			<img class="image-preview" src="<?php echo esc_url( $value ); ?>" style="max-width: 200px; <?php echo $value ? '' : 'display:none;'; ?>">
			<button type="button" class="button upload-image-btn" data-field="<?php echo esc_attr( $args['field'] ); ?>">
				<?php esc_html_e( 'Upload Image', 'autoparts-pro' ); ?>
			</button>
			<button type="button" class="button remove-image-btn" data-field="<?php echo esc_attr( $args['field'] ); ?>" style="<?php echo $value ? '' : 'display:none;'; ?>">
				<?php esc_html_e( 'Remove', 'autoparts-pro' ); ?>
			</button>
			<input type="hidden" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[<?php echo esc_attr( $args['field'] ); ?>]" value="<?php echo esc_attr( $value ); ?>" class="image-url-input">
		</div>
		<?php
	}
}

Theme_Options::init();
