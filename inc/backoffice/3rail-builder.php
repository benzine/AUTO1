<?php
/**
 * AutoParts Pro 3-Rail Headless Backoffice
 * 
 * @package AutoParts_Pro
 * @since 1.0.0
 */

namespace AutoPartsPro\Backoffice;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 3-Rail Backoffice Interface Class
 */
class Backoffice_Interface {
    
    private static $instance = null;
    private $modules = [];
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_modules();
        add_action('admin_menu', [$this, 'add_backoffice_page']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_ajax_app_save_section', [$this, 'ajax_save_section']);
        add_action('wp_ajax_app_delete_section', [$this, 'ajax_delete_section']);
        add_action('wp_ajax_app_duplicate_section', [$this, 'ajax_duplicate_section']);
        add_action('wp_ajax_app_reorder_sections', [$this, 'ajax_reorder_sections']);
        add_action('wp_ajax_app_get_section', [$this, 'ajax_get_section']);
        add_action('wp_ajax_app_reset_settings', [$this, 'ajax_reset_settings']);
    }
    
    /**
     * Initialize module library
     */
    private function init_modules() {
        $this->modules = [
            'text' => [
                'name' => __('Text', 'autoparts-pro'),
                'icon' => 'dashicons-editor-paragraph',
                'category' => 'basic',
            ],
            'heading' => [
                'name' => __('Heading', 'autoparts-pro'),
                'icon' => 'dashicons-editor-textcolor',
                'category' => 'basic',
            ],
            'button' => [
                'name' => __('Button', 'autoparts-pro'),
                'icon' => 'dashicons-button',
                'category' => 'basic',
            ],
            'image' => [
                'name' => __('Image', 'autoparts-pro'),
                'icon' => 'dashicons-format-image',
                'category' => 'media',
            ],
            'video' => [
                'name' => __('Video', 'autoparts-pro'),
                'icon' => 'dashicons-video-alt3',
                'category' => 'media',
            ],
            'icon' => [
                'name' => __('Icon', 'autoparts-pro'),
                'icon' => 'dashicons-star-filled',
                'category' => 'basic',
            ],
            'divider' => [
                'name' => __('Divider', 'autoparts-pro'),
                'icon' => 'dashicons-minus',
                'category' => 'layout',
            ],
            'spacer' => [
                'name' => __('Spacer', 'autoparts-pro'),
                'icon' => 'dashicons-arrow-up-down',
                'category' => 'layout',
            ],
            'form' => [
                'name' => __('Form', 'autoparts-pro'),
                'icon' => 'dashicons-feedback',
                'category' => 'advanced',
            ],
            'testimonials' => [
                'name' => __('Testimonials', 'autoparts-pro'),
                'icon' => 'dashicons-format-quote',
                'category' => 'content',
            ],
            'team' => [
                'name' => __('Team', 'autoparts-pro'),
                'icon' => 'dashicons-groups',
                'category' => 'content',
            ],
            'pricing' => [
                'name' => __('Pricing', 'autoparts-pro'),
                'icon' => 'dashicons-cart',
                'category' => 'woocommerce',
            ],
            'blog' => [
                'name' => __('Blog Posts', 'autoparts-pro'),
                'icon' => 'dashicons-admin-post',
                'category' => 'content',
            ],
            'accordion' => [
                'name' => __('Accordion', 'autoparts-pro'),
                'icon' => 'dashicons-arrow-down-alt2',
                'category' => 'advanced',
            ],
            'features' => [
                'name' => __('Features', 'autoparts-pro'),
                'icon' => 'dashicons-list-view',
                'category' => 'content',
            ],
            'stats' => [
                'name' => __('Stats', 'autoparts-pro'),
                'icon' => 'dashicons-chart-bar',
                'category' => 'content',
            ],
            'progress_bars' => [
                'name' => __('Progress Bars', 'autoparts-pro'),
                'icon' => 'dashicons-menu',
                'category' => 'content',
            ],
            'timeline' => [
                'name' => __('Timeline', 'autoparts-pro'),
                'icon' => 'dashicons-clock',
                'category' => 'content',
            ],
            'logo_carousel' => [
                'name' => __('Logo Carousel', 'autoparts-pro'),
                'icon' => 'dashicons-images-alt2',
                'category' => 'advanced',
            ],
            'social_feed' => [
                'name' => __('Social Feed', 'autoparts-pro'),
                'icon' => 'dashicons-share',
                'category' => 'advanced',
            ],
            'search' => [
                'name' => __('Search', 'autoparts-pro'),
                'icon' => 'dashicons-search',
                'category' => 'basic',
            ],
            'breadcrumbs' => [
                'name' => __('Breadcrumbs', 'autoparts-pro'),
                'icon' => 'dashicons-networking',
                'category' => 'navigation',
            ],
            'custom_html' => [
                'name' => __('Custom HTML', 'autoparts-pro'),
                'icon' => 'dashicons-code-standards',
                'category' => 'advanced',
            ],
            'product_grid' => [
                'name' => __('Product Grid', 'autoparts-pro'),
                'icon' => 'dashicons-grid-view',
                'category' => 'woocommerce',
            ],
        ];
    }
    
