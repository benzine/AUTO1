<?php
/**
 * Theme Options Panel - Main Class
 * 
 * @package AutoParts_Pro
 */

if (!defined('ABSPATH')) exit;

class AutoParts_Pro_Theme_Options {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
    }
    
    public function add_admin_menu() {
        add_theme_page(
            __('AutoParts Pro Options', 'autoparts-pro'),
            __('Theme Options', 'autoparts-pro'),
            'manage_options',
            'autoparts-pro-options',
            array($this, 'render_options_page')
        );
    }
    
    public function register_settings() {
        // General Settings
        register_setting('autoparts_pro_general', 'app_logo_light');
        register_setting('autoparts_pro_general', 'app_logo_dark');
        register_setting('autoparts_pro_general', 'app_sticky_logo');
        register_setting('autoparts_pro_general', 'app_mobile_logo');
        register_setting('autoparts_pro_general', 'app_site_tagline');
        
        // Colors
        register_setting('autoparts_pro_colors', 'app_primary_color');
        register_setting('autoparts_pro_colors', 'app_dark_color');
        register_setting('autoparts_pro_colors', 'app_accent_color');
        register_setting('autoparts_pro_colors', 'app_light_mode_bg');
        register_setting('autoparts_pro_colors', 'app_dark_mode_bg');
        
        // 3D Hero
        register_setting('autoparts_pro_hero', 'app_hero_model');
        register_setting('autoparts_pro_hero', 'app_hero_enabled');
        register_setting('autoparts_pro_hero', 'app_hero_scroll_distance');
        register_setting('autoparts_pro_hero', 'app_hero_labels');
        
        // Chatbot
        register_setting('autoparts_pro_chatbot', 'app_chatbot_enabled');
        register_setting('autoparts_pro_chatbot', 'app_chatbot_api_key');
        register_setting('autoparts_pro_chatbot', 'app_chatbot_avatar');
        register_setting('autoparts_pro_chatbot', 'app_chatbot_greeting');
        register_setting('autoparts_pro_chatbot', 'app_chatbot_auto_open');
        
        // Google Maps
        register_setting('autoparts_pro_maps', 'app_google_maps_api_key');
        register_setting('autoparts_pro_maps', 'app_store_locations');
        
        // UI Settings
        register_setting('autoparts_pro_ui', 'app_custom_cursor_enabled');
        register_setting('autoparts_pro_ui', 'app_back_to_top_icon');
        register_setting('autoparts_pro_ui', 'app_wishlist_enabled');
        register_setting('autoparts_pro_ui', 'app_quick_view_enabled');
    }
    
    public function enqueue_assets($hook) {
        if ('appearance_page_autoparts-pro-options' !== $hook) {
            return;
        }
        wp_enqueue_media();
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('autoparts-admin-options', AUTOPARTS_PRO_URI . '/assets/css/admin-options.css', array(), AUTOPARTS_PRO_VERSION);
        wp_enqueue_script('autoparts-admin-options', AUTOPARTS_PRO_URI . '/assets/js/admin-options.js', array('jquery', 'wp-color-picker'), AUTOPARTS_PRO_VERSION, true);
    }
    
    public function render_options_page() {
        ?>
        <div class="wrap autoparts-pro-options">
            <h1><?php echo esc_html__('AutoParts Pro Theme Options', 'autoparts-pro'); ?></h1>
            
            <nav class="nav-tab-wrapper">
                <a href="#general" class="nav-tab active"><?php esc_html_e('General', 'autoparts-pro'); ?></a>
                <a href="#colors" class="nav-tab"><?php esc_html_e('Colors', 'autoparts-pro'); ?></a>
                <a href="#hero" class="nav-tab"><?php esc_html_e('3D Hero', 'autoparts-pro'); ?></a>
                <a href="#chatbot" class="nav-tab"><?php esc_html_e('Chatbot', 'autoparts-pro'); ?></a>
                <a href="#maps" class="nav-tab"><?php esc_html_e('Google Maps', 'autoparts-pro'); ?></a>
                <a href="#ui" class="nav-tab"><?php esc_html_e('UI Settings', 'autoparts-pro'); ?></a>
            </nav>
            
            <form method="post" action="options.php">
                <?php settings_fields('autoparts_pro_general'); ?>
                <?php do_settings_sections('autoparts_pro_general'); ?>
                
                <div id="general" class="options-section active">
                    <h2><?php esc_html_e('General Settings', 'autoparts-pro'); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th><label><?php esc_html_e('Logo (Light Mode)', 'autoparts-pro'); ?></label></th>
                            <td>
                                <input type="text" name="app_logo_light" value="<?php echo esc_attr(get_option('app_logo_light')); ?>" class="regular-text" />
                                <button class="upload_image_button button"><?php esc_html_e('Upload', 'autoparts-pro'); ?></button>
                            </td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e('Logo (Dark Mode)', 'autoparts-pro'); ?></label></th>
                            <td>
                                <input type="text" name="app_logo_dark" value="<?php echo esc_attr(get_option('app_logo_dark')); ?>" class="regular-text" />
                                <button class="upload_image_button button"><?php esc_html_e('Upload', 'autoparts-pro'); ?></button>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div id="colors" class="options-section">
                    <h2><?php esc_html_e('Color Settings', 'autoparts-pro'); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th><label><?php esc_html_e('Primary Color', 'autoparts-pro'); ?></label></th>
                            <td>
                                <input type="text" name="app_primary_color" value="<?php echo esc_attr(get_option('app_primary_color', '#DC2626')); ?>" class="color-picker" />
                            </td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e('Accent Color', 'autoparts-pro'); ?></label></th>
                            <td>
                                <input type="text" name="app_accent_color" value="<?php echo esc_attr(get_option('app_accent_color', '#EA580C')); ?>" class="color-picker" />
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div id="hero" class="options-section">
                    <h2><?php esc_html_e('3D Hero Settings', 'autoparts-pro'); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th><label><?php esc_html_e('Enable 3D Hero', 'autoparts-pro'); ?></label></th>
                            <td>
                                <input type="checkbox" name="app_hero_enabled" value="1" <?php checked(get_option('app_hero_enabled', true)); ?> />
                            </td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e('3D Model URL', 'autoparts-pro'); ?></label></th>
                            <td>
                                <input type="text" name="app_hero_model" value="<?php echo esc_attr(get_option('app_hero_model')); ?>" class="regular-text" />
                                <button class="upload_model_button button"><?php esc_html_e('Upload Model', 'autoparts-pro'); ?></button>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div id="chatbot" class="options-section">
                    <h2><?php esc_html_e('AI Chatbot Settings', 'autoparts-pro'); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th><label><?php esc_html_e('Enable Chatbot', 'autoparts-pro'); ?></label></th>
                            <td>
                                <input type="checkbox" name="app_chatbot_enabled" value="1" <?php checked(get_option('app_chatbot_enabled', true)); ?> />
                            </td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e('API Key', 'autoparts-pro'); ?></label></th>
                            <td>
                                <input type="password" name="app_chatbot_api_key" value="<?php echo esc_attr(get_option('app_chatbot_api_key')); ?>" class="regular-text" />
                            </td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e('Greeting Message', 'autoparts-pro'); ?></label></th>
                            <td>
                                <textarea name="app_chatbot_greeting" rows="3" class="large-text"><?php echo esc_textarea(get_option('app_chatbot_greeting', 'Hello! How can I help you find the right parts today?')); ?></textarea>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div id="maps" class="options-section">
                    <h2><?php esc_html_e('Google Maps Settings', 'autoparts-pro'); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th><label><?php esc_html_e('API Key', 'autoparts-pro'); ?></label></th>
                            <td>
                                <input type="password" name="app_google_maps_api_key" value="<?php echo esc_attr(get_option('app_google_maps_api_key')); ?>" class="regular-text" />
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div id="ui" class="options-section">
                    <h2><?php esc_html_e('UI Settings', 'autoparts-pro'); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th><label><?php esc_html_e('Enable Custom Cursor', 'autoparts-pro'); ?></label></th>
                            <td>
                                <input type="checkbox" name="app_custom_cursor_enabled" value="1" <?php checked(get_option('app_custom_cursor_enabled', true)); ?> />
                            </td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e('Enable Wishlist', 'autoparts-pro'); ?></label></th>
                            <td>
                                <input type="checkbox" name="app_wishlist_enabled" value="1" <?php checked(get_option('app_wishlist_enabled', true)); ?> />
                            </td>
                        </tr>
                    </table>
                </div>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}

AutoParts_Pro_Theme_Options::get_instance();
