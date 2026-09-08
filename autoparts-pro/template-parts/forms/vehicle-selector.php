<?php
/**
 * Template part for displaying the Vehicle Compatibility Selector
 *
 * @package AutoParts_Pro
 */

$selector_style = isset($args['style']) ? $args['style'] : 'horizontal';
$show_title = isset($args['show_title']) ? $args['show_title'] : true;
$title = isset($args['title']) ? $args['title'] : __('Find Parts That Fit Your Vehicle', 'autoparts-pro');
$button_text = isset($args['button_text']) ? $args['button_text'] : __('Check Compatibility', 'autoparts-pro');
$redirect_url = isset($args['redirect_url']) ? $args['redirect_url'] : home_url('/shop/');
$compact_mode = isset($args['compact']) ? $args['compact'] : false;

// Get vehicle data
$makes = get_terms(array(
    'taxonomy' => 'vehicle_make',
    'hide_empty' => false,
));
?>

<div class="vehicle-selector <?php echo esc_attr($selector_style); ?> <?php echo $compact_mode ? 'compact-mode' : ''; ?>" id="vehicle-selector-<?php echo esc_attr(wp_rand(1000, 9999)); ?>">
    <?php if ($show_title && !$compact_mode) : ?>
    <div class="vehicle-selector-header">
        <h3 class="vehicle-selector-title">
            <svg class="vehicle-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/>
                <circle cx="7" cy="17" r="2"/>
                <circle cx="17" cy="17" r="2"/>
            </svg>
            <?php echo esc_html($title); ?>
        </h3>
    </div>
    <?php endif; ?>

    <form class="vehicle-selector-form" id="vehicle-fitment-form" data-redirect="<?php echo esc_url($redirect_url); ?>">
        <?php wp_nonce_field('vehicle_compatibility_check', 'vehicle_nonce'); ?>
        
        <div class="vehicle-fields-grid <?php echo $compact_mode ? 'compact-grid' : ''; ?>">
            <!-- Year Select -->
            <div class="vehicle-field">
                <label for="vehicle-year"><?php esc_html_e('Year', 'autoparts-pro'); ?></label>
                <select id="vehicle-year" name="vehicle_year" required class="vehicle-select">
                    <option value=""><?php esc_html_e('Select Year', 'autoparts-pro'); ?></option>
                    <?php
                    $current_year = date('Y');
                    for ($y = $current_year; $y >= $current_year - 30; $y--) :
                    ?>
                    <option value="<?php echo esc_attr($y); ?>"><?php echo esc_html($y); ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- Make Select -->
            <div class="vehicle-field">
                <label for="vehicle-make"><?php esc_html_e('Make', 'autoparts-pro'); ?></label>
                <select id="vehicle-make" name="vehicle_make" required class="vehicle-select" disabled>
                    <option value=""><?php esc_html_e('Select Year First', 'autoparts-pro'); ?></option>
                </select>
                <div class="vehicle-field-loader" style="display: none;">
                    <span class="loader-spinner"></span>
                </div>
            </div>

            <!-- Model Select -->
            <div class="vehicle-field">
                <label for="vehicle-model"><?php esc_html_e('Model', 'autoparts-pro'); ?></label>
                <select id="vehicle-model" name="vehicle_model" required class="vehicle-select" disabled>
                    <option value=""><?php esc_html_e('Select Make First', 'autoparts-pro'); ?></option>
                </select>
                <div class="vehicle-field-loader" style="display: none;">
                    <span class="loader-spinner"></span>
                </div>
            </div>

            <!-- Engine Select -->
            <div class="vehicle-field">
                <label for="vehicle-engine"><?php esc_html_e('Engine', 'autoparts-pro'); ?></label>
                <select id="vehicle-engine" name="vehicle_engine" class="vehicle-select" disabled>
                    <option value=""><?php esc_html_e('Select Model First', 'autoparts-pro'); ?></option>
                </select>
                <div class="vehicle-field-loader" style="display: none;">
                    <span class="loader-spinner"></span>
                </div>
            </div>
        </div>

        <div class="vehicle-selector-actions">
            <button type="submit" class="btn btn-primary vehicle-check-btn" id="vehicle-check-btn">
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <?php echo esc_html($button_text); ?>
            </button>
            
            <?php if (!$compact_mode) : ?>
            <button type="button" class="btn btn-outline vehicle-clear-btn" id="vehicle-clear-btn">
                <?php esc_html_e('Clear Selection', 'autoparts-pro'); ?>
            </button>
            <?php endif; ?>
        </div>

        <!-- Selected Vehicle Display -->
        <div class="vehicle-selected-display" id="vehicle-selected-display" style="display: none;">
            <div class="vehicle-badge-success">
                <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span class="vehicle-selected-text"></span>
                <button type="button" class="vehicle-change-btn"><?php esc_html_e('Change', 'autoparts-pro'); ?></button>
            </div>
        </div>

        <!-- Garage Save Option (for logged-in users) -->
        <?php if (is_user_logged_in()) : ?>
        <div class="vehicle-garage-option" id="vehicle-garage-option" style="display: none;">
            <label class="checkbox-label">
                <input type="checkbox" id="vehicle-save-to-garage" name="save_to_garage">
                <span><?php esc_html_e('Save to My Garage', 'autoparts-pro'); ?></span>
            </label>
        </div>
        <?php endif; ?>
    </form>

    <!-- My Garage Quick Access -->
    <?php if (is_user_logged_in()) : 
        $user_garage = get_user_meta(get_current_user_id(), '_user_garage_vehicles', true);
        if (!empty($user_garage)) :
    ?>
    <div class="my-garage-quick-access">
        <p class="garage-title"><?php esc_html_e('My Saved Vehicles:', 'autoparts-pro'); ?></p>
        <div class="garage-vehicles-list">
            <?php foreach ($user_garage as $index => $vehicle) : ?>
            <button type="button" class="garage-vehicle-item" data-vehicle-index="<?php echo esc_attr($index); ?>">
                <span class="garage-vehicle-name">
                    <?php echo esc_html($vehicle['year'] . ' ' . $vehicle['make'] . ' ' . $vehicle['model']); ?>
                </span>
                <span class="garage-vehicle-engine"><?php echo esc_html($vehicle['engine'] ?? ''); ?></span>
            </button>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; endif; ?>
</div>