    /**
     * Add backoffice page to admin menu
     */
    public function add_backoffice_page() {
        add_theme_page(
            __('3-Rail Builder', 'autoparts-pro'),
            __('3-Rail Builder', 'autoparts-pro'),
            'manage_options',
            'autoparts-pro-builder',
            [$this, 'render_builder_page']
        );
    }
    
    /**
     * Enqueue builder assets
     */
    public function enqueue_assets($hook) {
        if ('appearance_page_autoparts-pro-builder' !== $hook) {
            return;
        }
        
        // Builder CSS
        wp_enqueue_style(
            'app-builder-css',
            get_template_directory_uri() . '/assets/css/builder.css',
            ['wp-color-picker'],
            APP_VERSION
        );
        
        // Builder JS dependencies
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_media();
        
        // Builder main script
        wp_enqueue_script(
            'app-builder-js',
            get_template_directory_uri() . '/assets/js/builder.js',
            ['jquery', 'jquery-ui-sortable', 'wp-color-picker'],
            APP_VERSION,
            true
        );
        
        // Localize script with data
        wp_localize_script('app-builder-js', 'appBuilderData', [
            'nonce' => wp_create_nonce('app_builder_nonce'),
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'siteUrl' => home_url(),
            'templateUrl' => get_template_directory_uri(),
            'modules' => $this->modules,
            'strings' => [
                'saveSuccess' => __('Changes saved successfully!', 'autoparts-pro'),
                'saveError' => __('Error saving changes.', 'autoparts-pro'),
                'confirmDelete' => __('Are you sure you want to delete this section?', 'autoparts-pro'),
                'unsavedChanges' => __('You have unsaved changes. Discard them?', 'autoparts-pro'),
                'sectionAdded' => __('Section added', 'autoparts-pro'),
                'sectionDeleted' => __('Section deleted', 'autoparts-pro'),
                'sectionDuplicated' => __('Section duplicated', 'autoparts-pro'),
            ],
            'settings' => [
                'general' => get_option('app_general_settings', []),
                'hero' => get_option('app_hero_settings', []),
                'header' => get_option('app_header_settings', []),
                'footer' => get_option('app_footer_settings', []),
            ],
        ]);
    }
    
