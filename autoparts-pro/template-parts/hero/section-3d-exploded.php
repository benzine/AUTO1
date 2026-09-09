<?php
/**
 * AutoParts Pro - 3D Exploded View Hero Template Part
 * Premium scroll-triggered 3D animation section
 *
 * @package AutoParts_Pro
 * @version 2.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get theme options
$model_url = get_theme_mod('autoparts_3d_hero_model_url', '');
$explosion_factor = get_theme_mod('autoparts_3d_hero_explosion_factor', '2.5');
$camera_x = get_theme_mod('autoparts_3d_hero_camera_x', '0');
$camera_y = get_theme_mod('autoparts_3d_hero_camera_y', '3');
$camera_z = get_theme_mod('autoparts_3d_hero_camera_z', '8');
$auto_rotate = get_theme_mod('autoparts_3d_hero_auto_rotate', '1');
$rotation_speed = get_theme_mod('autoparts_3d_hero_rotation_speed', '0.002');
$scroll_distance = get_theme_mod('autoparts_3d_hero_scroll_distance', '2000');
$headline = get_theme_mod('autoparts_3d_hero_headline', 'Precision Engineering');
$subheadline = get_theme_mod('autoparts_3d_hero_subheadline', 'Explore every component in stunning detail');
$cta_text = get_theme_mod('autoparts_3d_hero_cta_text', 'Shop Now');
$cta_url = get_theme_mod('autoparts_3d_hero_cta_url', '/shop');
$show_labels = get_theme_mod('autoparts_3d_hero_show_labels', '1');

// Sample labels for the fallback engine model
$labels = array(
    array(
        'partName' => 'Engine Block',
        'title' => 'Engine Block',
        'description' => 'Aluminum alloy construction'
    ),
    array(
        'partName' => 'Piston 1',
        'title' => 'Piston',
        'description' => 'High-performance forged aluminum'
    ),
    array(
        'partName' => 'Crankshaft',
        'title' => 'Crankshaft',
        'description' => 'Forged steel, balanced'
    ),
    array(
        'partName' => 'Cylinder Head',
        'title' => 'Cylinder Head',
        'description' => 'DOHC, 4 valves per cylinder'
    ),
    array(
        'partName' => 'Camshaft 1',
        'title' => 'Camshaft',
        'description' => 'Performance profile'
    ),
    array(
        'partName' => 'Valve 1',
        'title' => 'Valve',
        'description' => 'Titanium intake/exhaust'
    ),
    array(
        'partName' => 'Spark Plug 1',
        'title' => 'Spark Plug',
        'description' => 'Iridium high-performance'
    ),
    array(
        'partName' => 'Oil Pan',
        'title' => 'Oil Pan',
        'description' => 'Baffled racing design'
    )
);

// Allow filtering of labels
$labels = apply_filters('autoparts_pro_3d_hero_labels', $labels);
?>

<section class="hero-3d-exploded" 
         data-model-url="<?php echo esc_url($model_url); ?>"
         data-explosion-factor="<?php echo esc_attr($explosion_factor); ?>"
         data-camera-x="<?php echo esc_attr($camera_x); ?>"
         data-camera-y="<?php echo esc_attr($camera_y); ?>"
         data-camera-z="<?php echo esc_attr($camera_z); ?>"
         data-auto-rotate="<?php echo esc_attr($auto_rotate); ?>"
         data-rotation-speed="<?php echo esc_attr($rotation_speed); ?>"
         data-scroll-distance="<?php echo esc_attr($scroll_distance); ?>">
    
    <!-- Mechanical Grid Background -->
    <div class="hero-3d-mechanical-grid"></div>
    
    <!-- Vignette Overlay -->
    <div class="hero-3d-vignette"></div>
    
    <!-- Loading State -->
    <div class="hero-3d-loading">
        <div class="hero-3d-loading-spinner"></div>
        <div class="hero-3d-loading-text"><?php esc_html_e('Loading Engine...', 'autoparts-pro'); ?></div>
    </div>
    
    <!-- Content Overlay -->
    <div class="hero-3d-content">
        <div class="hero-3d-headline">
            <h1 class="hero-3d-title">
                <?php echo esc_html($headline); ?>
                <span><?php esc_html_e('Redefined', 'autoparts-pro'); ?></span>
            </h1>
            <p class="hero-3d-subtitle"><?php echo esc_html($subheadline); ?></p>
        </div>
        
        <?php if ($cta_text && $cta_url) : ?>
        <div class="hero-3d-cta">
            <a href="<?php echo esc_url($cta_url); ?>" class="btn btn-primary btn-large">
                <?php echo esc_html($cta_text); ?>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Scroll Indicator -->
    <div class="hero-3d-scroll-indicator">
        <span class="hero-3d-scroll-text"><?php esc_html_e('Scroll to Explore', 'autoparts-pro'); ?></span>
        <div class="hero-3d-scroll-arrow"></div>
    </div>
    
    <!-- Labels Container (populated by JS) -->
    <?php if ($show_labels === '1') : ?>
    <script type="application/json" class="hero-3d-labels-data">
        <?php echo json_encode($labels); ?>
    </script>
    <?php endif; ?>
    
</section>

<?php
// Enqueue 3D hero assets
function autoparts_pro_enqueue_3d_hero_assets() {
    if (is_front_page() || has_block('autoparts-pro/3d-hero')) {
        // Three.js
        wp_enqueue_script('three', 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js', array(), 'r128', true);
        
        // GLTF Loader
        wp_enqueue_script('three-gltf-loader', 'https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js', array('three'), '0.128.0', true);
        
        // GSAP with ScrollTrigger
        wp_enqueue_script('gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js', array(), '3.12.2', true);
        wp_enqueue_script('gsap-scrolltrigger', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js', array('gsap'), '3.12.2', true);
        
        // 3D Hero Engine
        wp_enqueue_script('autoparts-3d-hero', 
            get_template_directory_uri() . '/assets/js/3d-hero-engine.js', 
            array('jquery', 'three', 'three-gltf-loader', 'gsap', 'gsap-scrolltrigger'), 
            AUTOPARTS_PRO_VERSION, 
            true
        );
        
        // Pass PHP variables to JavaScript
        $model_url = get_theme_mod('autoparts_3d_hero_model_url', '');
        $labels_raw = get_theme_mod('autoparts_3d_hero_labels', array());
        
        wp_localize_script('autoparts-3d-hero', 'autopartsPro3DHero', array(
            'modelUrl' => $model_url,
            'explosionFactor' => get_theme_mod('autoparts_3d_hero_explosion_factor', '2.5'),
            'cameraX' => get_theme_mod('autoparts_3d_hero_camera_x', '0'),
            'cameraY' => get_theme_mod('autoparts_3d_hero_camera_y', '3'),
            'cameraZ' => get_theme_mod('autoparts_3d_hero_camera_z', '8'),
            'autoRotate' => get_theme_mod('autoparts_3d_hero_auto_rotate', '1'),
            'rotationSpeed' => get_theme_mod('autoparts_3d_hero_rotation_speed', '0.002'),
            'scrollDistance' => get_theme_mod('autoparts_3d_hero_scroll_distance', '2000'),
            'labels' => !empty($labels_raw) ? $labels_raw : array(
                array('partName' => 'Engine Block', 'title' => 'Engine Block', 'description' => 'Aluminum alloy construction'),
                array('partName' => 'Piston 1', 'title' => 'Piston', 'description' => 'High-performance forged aluminum'),
                array('partName' => 'Crankshaft', 'title' => 'Crankshaft', 'description' => 'Forged steel, balanced'),
                array('partName' => 'Cylinder Head', 'title' => 'Cylinder Head', 'description' => 'DOHC, 4 valves per cylinder'),
                array('partName' => 'Camshaft 1', 'title' => 'Camshaft', 'description' => 'Performance profile'),
                array('partName' => 'Valve 1', 'title' => 'Valve', 'description' => 'Titanium intake/exhaust'),
                array('partName' => 'Spark Plug 1', 'title' => 'Spark Plug', 'description' => 'Iridium high-performance'),
                array('partName' => 'Oil Pan', 'title' => 'Oil Pan', 'description' => 'Baffled racing design')
            )
        ));
        
        // 3D Hero CSS
        wp_enqueue_style('autoparts-3d-hero', 
            get_template_directory_uri() . '/assets/css/hero-3d.css', 
            array(), 
            AUTOPARTS_PRO_VERSION
        );
    }
}
add_action('wp_enqueue_scripts', 'autoparts_pro_enqueue_3d_hero_assets', 20);

/**
 * Shortcode to display 3D hero on any page
 * Usage: [autoparts_3d_hero]
 */
function autoparts_pro_3d_hero_shortcode($atts) {
    $atts = shortcode_atts(array(
        'model_url' => '',
        'explosion_factor' => '2.5',
        'headline' => 'Precision Engineering',
        'subheadline' => 'Explore every component in stunning detail',
        'cta_text' => 'Shop Now',
        'cta_url' => '/shop'
    ), $atts);
    
    ob_start();
    include locate_template('template-parts/hero/section-3d-exploded.php');
    return ob_get_clean();
}
add_shortcode('autoparts_3d_hero', 'autoparts_pro_3d_hero_shortcode');

/**
 * Gutenberg block registration for 3D Hero
 */
function autoparts_pro_register_3d_hero_block() {
    if (function_exists('register_block_type')) {
        register_block_type('autoparts-pro/3d-hero', array(
            'render_callback' => 'autoparts_pro_3d_hero_shortcode'
        ));
    }
}
add_action('init', 'autoparts_pro_register_3d_hero_block');
