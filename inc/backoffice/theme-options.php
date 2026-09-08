<?php
/**
 * AutoParts Pro Theme Options Panel
 * 
 * @package AutoParts_Pro
 * @since 1.0.0
 */

namespace AutoPartsPro\Backoffice;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme Options Page Class
 */
class Theme_Options {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', [$this, 'add_options_page']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }
    
    /**
     * Add theme options page to admin menu
     */
    public function add_options_page() {
        add_theme_page(
            __('AutoParts Pro Options', 'autoparts-pro'),
            __('Theme Options', 'autoparts-pro'),
            'manage_options',
            'autoparts-pro-options',
            [$this, 'render_options_page']
        );
    }
    
    /**
     * Register all theme settings
     */
    public function register_settings() {
        // General Settings Section
        register_setting('autoparts_pro_options', 'app_general_settings', [
            'sanitize_callback' => [$this, 'sanitize_general_settings'],
            'default' => $this->get_default_general_settings()
        ]);
        
        // Hero Section Settings
        register_setting('autoparts_pro_options', 'app_hero_settings', [
            'sanitize_callback' => [$this, 'sanitize_hero_settings'],
            'default' => $this->get_default_hero_settings()
        ]);
        
        // Chatbot Settings
        register_setting('autoparts_pro_options', 'app_chatbot_settings', [
            'sanitize_callback' => [$this, 'sanitize_chatbot_settings'],
            'default' => $this->get_default_chatbot_settings()
        ]);
        
        // Google Maps Settings
        register_setting('autoparts_pro_options', 'app_maps_settings', [
            'sanitize_callback' => [$this, 'sanitize_maps_settings'],
            'default' => $this->get_default_maps_settings()
        ]);
        
        // Custom Cursor Settings
        register_setting('autoparts_pro_options', 'app_cursor_settings', [
            'sanitize_callback' => [$this, 'sanitize_cursor_settings'],
            'default' => $this->get_default_cursor_settings()
        ]);
        
        // Header Settings
        register_setting('autoparts_pro_options', 'app_header_settings', [
            'sanitize_callback' => [$this, 'sanitize_header_settings'],
            'default' => $this->get_default_header_settings()
        ]);
        
        // Footer Settings
        register_setting('autoparts_pro_options', 'app_footer_settings', [
            'sanitize_callback' => [$this, 'sanitize_footer_settings'],
            'default' => $this->get_default_footer_settings()
        ]);
        
        // Performance Settings
        register_setting('autoparts_pro_options', 'app_performance_settings', [
            'sanitize_callback' => [$this, 'sanitize_performance_settings'],
            'default' => $this->get_default_performance_settings()
        ]);
    }
    
    /**
     * Get default general settings
     */
    private function get_default_general_settings() {
        return [
            'site_mode' => 'dark',
            'primary_color' => '#DC2626',
            'accent_color' => '#EA580C',
            'enable_preloader' => true,
            'preloader_timeout' => 3000,
        ];
    }
    
    /**
     * Get default hero settings
     */
    private function get_default_hero_settings() {
        return [
            'enable_hero' => true,
            'hero_3d_model' => '',
            'hero_title' => __('Premium Auto Parts', 'autoparts-pro'),
            'hero_subtitle' => __('Engineered for Performance', 'autoparts-pro'),
            'scroll_sensitivity' => 1.0,
            'explosion_distance' => 500,
            'auto_rotate' => false,
            'rotation_speed' => 0.5,
        ];
    }
    
    /**
     * Get default chatbot settings
     */
    private function get_default_chatbot_settings() {
        return [
            'enable_chatbot' => true,
            'chatbot_name' => __('AutoAssistant', 'autoparts-pro'),
            'chatbot_avatar' => '',
            'greeting_message' => __('Hi! Need help finding the right parts?', 'autoparts-pro'),
            'api_endpoint' => '',
            'api_key' => '',
            'auto_open_delay' => 4000,
            'auto_close_delay' => 3000,
            'play_sound' => true,
            'sound_file' => '',
        ];
    }
    
    /**
     * Get default maps settings
     */
    private function get_default_maps_settings() {
        return [
            'enable_maps' => true,
            'google_api_key' => '',
            'default_lat' => 40.7128,
            'default_lng' => -74.0060,
            'zoom_level' => 12,
            'map_style' => 'dark',
            'marker_icon' => 'gear',
        ];
    }
    
