<?php
/**
 * Template part for displaying the 3D Exploded View Hero Section
 *
 * @package AutoParts_Pro
 */

$hero_settings = get_option('autoparts_hero_settings', array());
$is_enabled = isset($hero_settings['enable']) ? $hero_settings['enable'] : true;
$model_url = isset($hero_settings['model_url']) ? $hero_settings['model_url'] : '';
$scroll_distance = isset($hero_settings['scroll_distance']) ? $hero_settings['scroll_distance'] : 1500;
$auto_rotate = isset($hero_settings['auto_rotate']) ? $hero_settings['auto_rotate'] : true;
$rotation_speed = isset($hero_settings['rotation_speed']) ? $hero_settings['rotation_speed'] : 0.5;
$camera_position = isset($hero_settings['camera_position']) ? $hero_settings['camera_position'] : array(0, 2, 5);
$labels = isset($hero_settings['component_labels']) ? $hero_settings['component_labels'] : array();
$background_color = isset($hero_settings['bg_color']) ? $hero_settings['bg_color'] : '#0A0A0A';

if (!$is_enabled) {
    return;
}
?>

<section id="hero-3d" class="hero-3d-section" data-scroll-distance="<?php echo esc_attr($scroll_distance); ?>" style="--hero-bg: <?php echo esc_attr($background_color); ?>;">
    <div class="hero-canvas-container" id="hero-canvas-wrapper">
        <canvas id="hero-canvas"></canvas>
        
        <!-- Loading Overlay -->
        <div class="hero-loading-overlay" id="hero-loader">
            <div class="hero-loader-content">
                <div class="hero-spinner"></div>
                <p class="hero-loading-text"><?php esc_html_e('Loading Engine Model...', 'autoparts-pro'); ?></p>
                <div class="hero-progress-bar">
                    <div class="hero-progress-fill" id="hero-progress"></div>
                </div>
                <p class="hero-progress-percent" id="hero-progress-text">0%</p>
            </div>
        </div>

        <!-- Component Labels Container -->
        <div class="hero-labels-container" id="hero-labels">
            <?php if (!empty($labels)) : foreach ($labels as $index => $label) : ?>
            <div class="hero-component-label" 
                 data-component-id="<?php echo esc_attr($label['id'] ?? 'part-' . $index); ?>"
                 data-position-x="<?php echo esc_attr($label['position_x'] ?? 0); ?>"
                 data-position-y="<?php echo esc_attr($label['position_y'] ?? 0); ?>"
                 data-position-z="<?php echo esc_attr($label['position_z'] ?? 0); ?>"
                 style="opacity: 0; transform: translate3d(0, 0, 0);">
                <div class="hero-label-line"></div>
                <div class="hero-label-box">
                    <span class="hero-label-title"><?php echo esc_html($label['title'] ?? ''); ?></span>
                    <?php if (!empty($label['description'])) : ?>
                    <span class="hero-label-desc"><?php echo esc_html($label['description']); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>

        <!-- Scroll Indicator -->
        <div class="hero-scroll-indicator" id="hero-scroll-hint">
            <div class="hero-scroll-arrow"></div>
            <span class="hero-scroll-text"><?php esc_html_e('Scroll to Explore', 'autoparts-pro'); ?></span>
        </div>
    </div>

    <div class="hero-content-overlay">
        <div class="hero-text-content">
            <h1 class="hero-main-title">
                <span class="hero-title-line"><?php echo esc_html($hero_settings['main_title'] ?? __('Precision Engineering', 'autoparts-pro')); ?></span>
                <span class="hero-title-accent"><?php echo esc_html($hero_settings['accent_title'] ?? __('Premium Auto Parts', 'autoparts-pro')); ?></span>
            </h1>
            <p class="hero-subtitle"><?php echo esc_html($hero_settings['subtitle'] ?? __('Experience the power of genuine OEM and aftermarket parts. Built for performance, engineered for longevity.', 'autoparts-pro')); ?></p>
            
            <div class="hero-cta-group">
                <?php if (!empty($hero_settings['primary_button_url'])) : ?>
                <a href="<?php echo esc_url($hero_settings['primary_button_url']); ?>" class="btn btn-primary hero-btn-primary">
                    <?php echo esc_html($hero_settings['primary_button_text'] ?? __('Shop Now', 'autoparts-pro')); ?>
                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
                <?php endif; ?>
                
                <?php if (!empty($hero_settings['secondary_button_url'])) : ?>
                <a href="<?php echo esc_url($hero_settings['secondary_button_url']); ?>" class="btn btn-outline hero-btn-secondary">
                    <?php echo esc_html($hero_settings['secondary_button_text'] ?? __('View Catalog', 'autoparts-pro')); ?>
                </a>
                <?php endif; ?>
            </div>

            <!-- Stats Counter -->
            <div class="hero-stats">
                <div class="hero-stat-item">
                    <span class="hero-stat-number" data-count="<?php echo esc_attr($hero_settings['stat1_number'] ?? 10000); ?>">0</span>
                    <span class="hero-stat-label"><?php echo esc_html($hero_settings['stat1_label'] ?? __('Parts Available', 'autoparts-pro')); ?></span>
                </div>
                <div class="hero-stat-item">
                    <span class="hero-stat-number" data-count="<?php echo esc_attr($hero_settings['stat2_number'] ?? 500); ?>">0</span>
                    <span class="hero-stat-label"><?php echo esc_html($hero_settings['stat2_label'] ?? __('Brands', 'autoparts-pro')); ?></span>
                </div>
                <div class="hero-stat-item">
                    <span class="hero-stat-number" data-count="<?php echo esc_attr($hero_settings['stat3_number'] ?? 99); ?>">0</span>
                    <span class="hero-stat-suffix">%</span>
                    <span class="hero-stat-label"><?php echo esc_html($hero_settings['stat3_label'] ?? __('Satisfaction Rate', 'autoparts-pro')); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden data for Three.js -->
    <script type="application/json" id="hero-3d-data">
        {
            "modelUrl": "<?php echo esc_url($model_url); ?>",
            "scrollDistance": <?php echo esc_js($scroll_distance); ?>,
            "autoRotate": <?php echo $auto_rotate ? 'true' : 'false'; ?>,
            "rotationSpeed": <?php echo esc_js($rotation_speed); ?>,
            "cameraPosition": <?php echo json_encode($camera_position); ?>,
            "components": <?php echo json_encode($labels); ?>
        }
    </script>
</section>
