<?php
/**
 * 3-Rail Page Builder - Main Class
 */
if (!defined('ABSPATH')) exit;

class AutoParts_Pro_Page_Builder {
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) self::$instance = new self();
        return self::$instance;
    }
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_builder_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('wp_ajax_app_save_section', array($this, 'ajax_save_section'));
        add_action('wp_ajax_app_delete_section', array($this, 'ajax_delete_section'));
        add_action('wp_ajax_app_duplicate_section', array($this, 'ajax_duplicate_section'));
        add_action('wp_ajax_app_reorder_sections', array($this, 'ajax_reorder_sections'));
        add_action('wp_ajax_app_get_section', array($this, 'ajax_get_section'));
    }
    
    public function add_builder_menu() {
        add_theme_page(__('Page Builder', 'autoparts-pro'), __('Page Builder', 'autoparts-pro'), 'edit_pages', 'autoparts-pro-builder', array($this, 'render_interface'));
    }
    
    public function enqueue_assets($hook) {
        if ('appearance_page_autoparts-pro-builder' !== $hook) return;
        wp_enqueue_media();
        wp_enqueue_style('autoparts-builder', AUTOPARTS_PRO_URI . '/assets/css/builder.css', array(), AUTOPARTS_PRO_VERSION);
        wp_enqueue_script('autoparts-builder', AUTOPARTS_PRO_URI . '/assets/js/builder.js', array('jquery', 'jquery-ui-sortable'), AUTOPARTS_PRO_VERSION, true);
        wp_localize_script('autoparts-builder', 'appBuilderData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('app_builder_nonce'),
            'modules' => $this->get_modules(),
            'strings' => array('save' => __('Save'), 'delete' => __('Delete'), 'duplicate' => __('Duplicate'), 'sectionSaved' => __('Section saved'), 'confirmDelete' => __('Are you sure?'))
        ));
    }
    
    public function get_modules() {
        return array(
            'text' => array('name' => __('Text'), 'icon' => 'dashicons-editor-paragraph', 'category' => 'basic'),
            'heading' => array('name' => __('Heading'), 'icon' => 'dashicons-editor-textcolor', 'category' => 'basic'),
            'button' => array('name' => __('Button'), 'icon' => 'dashicons-button', 'category' => 'basic'),
            'image' => array('name' => __('Image'), 'icon' => 'dashicons-format-image', 'category' => 'media'),
            'video' => array('name' => __('Video'), 'icon' => 'dashicons-video-alt3', 'category' => 'media'),
            'form' => array('name' => __('Form'), 'icon' => 'dashicons-feedback', 'category' => 'advanced'),
            'product-grid' => array('name' => __('Product Grid'), 'icon' => 'dashicons-grid-view', 'category' => 'commerce'),
            'flash-deals' => array('name' => __('Flash Deals'), 'icon' => 'dashicons-lightbulb', 'category' => 'commerce'),
            'testimonials' => array('name' => __('Testimonials'), 'icon' => 'dashicons-format-quote', 'category' => 'content'),
            'features' => array('name' => __('Features'), 'icon' => 'dashicons-yes-alt', 'category' => 'content'),
        );
    }
    
    public function render_interface() {
        $page_id = isset($_GET['page_id']) ? absint($_GET['page_id']) : 0;
        ?>
        <div class="app-builder-wrap" data-page-id="<?php echo esc_attr($page_id); ?>">
            <div class="app-builder-left-rail">
                <div class="builder-rail-header"><h2><?php _e('Sections & Modules'); ?></h2></div>
                <div class="sections-list" id="sectionsList"></div>
                <div class="module-library">
                    <h3><?php _e('Module Library'); ?></h3>
                    <input type="text" class="module-search" placeholder="<?php _e('Search...'); ?>" />
                    <div class="module-categories">
                        <button class="category-btn active" data-category="all"><?php _e('All'); ?></button>
                        <button class="category-btn" data-category="basic"><?php _e('Basic'); ?></button>
                        <button class="category-btn" data-category="media"><?php _e('Media'); ?></button>
                        <button class="category-btn" data-category="content"><?php _e('Content'); ?></button>
                        <button class="category-btn" data-category="commerce"><?php _e('Commerce'); ?></button>
                    </div>
                    <div class="modules-grid">
                        <?php foreach ($this->get_modules() as $id => $mod): ?>
                        <div class="module-item" data-module="<?php echo esc_attr($id); ?>" data-category="<?php echo esc_attr($mod['category']); ?>">
                            <span class="dashicons <?php echo esc_attr($mod['icon']); ?>"></span>
                            <span class="module-name"><?php echo esc_html($mod['name']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="accessibility-panel">
                    <h3><?php _e('Accessibility'); ?></h3>
                    <button class="a11y-btn" data-action="font-decrease">A-</button>
                    <button class="a11y-btn" data-action="font-reset">A</button>
                    <button class="a11y-btn" data-action="font-increase">A+</button>
                    <button class="a11y-btn" data-action="high-contrast"><?php _e('High Contrast'); ?></button>
                </div>
            </div>
            <div class="app-builder-center-stage">
                <div class="stage-toolbar">
                    <button class="back-to-full-page"><?php _e('Back to Full Page'); ?></button>
                    <div class="device-preview">
                        <button class="device-btn active" data-device="desktop"><span class="dashicons dashicons-desktop"></span></button>
                        <button class="device-btn" data-device="tablet"><span class="dashicons dashicons-tablet"></span></button>
                        <button class="device-btn" data-device="mobile"><span class="dashicons dashicons-smartphone"></span></button>
                    </div>
                    <div class="section-toolbar">
                        <input type="text" class="section-name-input" placeholder="<?php _e('Section Name'); ?>" />
                        <button class="toggle-visibility"><span class="dashicons dashicons-visibility"></span></button>
                        <button class="duplicate-section"><span class="dashicons dashicons-admin-copy"></span></button>
                        <button class="delete-section"><span class="dashicons dashicons-trash"></span></button>
                        <button class="save-section button-primary"><?php _e('Save Section'); ?></button>
                    </div>
                </div>
                <div class="live-canvas"><iframe id="builderPreview" src="<?php echo esc_url(home_url('/?app_builder_preview=1')); ?>"></iframe></div>
            </div>
            <div class="app-builder-right-rail">
                <div class="builder-rail-header"><h2><?php _e('Properties'); ?></h2></div>
                <div class="property-tabs">
                    <button class="tab-btn active" data-tab="layout"><?php _e('Layout'); ?></button>
                    <button class="tab-btn" data-tab="style"><?php _e('Style'); ?></button>
                    <button class="tab-btn" data-tab="advanced"><?php _e('Advanced'); ?></button>
                </div>
                <div class="property-panels">
                    <div class="panel active" id="layout-panel">
                        <div class="property-group"><label><?php _e('Display'); ?></label><select class="property-control" data-property="display"><option value="block">Block</option><option value="flex">Flex</option><option value="grid">Grid</option></select></div>
                        <div class="property-group"><label><?php _e('Width'); ?></label><input type="text" class="property-control" data-property="width" placeholder="100%" /></div>
                        <div class="property-group"><label><?php _e('Height'); ?></label><input type="text" class="property-control" data-property="height" placeholder="auto" /></div>
                    </div>
                    <div class="panel" id="style-panel">
                        <div class="property-group"><label><?php _e('Background Color'); ?></label><input type="text" class="property-control color-picker" data-property="backgroundColor" /></div>
                        <div class="property-group"><label><?php _e('Padding'); ?></label><div class="spacing-control"><input type="text" class="spacing-input" data-side="top" placeholder="Top" /><input type="text" class="spacing-input" data-side="right" placeholder="Right" /><input type="text" class="spacing-input" data-side="bottom" placeholder="Bottom" /><input type="text" class="spacing-input" data-side="left" placeholder="Left" /></div></div>
                    </div>
                    <div class="panel" id="advanced-panel">
                        <div class="property-group"><label><?php _e('Animation'); ?></label><select class="property-control" data-property="animation"><option value="none">None</option><option value="fade-in">Fade In</option><option value="slide-up">Slide Up</option></select></div>
                        <div class="property-group"><label><?php _e('Custom CSS Class'); ?></label><input type="text" class="property-control" data-property="customClass" /></div>
                    </div>
                </div>
                <div class="undo-redo-controls">
                    <button class="undo-btn" disabled><span class="dashicons dashicons-undo"></span></button>
                    <button class="redo-btn" disabled><span class="dashicons dashicons-redo"></span></button>
                    <span class="history-count">0/50</span>
                </div>
            </div>
        </div>
        <?php
    }
    
    public function ajax_save_section() {
        check_ajax_referer('app_builder_nonce', 'nonce');
        $section_data = isset($_POST['section_data']) ? json_decode(stripslashes($_POST['section_data']), true) : array();
        $page_id = isset($_POST['page_id']) ? absint($_POST['page_id']) : 0;
        if (empty($section_data)) wp_send_json_error(array('message' => __('No data')));
        update_post_meta($page_id, '_app_section_' . sanitize_key($section_data['id']), $section_data);
        wp_send_json_success(array('message' => __('Saved')));
    }
    
    public function ajax_delete_section() {
        check_ajax_referer('app_builder_nonce', 'nonce');
        $section_id = isset($_POST['section_id']) ? sanitize_key($_POST['section_id']) : '';
        $page_id = isset($_POST['page_id']) ? absint($_POST['page_id']) : 0;
        delete_post_meta($page_id, '_app_section_' . $section_id);
        wp_send_json_success();
    }
    
    public function ajax_duplicate_section() {
        check_ajax_referer('app_builder_nonce', 'nonce');
        $section_id = isset($_POST['section_id']) ? sanitize_key($_POST['section_id']) : '';
        $page_id = isset($_POST['page_id']) ? absint($_POST['page_id']) : 0;
        $original = get_post_meta($page_id, '_app_section_' . $section_id, true);
        if (empty($original)) wp_send_json_error();
        $new_id = $section_id . '_copy_' . time();
        $original['id'] = $new_id;
        $original['name'] .= ' (Copy)';
        update_post_meta($page_id, '_app_section_' . $new_id, $original);
        wp_send_json_success(array('new_id' => $new_id, 'data' => $original));
    }
    
    public function ajax_reorder_sections() {
        check_ajax_referer('app_builder_nonce', 'nonce');
        $order = isset($_POST['order']) ? $_POST['order'] : array();
        $page_id = isset($_POST['page_id']) ? absint($_POST['page_id']) : 0;
        update_post_meta($page_id, '_app_sections_order', $order);
        wp_send_json_success();
    }
    
    public function ajax_get_section() {
        check_ajax_referer('app_builder_nonce', 'nonce');
        $section_id = isset($_POST['section_id']) ? sanitize_key($_POST['section_id']) : '';
        $page_id = isset($_POST['page_id']) ? absint($_POST['page_id']) : 0;
        $section = get_post_meta($page_id, '_app_section_' . $section_id, true);
        if (!empty($section)) wp_send_json_success(array('data' => $section));
        wp_send_json_error();
    }
}

AutoParts_Pro_Page_Builder::get_instance();