    /**
     * Get default cursor settings
     */
    private function get_default_cursor_settings() {
        return [
            'enable_cursor' => true,
            'cursor_style' => 'crosshair',
            'hover_effect' => 'gear',
            'click_effect' => 'spark',
            'trail_enabled' => true,
            'trail_length' => 10,
            'disable_on_mobile' => true,
        ];
    }
    
    /**
     * Get default header settings
     */
    private function get_default_header_settings() {
        return [
            'header_layout' => 'standard',
            'sticky_header' => true,
            'transparent_header' => false,
            'logo_light' => '',
            'logo_dark' => '',
            'logo_sticky' => '',
            'logo_mobile' => '',
            'show_vehicle_selector' => true,
            'show_wishlist_icon' => true,
            'show_cart_icon' => true,
            'show_search' => true,
            'menu_style' => 'gear-shift',
        ];
    }
    
    /**
     * Get default footer settings
     */
    private function get_default_footer_settings() {
        return [
            'footer_layout' => 'standard',
            'show_newsletter' => true,
            'show_social_links' => true,
            'show_payment_icons' => true,
            'copyright_text' => sprintf(
                __('&copy; %s AutoParts Pro. All rights reserved.', 'autoparts-pro'),
                date('Y')
            ),
            'back_to_top_enabled' => true,
            'back_to_top_icon' => 'arrow',
        ];
    }
    
    /**
     * Get default performance settings
     */
    private function get_default_performance_settings() {
        return [
            'lazy_load_images' => true,
            'lazy_load_iframes' => true,
            'minify_css' => false,
            'minify_js' => false,
            'preload_critical_assets' => true,
            'dns_prefetch_domains' => [],
            'disable_emojis' => true,
            'disable_embeds' => false,
        ];
    }
    
    /**
     * Sanitize general settings
     */
    public function sanitize_general_settings($input) {
        $sanitized = [];
        $sanitized['site_mode'] = in_array($input['site_mode'], ['light', 'dark']) ? $input['site_mode'] : 'dark';
        $sanitized['primary_color'] = sanitize_hex_color($input['primary_color']) ?: '#DC2626';
        $sanitized['accent_color'] = sanitize_hex_color($input['accent_color']) ?: '#EA580C';
        $sanitized['enable_preloader'] = !empty($input['enable_preloader']);
        $sanitized['preloader_timeout'] = absint($input['preloader_timeout']);
        return $sanitized;
    }
    
    /**
     * Sanitize hero settings
     */
    public function sanitize_hero_settings($input) {
        $sanitized = [];
        $sanitized['enable_hero'] = !empty($input['enable_hero']);
        $sanitized['hero_3d_model'] = esc_url_raw($input['hero_3d_model']);
        $sanitized['hero_title'] = sanitize_text_field($input['hero_title']);
        $sanitized['hero_subtitle'] = sanitize_text_field($input['hero_subtitle']);
        $sanitized['scroll_sensitivity'] = floatval($input['scroll_sensitivity']);
        $sanitized['explosion_distance'] = absint($input['explosion_distance']);
        $sanitized['auto_rotate'] = !empty($input['auto_rotate']);
        $sanitized['rotation_speed'] = floatval($input['rotation_speed']);
        return $sanitized;
    }
    
    /**
     * Sanitize chatbot settings
     */
    public function sanitize_chatbot_settings($input) {
        $sanitized = [];
        $sanitized['enable_chatbot'] = !empty($input['enable_chatbot']);
        $sanitized['chatbot_name'] = sanitize_text_field($input['chatbot_name']);
        $sanitized['chatbot_avatar'] = esc_url_raw($input['chatbot_avatar']);
        $sanitized['greeting_message'] = sanitize_textarea_field($input['greeting_message']);
        $sanitized['api_endpoint'] = esc_url_raw($input['api_endpoint']);
        $sanitized['api_key'] = sanitize_text_field($input['api_key']);
        $sanitized['auto_open_delay'] = absint($input['auto_open_delay']);
        $sanitized['auto_close_delay'] = absint($input['auto_close_delay']);
        $sanitized['play_sound'] = !empty($input['play_sound']);
        $sanitized['sound_file'] = esc_url_raw($input['sound_file']);
        return $sanitized;
    }
    