    /**
     * Render the builder page
     */
    public function render_builder_page() {
        $page_id = isset($_GET['page_id']) ? absint($_GET['page_id']) : 0;
        $sections = get_option('app_page_sections_' . $page_id, []);
        ?>
        <div class="app-builder-wrapper" data-page-id="<?php echo esc_attr($page_id); ?>">
            <!-- Left Rail -->
            <div class="app-builder-left-rail">
                <div class="rail-header">
                    <h2><?php _e('Sections & Modules', 'autoparts-pro'); ?></h2>
                    <div class="rail-actions">
                        <button type="button" id="app-add-section" class="button button-primary">
                            <span class="dashicons dashicons-plus-alt"></span>
                            <?php _e('Add Section', 'autoparts-pro'); ?>
                        </button>
                    </div>
                </div>
                
                <div class="sections-list" id="app-sections-list">
                    <?php if (!empty($sections)) : ?>
                        <?php foreach ($sections as $index => $section) : ?>
                            <div class="section-item" data-section-id="<?php echo esc_attr($section['id']); ?>" data-index="<?php echo esc_attr($index); ?>">
                                <div class="section-handle">
                                    <span class="dashicons dashicons-menu"></span>
                                </div>
                                <div class="section-info">
                                    <span class="section-name"><?php echo esc_html($section['name']); ?></span>
                                    <span class="section-type"><?php echo esc_html($section['type']); ?></span>
                                </div>
                                <div class="section-actions">
                                    <button type="button" class="button-icon edit-section" title="<?php esc_attr_e('Edit', 'autoparts-pro'); ?>">
                                        <span class="dashicons dashicons-edit"></span>
                                    </button>
                                    <button type="button" class="button-icon duplicate-section" title="<?php esc_attr_e('Duplicate', 'autoparts-pro'); ?>">
                                        <span class="dashicons dashicons-admin-page"></span>
                                    </button>
                                    <button type="button" class="button-icon delete-section" title="<?php esc_attr_e('Delete', 'autoparts-pro'); ?>">
                                        <span class="dashicons dashicons-trash"></span>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-sections">
                            <p><?php _e('No sections yet. Click "Add Section" to start building.', 'autoparts-pro'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="modules-library">
                    <h3><?php _e('Module Library', 'autoparts-pro'); ?></h3>
                    <div class="module-search">
                        <input type="text" id="app-module-search" placeholder="<?php esc_attr_e('Search modules...', 'autoparts-pro'); ?>" />
                    </div>
                    <div class="modules-grid" id="app-modules-grid">
                        <?php foreach ($this->modules as $key => $module) : ?>
                            <div class="module-item" data-module="<?php echo esc_attr($key); ?>" data-category="<?php echo esc_attr($module['category']); ?>">
                                <span class="module-icon dashicons <?php echo esc_attr($module['icon']); ?>"></span>
                                <span class="module-name"><?php echo esc_html($module['name']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="rail-footer">
                    <div class="accessibility-panel">
                        <h4><?php _e('Accessibility', 'autoparts-pro'); ?></h4>
                        <div class="accessibility-controls">
                            <button type="button" class="button" data-action="font-decrease">A-</button>
                            <button type="button" class="button" data-action="font-reset">A</button>
                            <button type="button" class="button" data-action="font-increase">A+</button>
                            <label>
                                <input type="checkbox" id="app-high-contrast" />
                                <?php _e('High Contrast', 'autoparts-pro'); ?>
                            </label>
                            <label>
                                <input type="checkbox" id="app-reduced-motion" />
                                <?php _e('Reduced Motion', 'autoparts-pro'); ?>
                            </label>
                        </div>
                    </div>
                    
                    <div class="builder-actions">
                        <button type="button" id="app-undo" class="button" disabled>
                            <span class="dashicons dashicons-undo"></span>
                            <?php _e('Undo', 'autoparts-pro'); ?>
                        </button>
                        <button type="button" id="app-redo" class="button" disabled>
                            <span class="dashicons dashicons-redo"></span>
                            <?php _e('Redo', 'autoparts-pro'); ?>
                        </button>
                        <button type="button" id="app-save-builder" class="button button-primary button-large">
                            <span class="dashicons dashicons-yes"></span>
                            <?php _e('Save All', 'autoparts-pro'); ?>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Center Stage (Canvas) -->
            <div class="app-builder-center-stage">
                <div class="stage-toolbar">
                    <div class="stage-title">
                        <h2><?php echo $page_id ? get_the_title($page_id) : __('Homepage', 'autoparts-pro'); ?></h2>
                    </div>
                    <div class="device-preview">
                        <button type="button" class="device-btn active" data-device="desktop" title="<?php esc_attr_e('Desktop', 'autoparts-pro'); ?>">
                            <span class="dashicons dashicons-desktop"></span>
                        </button>
                        <button type="button" class="device-btn" data-device="tablet" title="<?php esc_attr_e('Tablet', 'autoparts-pro'); ?>">
                            <span class="dashicons dashicons-tablet"></span>
                        </button>
                        <button type="button" class="device-btn" data-device="mobile" title="<?php esc_attr_e('Mobile', 'autoparts-pro'); ?>">
                            <span class="dashicons dashicons-smartphone"></span>
                        </button>
                    </div>
                    <div class="view-actions">
                        <a href="<?php echo $page_id ? get_permalink($page_id) : home_url(); ?>" target="_blank" class="button">
                            <span class="dashicons dashicons-external"></span>
                            <?php _e('View Page', 'autoparts-pro'); ?>
                        </a>
                    </div>
                </div>
                
                <div class="stage-canvas-wrapper" id="app-canvas-wrapper">
                    <iframe id="app-builder-canvas" src="<?php echo admin_url('admin-ajax.php?action=app_render_canvas&page_id=' . $page_id . '&nonce=' . wp_create_nonce('app_render_canvas')); ?>"></iframe>
                </div>
            </div>
            
            <!-- Right Rail (Property Inspector) -->
            <div class="app-builder-right-rail">
                <div class="rail-header">
                    <h2><?php _e('Properties', 'autoparts-pro'); ?></h2>
                    <button type="button" class="button close-rail">
                        <span class="dashicons dashicons-no"></span>
                    </button>
                </div>
                
                <div class="property-tabs">
                    <button type="button" class="tab-btn active" data-tab="layout">
                        <?php _e('Layout', 'autoparts-pro'); ?>
                    </button>
                    <button type="button" class="tab-btn" data-tab="style">
                        <?php _e('Style', 'autoparts-pro'); ?>
                    </button>
                    <button type="button" class="tab-btn" data-tab="advanced">
                        <?php _e('Advanced', 'autoparts-pro'); ?>
                    </button>
                </div>
                
                <div class="properties-content" id="app-properties-content">
                    <div class="property-panel" id="panel-layout">
                        <div class="property-group">
                            <label><?php _e('Display', 'autoparts-pro'); ?></label>
                            <select name="display">
                                <option value="block"><?php _e('Block', 'autoparts-pro'); ?></option>
                                <option value="flex"><?php _e('Flex', 'autoparts-pro'); ?></option>
                                <option value="grid"><?php _e('Grid', 'autoparts-pro'); ?></option>
                                <option value="none"><?php _e('None', 'autoparts-pro'); ?></option>
                            </select>
                        </div>
                        
                        <div class="property-group">
                            <label><?php _e('Width', 'autoparts-pro'); ?></label>
                            <div class="dimension-input">
                                <input type="text" name="width" placeholder="auto" />
                                <select name="width_unit">
                                    <option value="px">px</option>
                                    <option value="%">%</option>
                                    <option value="vw">vw</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="property-group">
                            <label><?php _e('Height', 'autoparts-pro'); ?></label>
                            <div class="dimension-input">
                                <input type="text" name="height" placeholder="auto" />
                                <select name="height_unit">
                                    <option value="px">px</option>
                                    <option value="%">%</option>
                                    <option value="vh">vh</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="property-group">
                            <label><?php _e('Padding', 'autoparts-pro'); ?></label>
                            <div class="dimension-inputs">
                                <input type="number" name="padding_top" placeholder="Top" />
                                <input type="number" name="padding_right" placeholder="Right" />
                                <input type="number" name="padding_bottom" placeholder="Bottom" />
                                <input type="number" name="padding_left" placeholder="Left" />
                            </div>
                        </div>
                        
                        <div class="property-group">
                            <label><?php _e('Margin', 'autoparts-pro'); ?></label>
                            <div class="dimension-inputs">
                                <input type="number" name="margin_top" placeholder="Top" />
                                <input type="number" name="margin_right" placeholder="Right" />
                                <input type="number" name="margin_bottom" placeholder="Bottom" />
                                <input type="number" name="margin_left" placeholder="Left" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="property-panel" id="panel-style" style="display:none;">
                        <div class="property-group">
                            <label><?php _e('Background Color', 'autoparts-pro'); ?></label>
                            <input type="text" name="background_color" class="app-color-picker" />
                        </div>
                        
                        <div class="property-group">
                            <label><?php _e('Background Image', 'autoparts-pro'); ?></label>
                            <div class="image-upload">
                                <img src="" alt="" class="preview-image" />
                                <button type="button" class="button upload-btn"><?php _e('Upload', 'autoparts-pro'); ?></button>
                                <button type="button" class="button remove-btn"><?php _e('Remove', 'autoparts-pro'); ?></button>
                            </div>
                        </div>
                        
                        <div class="property-group">
                            <label><?php _e('Border Radius', 'autoparts-pro'); ?></label>
                            <div class="dimension-inputs">
                                <input type="number" name="border_radius_tl" placeholder="TL" />
                                <input type="number" name="border_radius_tr" placeholder="TR" />
                                <input type="number" name="border_radius_br" placeholder="BR" />
                                <input type="number" name="border_radius_bl" placeholder="BL" />
                            </div>
                        </div>
                        
                        <div class="property-group">
                            <label><?php _e('Box Shadow', 'autoparts-pro'); ?></label>
                            <div class="shadow-controls">
                                <input type="number" name="shadow_x" placeholder="X" />
                                <input type="number" name="shadow_y" placeholder="Y" />
                                <input type="number" name="shadow_blur" placeholder="Blur" />
                                <input type="text" name="shadow_color" class="app-color-picker" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="property-panel" id="panel-advanced" style="display:none;">
                        <div class="property-group">
                            <label><?php _e('CSS Classes', 'autoparts-pro'); ?></label>
                            <input type="text" name="css_classes" />
                        </div>
                        
                        <div class="property-group">
                            <label><?php _e('Custom CSS', 'autoparts-pro'); ?></label>
                            <textarea name="custom_css" rows="5"></textarea>
                        </div>
                        
                        <div class="property-group">
                            <label><?php _e('Animation', 'autoparts-pro'); ?></label>
                            <select name="animation">
                                <option value=""><?php _e('None', 'autoparts-pro'); ?></option>
                                <option value="fade-in"><?php _e('Fade In', 'autoparts-pro'); ?></option>
                                <option value="slide-up"><?php _e('Slide Up', 'autoparts-pro'); ?></option>
                                <option value="slide-down"><?php _e('Slide Down', 'autoparts-pro'); ?></option>
                                <option value="zoom-in"><?php _e('Zoom In', 'autoparts-pro'); ?></option>
                            </select>
                        </div>
                        
                        <div class="property-group">
                            <label><?php _e('Animation Delay (ms)', 'autoparts-pro'); ?></label>
                            <input type="number" name="animation_delay" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Add Section Modal -->
        <div id="app-add-section-modal" class="app-modal" style="display:none;">
            <div class="modal-overlay"></div>
            <div class="modal-content">
                <div class="modal-header">
                    <h3><?php _e('Add New Section', 'autoparts-pro'); ?></h3>
                    <button type="button" class="modal-close">
                        <span class="dashicons dashicons-no"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="section-templates">
                        <?php
                        $templates = [
                            'hero' => __('Hero Section', 'autoparts-pro'),
                            'features' => __('Features Grid', 'autoparts-pro'),
                            'products' => __('Product Showcase', 'autoparts-pro'),
                            'testimonials' => __('Testimonials', 'autoparts-pro'),
                            'cta' => __('Call to Action', 'autoparts-pro'),
                            'blog' => __('Blog Posts', 'autoparts-pro'),
                            'brands' => __('Brand Logos', 'autoparts-pro'),
                            'contact' => __('Contact Form', 'autoparts-pro'),
                            'map' => __('Store Locator', 'autoparts-pro'),
                            'footer' => __('Footer', 'autoparts-pro'),
                        ];
                        foreach ($templates as $key => $name) :
                        ?>
                            <div class="template-card" data-template="<?php echo esc_attr($key); ?>">
                                <div class="template-preview">
                                    <span class="dashicons dashicons-<?php echo $key === 'hero' ? 'flag' : ($key === 'products' ? 'grid-view' : 'admin-page'); ?>"></span>
                                </div>
                                <div class="template-name"><?php echo esc_html($name); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="button modal-cancel"><?php _e('Cancel', 'autoparts-pro'); ?></button>
                    <button type="button" class="button button-primary modal-confirm"><?php _e('Add Section', 'autoparts-pro'); ?></button>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * AJAX: Save section
     */
    public function ajax_save_section() {
        check_ajax_referer('app_builder_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'autoparts-pro')]);
        }
        
        $page_id = isset($_POST['page_id']) ? absint($_POST['page_id']) : 0;
        $section_data = isset($_POST['section']) ? $_POST['section'] : [];
        
        if (empty($section_data)) {
            wp_send_json_error(['message' => __('No section data', 'autoparts-pro')]);
        }
        
        $sections = get_option('app_page_sections_' . $page_id, []);
        
        if (isset($section_data['id'])) {
            // Update existing section
            foreach ($sections as $index => $section) {
                if ($section['id'] === $section_data['id']) {
                    $sections[$index] = $section_data;
                    break;
                }
            }
        } else {
            // Add new section
            $section_data['id'] = 'section_' . uniqid();
            $section_data['created'] = current_time('mysql');
            $sections[] = $section_data;
        }
        
        update_option('app_page_sections_' . $page_id, $sections);
        
        wp_send_json_success([
            'message' => __('Section saved', 'autoparts-pro'),
            'section' => $section_data,
        ]);
    }
    
    /**
     * AJAX: Delete section
     */
    public function ajax_delete_section() {
        check_ajax_referer('app_builder_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'autoparts-pro')]);
        }
        
        $page_id = isset($_POST['page_id']) ? absint($_POST['page_id']) : 0;
        $section_id = isset($_POST['section_id']) ? sanitize_text_field($_POST['section_id']) : '';
        
        if (empty($section_id)) {
            wp_send_json_error(['message' => __('No section ID', 'autoparts-pro')]);
        }
        
        $sections = get_option('app_page_sections_' . $page_id, []);
        $sections = array_filter($sections, function($section) use ($section_id) {
            return $section['id'] !== $section_id;
        });
        $sections = array_values($sections);
        
        update_option('app_page_sections_' . $page_id, $sections);
        
        wp_send_json_success(['message' => __('Section deleted', 'autoparts-pro')]);
    }
    
    /**
     * AJAX: Duplicate section
     */
    public function ajax_duplicate_section() {
        check_ajax_referer('app_builder_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'autoparts-pro')]);
        }
        
        $page_id = isset($_POST['page_id']) ? absint($_POST['page_id']) : 0;
        $section_id = isset($_POST['section_id']) ? sanitize_text_field($_POST['section_id']) : '';
        
        $sections = get_option('app_page_sections_' . $page_id, []);
        
        foreach ($sections as $index => $section) {
            if ($section['id'] === $section_id) {
                $duplicate = $section;
                $duplicate['id'] = 'section_' . uniqid();
                $duplicate['name'] = $section['name'] . ' (Copy)';
                $duplicate['created'] = current_time('mysql');
                
                array_splice($sections, $index + 1, 0, [$duplicate]);
                break;
            }
        }
        
        update_option('app_page_sections_' . $page_id, $sections);
        
        wp_send_json_success(['message' => __('Section duplicated', 'autoparts-pro')]);
    }
    
    /**
     * AJAX: Reorder sections
     */
    public function ajax_reorder_sections() {
        check_ajax_referer('app_builder_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'autoparts-pro')]);
        }
        
        $page_id = isset($_POST['page_id']) ? absint($_POST['page_id']) : 0;
        $order = isset($_POST['order']) ? $_POST['order'] : [];
        
        if (empty($order)) {
            wp_send_json_error(['message' => __('No order data', 'autoparts-pro')]);
        }
        
        $sections = get_option('app_page_sections_' . $page_id, []);
        $new_sections = [];
        
        foreach ($order as $section_id) {
            foreach ($sections as $section) {
                if ($section['id'] === $section_id) {
                    $new_sections[] = $section;
                    break;
                }
            }
        }
        
        update_option('app_page_sections_' . $page_id, $new_sections);
        
        wp_send_json_success(['message' => __('Sections reordered', 'autoparts-pro')]);
    }
    
    /**
     * AJAX: Get section
     */
    public function ajax_get_section() {
        check_ajax_referer('app_builder_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'autoparts-pro')]);
        }
        
        $page_id = isset($_POST['page_id']) ? absint($_POST['page_id']) : 0;
        $section_id = isset($_POST['section_id']) ? sanitize_text_field($_POST['section_id']) : '';
        
        $sections = get_option('app_page_sections_' . $page_id, []);
        
        foreach ($sections as $section) {
            if ($section['id'] === $section_id) {
                wp_send_json_success(['section' => $section]);
            }
        }
        
        wp_send_json_error(['message' => __('Section not found', 'autoparts-pro')]);
    }
    
    /**
     * AJAX: Reset settings
     */
    public function ajax_reset_settings() {
        check_ajax_referer('app_builder_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'autoparts-pro')]);
        }
        
        $settings_type = isset($_POST['settings_type']) ? sanitize_text_field($_POST['settings_type']) : 'all';
        
        if ($settings_type === 'all') {
            delete_option('app_general_settings');
            delete_option('app_hero_settings');
            delete_option('app_chatbot_settings');
            delete_option('app_maps_settings');
            delete_option('app_cursor_settings');
            delete_option('app_header_settings');
            delete_option('app_footer_settings');
            delete_option('app_performance_settings');
        }
        
        wp_send_json_success(['message' => __('Settings reset to defaults', 'autoparts-pro')]);
    }
}

// Initialize
Backoffice_Interface::get_instance();