    /**
     * Sanitize maps settings
     */
    public function sanitize_maps_settings($input) {
        $sanitized = [];
        $sanitized['enable_maps'] = !empty($input['enable_maps']);
        $sanitized['google_api_key'] = sanitize_text_field($input['google_api_key']);
        $sanitized['default_lat'] = floatval($input['default_lat']);
        $sanitized['default_lng'] = floatval($input['default_lng']);
        $sanitized['zoom_level'] = absint($input['zoom_level']);
        $sanitized['map_style'] = in_array($input['map_style'], ['light', 'dark', 'custom']) ? $input['map_style'] : 'dark';
        $sanitized['marker_icon'] = in_array($input['marker_icon'], ['gear', 'wrench', 'pin']) ? $input['marker_icon'] : 'gear';
        return $sanitized;
    }
    
    /**
     * Sanitize cursor settings
     */
    public function sanitize_cursor_settings($input) {
        $sanitized = [];
        $sanitized['enable_cursor'] = !empty($input['enable_cursor']);
        $sanitized['cursor_style'] = in_array($input['cursor_style'], ['crosshair', 'dot', 'none']) ? $input['cursor_style'] : 'crosshair';
        $sanitized['hover_effect'] = in_array($input['hover_effect'], ['gear', 'pointer', 'none']) ? $input['hover_effect'] : 'gear';
        $sanitized['click_effect'] = in_array($input['click_effect'], ['spark', 'ripple', 'none']) ? $input['click_effect'] : 'spark';
        $sanitized['trail_enabled'] = !empty($input['trail_enabled']);
        $sanitized['trail_length'] = absint($input['trail_length']);
        $sanitized['disable_on_mobile'] = !empty($input['disable_on_mobile']);
        return $sanitized;
    }
    
    /**
     * Sanitize header settings
     */
    public function sanitize_header_settings($input) {
        $sanitized = [];
        $sanitized['header_layout'] = in_array($input['header_layout'], ['standard', 'centered', 'split']) ? $input['header_layout'] : 'standard';
        $sanitized['sticky_header'] = !empty($input['sticky_header']);
        $sanitized['transparent_header'] = !empty($input['transparent_header']);
        $sanitized['logo_light'] = absint($input['logo_light']);
        $sanitized['logo_dark'] = absint($input['logo_dark']);
        $sanitized['logo_sticky'] = absint($input['logo_sticky']);
        $sanitized['logo_mobile'] = absint($input['logo_mobile']);
        $sanitized['show_vehicle_selector'] = !empty($input['show_vehicle_selector']);
        $sanitized['show_wishlist_icon'] = !empty($input['show_wishlist_icon']);
        $sanitized['show_cart_icon'] = !empty($input['show_cart_icon']);
        $sanitized['show_search'] = !empty($input['show_search']);
        $sanitized['menu_style'] = in_array($input['menu_style'], ['gear-shift', 'standard', 'minimal']) ? $input['menu_style'] : 'gear-shift';
        return $sanitized;
    }
    
    /**
     * Sanitize footer settings
     */
    public function sanitize_footer_settings($input) {
        $sanitized = [];
        $sanitized['footer_layout'] = in_array($input['footer_layout'], ['standard', 'minimal', 'extended']) ? $input['footer_layout'] : 'standard';
        $sanitized['show_newsletter'] = !empty($input['show_newsletter']);
        $sanitized['show_social_links'] = !empty($input['show_social_links']);
        $sanitized['show_payment_icons'] = !empty($input['show_payment_icons']);
        $sanitized['copyright_text'] = wp_kses_post($input['copyright_text']);
        $sanitized['back_to_top_enabled'] = !empty($input['back_to_top_enabled']);
        $sanitized['back_to_top_icon'] = in_array($input['back_to_top_icon'], ['arrow', 'gear', 'rocket']) ? $input['back_to_top_icon'] : 'arrow';
        return $sanitized;
    }
    
    /**
     * Sanitize performance settings
     */
    public function sanitize_performance_settings($input) {
        $sanitized = [];
        $sanitized['lazy_load_images'] = !empty($input['lazy_load_images']);
        $sanitized['lazy_load_iframes'] = !empty($input['lazy_load_iframes']);
        $sanitized['minify_css'] = !empty($input['minify_css']);
        $sanitized['minify_js'] = !empty($input['minify_js']);
        $sanitized['preload_critical_assets'] = !empty($input['preload_critical_assets']);
        $sanitized['dns_prefetch_domains'] = array_map('esc_url_raw', (array)$input['dns_prefetch_domains']);
        $sanitized['disable_emojis'] = !empty($input['disable_emojis']);
        $sanitized['disable_embeds'] = !empty($input['disable_embeds']);
        return $sanitized;
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_assets($hook) {
        if ('appearance_page_autoparts-pro-options' !== $hook) {
            return;
        }
        
        wp_enqueue_style(
            'app-options-css',
            get_template_directory_uri() . '/assets/css/admin-options.css',
            [],
            APP_VERSION
        );
        
        wp_enqueue_script(
            'wp-color-picker'
        );
        
        wp_enqueue_media();
        
        wp_enqueue_script(
            'app-options-js',
            get_template_directory_uri() . '/assets/js/admin-options.js',
            ['jquery', 'wp-color-picker'],
            APP_VERSION,
            true
        );
        
        wp_localize_script('app-options-js', 'appOptionsData', [
            'nonce' => wp_create_nonce('app_options_nonce'),
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'strings' => [
                'saveSuccess' => __('Settings saved successfully!', 'autoparts-pro'),
                'saveError' => __('Error saving settings. Please try again.', 'autoparts-pro'),
                'confirmReset' => __('Are you sure you want to reset all settings to default?', 'autoparts-pro'),
            ],
        ]);
    }
    
    /**
     * Render the options page
     */
    public function render_options_page() {
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
        ?>
        <div class="wrap app-theme-options">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="app-options-container">
                <nav class="app-options-nav">
                    <ul class="nav-tabs">
                        <li><a href="?page=autoparts-pro-options&tab=general" class="<?php echo $active_tab === 'general' ? 'active' : ''; ?>"><?php _e('General', 'autoparts-pro'); ?></a></li>
                        <li><a href="?page=autoparts-pro-options&tab=hero" class="<?php echo $active_tab === 'hero' ? 'active' : ''; ?>"><?php _e('Hero Section', 'autoparts-pro'); ?></a></li>
                        <li><a href="?page=autoparts-pro-options&tab=chatbot" class="<?php echo $active_tab === 'chatbot' ? 'active' : ''; ?>"><?php _e('Chatbot', 'autoparts-pro'); ?></a></li>
                        <li><a href="?page=autoparts-pro-options&tab=maps" class="<?php echo $active_tab === 'maps' ? 'active' : ''; ?>"><?php _e('Maps', 'autoparts-pro'); ?></a></li>
                        <li><a href="?page=autoparts-pro-options&tab=cursor" class="<?php echo $active_tab === 'cursor' ? 'active' : ''; ?>"><?php _e('Cursor', 'autoparts-pro'); ?></a></li>
                        <li><a href="?page=autoparts-pro-options&tab=header" class="<?php echo $active_tab === 'header' ? 'active' : ''; ?>"><?php _e('Header', 'autoparts-pro'); ?></a></li>
                        <li><a href="?page=autoparts-pro-options&tab=footer" class="<?php echo $active_tab === 'footer' ? 'active' : ''; ?>"><?php _e('Footer', 'autoparts-pro'); ?></a></li>
                        <li><a href="?page=autoparts-pro-options&tab=performance" class="<?php echo $active_tab === 'performance' ? 'active' : ''; ?>"><?php _e('Performance', 'autoparts-pro'); ?></a></li>
                    </ul>
                </nav>
                
                <form method="post" action="options.php" enctype="multipart/form-data">
                    <?php
                    settings_fields('autoparts_pro_options');
                    
                    switch ($active_tab) {
                        case 'general':
                            $this->render_general_tab();
                            break;
                        case 'hero':
                            $this->render_hero_tab();
                            break;
                        case 'chatbot':
                            $this->render_chatbot_tab();
                            break;
                        case 'maps':
                            $this->render_maps_tab();
                            break;
                        case 'cursor':
                            $this->render_cursor_tab();
                            break;
                        case 'header':
                            $this->render_header_tab();
                            break;
                        case 'footer':
                            $this->render_footer_tab();
                            break;
                        case 'performance':
                            $this->render_performance_tab();
                            break;
                    }
                    
                    submit_button(__('Save Settings', 'autoparts-pro'));
                    ?>
                </form>
                
                <div class="app-reset-section">
                    <button type="button" id="app-reset-settings" class="button button-secondary">
                        <?php _e('Reset to Defaults', 'autoparts-pro'); ?>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
    
    private function render_general_tab() {
        $settings = get_option('app_general_settings', $this->get_default_general_settings());
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Site Mode', 'autoparts-pro'); ?></th>
                <td>
                    <select name="app_general_settings[site_mode]">
                        <option value="light" <?php selected($settings['site_mode'], 'light'); ?>><?php _e('Light', 'autoparts-pro'); ?></option>
                        <option value="dark" <?php selected($settings['site_mode'], 'dark'); ?>><?php _e('Dark', 'autoparts-pro'); ?></option>
                    </select>
                    <p class="description"><?php _e('Default color mode for the site.', 'autoparts-pro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Primary Color', 'autoparts-pro'); ?></th>
                <td>
                    <input type="text" name="app_general_settings[primary_color]" value="<?php echo esc_attr($settings['primary_color']); ?>" class="app-color-picker" />
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Accent Color', 'autoparts-pro'); ?></th>
                <td>
                    <input type="text" name="app_general_settings[accent_color]" value="<?php echo esc_attr($settings['accent_color']); ?>" class="app-color-picker" />
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Enable Preloader', 'autoparts-pro'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="app_general_settings[enable_preloader]" value="1" <?php checked($settings['enable_preloader']); ?> />
                        <?php _e('Show loading animation on page load', 'autoparts-pro'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Preloader Timeout (ms)', 'autoparts-pro'); ?></th>
                <td>
                    <input type="number" name="app_general_settings[preloader_timeout]" value="<?php echo esc_attr($settings['preloader_timeout']); ?>" class="small-text" />
                </td>
            </tr>
        </table>
        <?php
    }
    
    private function render_hero_tab() {
        $settings = get_option('app_hero_settings', $this->get_default_hero_settings());
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Enable Hero', 'autoparts-pro'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="app_hero_settings[enable_hero]" value="1" <?php checked($settings['enable_hero']); ?> />
                        <?php _e('Show 3D exploded view hero section', 'autoparts-pro'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('3D Model URL', 'autoparts-pro'); ?></th>
                <td>
                    <input type="text" name="app_hero_settings[hero_3d_model]" value="<?php echo esc_attr($settings['hero_3d_model']); ?>" class="large-text" />
                    <button type="button" class="button app-upload-btn" data-target="hero_3d_model"><?php _e('Upload Model', 'autoparts-pro'); ?></button>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Hero Title', 'autoparts-pro'); ?></th>
                <td>
                    <input type="text" name="app_hero_settings[hero_title]" value="<?php echo esc_attr($settings['hero_title']); ?>" class="large-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Hero Subtitle', 'autoparts-pro'); ?></th>
                <td>
                    <input type="text" name="app_hero_settings[hero_subtitle]" value="<?php echo esc_attr($settings['hero_subtitle']); ?>" class="large-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Scroll Sensitivity', 'autoparts-pro'); ?></th>
                <td>
                    <input type="range" name="app_hero_settings[scroll_sensitivity]" min="0.1" max="2" step="0.1" value="<?php echo esc_attr($settings['scroll_sensitivity']); ?>" class="app-range-slider" />
                    <span class="app-range-value"><?php echo esc_html($settings['scroll_sensitivity']); ?></span>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Explosion Distance', 'autoparts-pro'); ?></th>
                <td>
                    <input type="number" name="app_hero_settings[explosion_distance]" value="<?php echo esc_attr($settings['explosion_distance']); ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Auto Rotate', 'autoparts-pro'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="app_hero_settings[auto_rotate]" value="1" <?php checked($settings['auto_rotate']); ?> />
                        <?php _e('Enable automatic rotation', 'autoparts-pro'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Rotation Speed', 'autoparts-pro'); ?></th>
                <td>
                    <input type="number" name="app_hero_settings[rotation_speed]" step="0.1" value="<?php echo esc_attr($settings['rotation_speed']); ?>" />
                </td>
            </tr>
        </table>
        <?php
    }
    
    private function render_chatbot_tab() {
        $settings = get_option('app_chatbot_settings', $this->get_default_chatbot_settings());
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Enable Chatbot', 'autoparts-pro'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="app_chatbot_settings[enable_chatbot]" value="1" <?php checked($settings['enable_chatbot']); ?> />
                        <?php _e('Show AI chatbot widget', 'autoparts-pro'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Chatbot Name', 'autoparts-pro'); ?></th>
                <td>
                    <input type="text" name="app_chatbot_settings[chatbot_name]" value="<?php echo esc_attr($settings['chatbot_name']); ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Chatbot Avatar', 'autoparts-pro'); ?></th>
                <td>
                    <input type="text" name="app_chatbot_settings[chatbot_avatar]" value="<?php echo esc_attr($settings['chatbot_avatar']); ?>" class="large-text" />
                    <button type="button" class="button app-upload-btn" data-target="chatbot_avatar"><?php _e('Upload Image', 'autoparts-pro'); ?></button>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Greeting Message', 'autoparts-pro'); ?></th>
                <td>
                    <textarea name="app_chatbot_settings[greeting_message]" rows="3" class="large-text"><?php echo esc_textarea($settings['greeting_message']); ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('API Endpoint', 'autoparts-pro'); ?></th>
                <td>
                    <input type="url" name="app_chatbot_settings[api_endpoint]" value="<?php echo esc_attr($settings['api_endpoint']); ?>" class="large-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('API Key', 'autoparts-pro'); ?></th>
                <td>
                    <input type="password" name="app_chatbot_settings[api_key]" value="<?php echo esc_attr($settings['api_key']); ?>" class="large-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Auto Open Delay (ms)', 'autoparts-pro'); ?></th>
                <td>
                    <input type="number" name="app_chatbot_settings[auto_open_delay]" value="<?php echo esc_attr($settings['auto_open_delay']); ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Play Sound', 'autoparts-pro'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="app_chatbot_settings[play_sound]" value="1" <?php checked($settings['play_sound']); ?> />
                        <?php _e('Play click sound on auto-open', 'autoparts-pro'); ?>
                    </label>
                </td>
            </tr>
        </table>
        <?php
    }
    
    private function render_maps_tab() {
        $settings = get_option('app_maps_settings', $this->get_default_maps_settings());
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Enable Maps', 'autoparts-pro'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="app_maps_settings[enable_maps]" value="1" <?php checked($settings['enable_maps']); ?> />
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Google API Key', 'autoparts-pro'); ?></th>
                <td>
                    <input type="text" name="app_maps_settings[google_api_key]" value="<?php echo esc_attr($settings['google_api_key']); ?>" class="large-text" />
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Default Latitude', 'autoparts-pro'); ?></th>
                <td>
                    <input type="number" step="0.000001" name="app_maps_settings[default_lat]" value="<?php echo esc_attr($settings['default_lat']); ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Default Longitude', 'autoparts-pro'); ?></th>
                <td>
                    <input type="number" step="0.000001" name="app_maps_settings[default_lng]" value="<?php echo esc_attr($settings['default_lng']); ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Zoom Level', 'autoparts-pro'); ?></th>
                <td>
                    <input type="number" name="app_maps_settings[zoom_level]" min="1" max="20" value="<?php echo esc_attr($settings['zoom_level']); ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Map Style', 'autoparts-pro'); ?></th>
                <td>
                    <select name="app_maps_settings[map_style]">
                        <option value="light" <?php selected($settings['map_style'], 'light'); ?>><?php _e('Light', 'autoparts-pro'); ?></option>
                        <option value="dark" <?php selected($settings['map_style'], 'dark'); ?>><?php _e('Dark', 'autoparts-pro'); ?></option>
                        <option value="custom" <?php selected($settings['map_style'], 'custom'); ?>><?php _e('Custom', 'autoparts-pro'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Marker Icon', 'autoparts-pro'); ?></th>
                <td>
                    <select name="app_maps_settings[marker_icon]">
                        <option value="gear" <?php selected($settings['marker_icon'], 'gear'); ?>><?php _e('Gear', 'autoparts-pro'); ?></option>
                        <option value="wrench" <?php selected($settings['marker_icon'], 'wrench'); ?>><?php _e('Wrench', 'autoparts-pro'); ?></option>
                        <option value="pin" <?php selected($settings['marker_icon'], 'pin'); ?>><?php _e('Pin', 'autoparts-pro'); ?></option>
                    </select>
                </td>
            </tr>
        </table>
        <?php
    }
    
    private function render_cursor_tab() {
        $settings = get_option('app_cursor_settings', $this->get_default_cursor_settings());
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Enable Custom Cursor', 'autoparts-pro'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="app_cursor_settings[enable_cursor]" value="1" <?php checked($settings['enable_cursor']); ?> />
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Cursor Style', 'autoparts-pro'); ?></th>
                <td>
                    <select name="app_cursor_settings[cursor_style]">
                        <option value="crosshair" <?php selected($settings['cursor_style'], 'crosshair'); ?>><?php _e('Crosshair', 'autoparts-pro'); ?></option>
                        <option value="dot" <?php selected($settings['cursor_style'], 'dot'); ?>><?php _e('Dot', 'autoparts-pro'); ?></option>
                        <option value="none" <?php selected($settings['cursor_style'], 'none'); ?>><?php _e('None', 'autoparts-pro'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Hover Effect', 'autoparts-pro'); ?></th>
                <td>
                    <select name="app_cursor_settings[hover_effect]">
                        <option value="gear" <?php selected($settings['hover_effect'], 'gear'); ?>><?php _e('Gear', 'autoparts-pro'); ?></option>
                        <option value="pointer" <?php selected($settings['hover_effect'], 'pointer'); ?>><?php _e('Pointer', 'autoparts-pro'); ?></option>
                        <option value="none" <?php selected($settings['hover_effect'], 'none'); ?>><?php _e('None', 'autoparts-pro'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Click Effect', 'autoparts-pro'); ?></th>
                <td>
                    <select name="app_cursor_settings[click_effect]">
                        <option value="spark" <?php selected($settings['click_effect'], 'spark'); ?>><?php _e('Spark', 'autoparts-pro'); ?></option>
                        <option value="ripple" <?php selected($settings['click_effect'], 'ripple'); ?>><?php _e('Ripple', 'autoparts-pro'); ?></option>
                        <option value="none" <?php selected($settings['click_effect'], 'none'); ?>><?php _e('None', 'autoparts-pro'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Trail Enabled', 'autoparts-pro'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="app_cursor_settings[trail_enabled]" value="1" <?php checked($settings['trail_enabled']); ?> />
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Disable on Mobile', 'autoparts-pro'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="app_cursor_settings[disable_on_mobile]" value="1" <?php checked($settings['disable_on_mobile']); ?> />
                    </label>
                </td>
            </tr>
        </table>
        <?php
    }
    
    private function render_header_tab() {
        $settings = get_option('app_header_settings', $this->get_default_header_settings());
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Header Layout', 'autoparts-pro'); ?></th>
                <td>
                    <select name="app_header_settings[header_layout]">
                        <option value="standard" <?php selected($settings['header_layout'], 'standard'); ?>><?php _e('Standard', 'autoparts-pro'); ?></option>
                        <option value="centered" <?php selected($settings['header_layout'], 'centered'); ?>><?php _e('Centered', 'autoparts-pro'); ?></option>
                        <option value="split" <?php selected($settings['header_layout'], 'split'); ?>><?php _e('Split', 'autoparts-pro'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Sticky Header', 'autoparts-pro'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="app_header_settings[sticky_header]" value="1" <?php checked($settings['sticky_header']); ?> />
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Transparent Header', 'autoparts-pro'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="app_header_settings[transparent_header]" value="1" <?php checked($settings['transparent_header']); ?> />
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Menu Style', 'autoparts-pro'); ?></th>
                <td>
                    <select name="app_header_settings[menu_style]">
                        <option value="gear-shift" <?php selected($settings['menu_style'], 'gear-shift'); ?>><?php _e('Gear Shift', 'autoparts-pro'); ?></option>
                        <option value="standard" <?php selected($settings['menu_style'], 'standard'); ?>><?php _e('Standard', 'autoparts-pro'); ?></option>
                        <option value="minimal" <?php selected($settings['menu_style'], 'minimal'); ?>><?php _e('Minimal', 'autoparts-pro'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Show Vehicle Selector', 'autoparts-pro'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="app_header_settings[show_vehicle_selector]" value="1" <?php checked($settings['show_vehicle_selector']); ?> />
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Show Wishlist Icon', 'autoparts-pro'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="app_header_settings[show_wishlist_icon]" value="1" <?php checked($settings['show_wishlist_icon']); ?> />
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Show Cart Icon', 'autoparts-pro'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="app_header_settings[show_cart_icon]" value="1" <?php checked($settings['show_cart_icon']); ?> />
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Show Search', 'autoparts-pro'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="app_header_settings[show_search]" value="1" <?php checked($settings['show_search']); ?> />
                    </label>
                </td>
            </tr>
        </table>
        <?php
    }
    
    private function render_footer_tab() {
        $settings = get_option('app_footer_settings', $this->get_default_footer_settings());
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Footer Layout', 'autoparts-pro'); ?></th>
                <td>
                    <select name="app_footer_settings[footer_layout]">
                        <option value="standard" <?php selected($settings['footer_layout'], 'standard'); ?>><?php _e('Standard', 'autoparts-pro'); ?></option>
                        <option value="minimal" <?php selected($settings['footer_layout'], 'minimal'); ?>><?php _e('Minimal', 'autoparts-pro'); ?></option>
                        <option value="extended" <?php selected($settings['footer_layout'], 'extended'); ?>><?php _e('Extended', 'autoparts-pro'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Show Newsletter', 'autoparts-pro'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="app_footer_settings[show_newsletter]" value="1" <?php checked($settings['show_newsletter']); ?> />
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Show Social Links', 'autoparts-pro'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="app_footer_settings[show_social_links]" value="1" <?php checked($settings['show_social_links']); ?> />
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Show Payment Icons', 'autoparts-pro'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="app_footer_settings[show_payment_icons]" value="1" <?php checked($settings['show_payment_icons']); ?> />
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Copyright Text', 'autoparts-pro'); ?></th>
                <td>
                    <textarea name="app_footer_settings[copyright_text]" rows="2" class="large-text"><?php echo esc_textarea($settings['copyright_text']); ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Back to Top Enabled', 'autoparts-pro'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="app_footer_settings[back_to_top_enabled]" value="1" <?php checked($settings['back_to_top_enabled']); ?> />
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Back to Top Icon', 'autoparts-pro'); ?></th>
                <td>
                    <select name="app_footer_settings[back_to_top_icon]">
                        <option value="arrow" <?php selected($settings['back_to_top_icon'], 'arrow'); ?>><?php _e('Arrow', 'autoparts-pro'); ?></option>
                        <option value="gear" <?php selected($settings['back_to_top_icon'], 'gear'); ?>><?php _e('Gear', 'autoparts-pro'); ?></option>
                        <option value="rocket" <?php selected($settings['back_to_top_icon'], 'rocket'); ?>><?php _e('Rocket', 'autoparts-pro'); ?></option>
                    </select>
                </td>
            </tr>
        </table>
        <?php
    }
    
    private function render_performance_tab() {
        $settings = get_option('app_performance_settings', $this->get_default_performance_settings());
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Lazy Load Images', 'autoparts-pro'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="app_performance_settings[lazy_load_images]" value="1" <?php checked($settings['lazy_load_images']); ?> />
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Lazy Load Iframes', 'autoparts-pro'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="app_performance_settings[lazy_load_iframes]" value="1" <?php checked($settings['lazy_load_iframes']); ?> />
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Minify CSS', 'autoparts-pro'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="app_performance_settings[minify_css]" value="1" <?php checked($settings['minify_css']); ?> />
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Minify JS', 'autoparts-pro'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="app_performance_settings[minify_js]" value="1" <?php checked($settings['minify_js']); ?> />
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Preload Critical Assets', 'autoparts-pro'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="app_performance_settings[preload_critical_assets]" value="1" <?php checked($settings['preload_critical_assets']); ?> />
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Disable Emojis', 'autoparts-pro'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="app_performance_settings[disable_emojis]" value="1" <?php checked($settings['disable_emojis']); ?> />
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Disable Embeds', 'autoparts-pro'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="app_performance_settings[disable_embeds]" value="1" <?php checked($settings['disable_embeds']); ?> />
                    </label>
                </td>
            </tr>
        </table>
        <?php
    }
}

// Initialize
Theme_Options::get_instance();
