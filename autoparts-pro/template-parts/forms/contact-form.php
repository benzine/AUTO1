<?php
/**
 * Template part for displaying the Advanced Contact Form with Spam Protection
 *
 * @package AutoParts_Pro
 */

$form_id = isset($args['form_id']) ? $args['form_id'] : 'contact-form-' . wp_rand(1000, 9999);
$form_type = isset($args['form_type']) ? $args['form_type'] : 'contact'; // contact, quote_request, support
$show_subject = isset($args['show_subject']) ? $args['show_subject'] : true;
$show_department = isset($args['show_department']) ? $args['show_department'] : true;
$enable_file_upload = isset($args['enable_file_upload']) ? $args['enable_file_upload'] : true;
$max_file_size = isset($args['max_file_size']) ? $args['max_file_size'] : 5; // MB
$allowed_files = isset($args['allowed_files']) ? $args['allowed_files'] : 'jpg,jpeg,png,pdf,doc,docx';
$success_message = isset($args['success_message']) ? $args['success_message'] : __('Thank you! Your message has been sent successfully.', 'autoparts-pro');
$submit_text = isset($args['submit_text']) ? $args['submit_text'] : __('Send Message', 'autoparts-pro');

// Departments
$departments = array(
    'sales' => __('Sales Inquiry', 'autoparts-pro'),
    'support' => __('Technical Support', 'autoparts-pro'),
    'parts' => __('Parts Compatibility', 'autoparts-pro'),
    'wholesale' => __('Wholesale/Bulk Orders', 'autoparts-pro'),
    'returns' => __('Returns & Refunds', 'autoparts-pro'),
    'other' => __('Other', 'autoparts-pro'),
);
?>

<div class="advanced-contact-form" id="<?php echo esc_attr($form_id); ?>" data-form-type="<?php echo esc_attr($form_type); ?>">
    <form class="form-builder-form" id="form-<?php echo esc_attr($form_id); ?>" enctype="multipart/form-data">
        <?php wp_nonce_field('form_submission_' . $form_id, 'form_nonce'); ?>
        <input type="hidden" name="form_id" value="<?php echo esc_attr($form_id); ?>">
        <input type="hidden" name="form_type" value="<?php echo esc_attr($form_type); ?>">
        <input type="hidden" name="form_timestamp" id="form-timestamp" value="<?php echo time(); ?>">
        <input type="hidden" name="vehicle_data" id="form-vehicle-data" value="">
        
        <!-- Honeypot Field (Spam Protection) -->
        <div class="form-honeypot" style="display: none !important;" aria-hidden="true">
            <label for="website-url-<?php echo esc_attr($form_id); ?>"><?php esc_html_e('Website', 'autoparts-pro'); ?></label>
            <input type="text" id="website-url-<?php echo esc_attr($form_id); ?>" name="website_url" value="" autocomplete="off">
        </div>

        <?php if ($form_type === 'quote_request') : ?>
        <!-- Quote Request Specific Fields -->
        <div class="form-section">
            <h4 class="form-section-title"><?php esc_html_e('Quote Details', 'autoparts-pro'); ?></h4>
            
            <div class="form-grid-2">
                <div class="form-group">
                    <label for="part-numbers-<?php echo esc_attr($form_id); ?>">
                        <?php esc_html_e('Part Numbers', 'autoparts-pro'); ?>
                        <span class="required">*</span>
                    </label>
                    <textarea id="part-numbers-<?php echo esc_attr($form_id); ?>" name="part_numbers" rows="3" required placeholder="<?php esc_attr_e('Enter OEM or manufacturer part numbers (one per line)', 'autoparts-pro'); ?>"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="quantity-<?php echo esc_attr($form_id); ?>">
                        <?php esc_html_e('Estimated Quantity', 'autoparts-pro'); ?>
                        <span class="required">*</span>
                    </label>
                    <select id="quantity-<?php echo esc_attr($form_id); ?>" name="quantity" required>
                        <option value=""><?php esc_html_e('Select quantity range', 'autoparts-pro'); ?></option>
                        <option value="1-10">1 - 10 units</option>
                        <option value="11-50">11 - 50 units</option>
                        <option value="51-100">51 - 100 units</option>
                        <option value="101-500">101 - 500 units</option>
                        <option value="500+">500+ units</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="project-details-<?php echo esc_attr($form_id); ?>">
                    <?php esc_html_e('Project Details / Requirements', 'autoparts-pro'); ?>
                </label>
                <textarea id="project-details-<?php echo esc_attr($form_id); ?>" name="project_details" rows="4" placeholder="<?php esc_attr_e('Describe your project, timeline, special requirements...', 'autoparts-pro'); ?>"></textarea>
            </div>
        </div>
        <?php endif; ?>

        <!-- Standard Contact Fields -->
        <div class="form-section">
            <h4 class="form-section-title"><?php esc_html_e('Your Information', 'autoparts-pro'); ?></h4>
            
            <div class="form-grid-2">
                <div class="form-group">
                    <label for="first-name-<?php echo esc_attr($form_id); ?>">
                        <?php esc_html_e('First Name', 'autoparts-pro'); ?>
                        <span class="required">*</span>
                    </label>
                    <input type="text" id="first-name-<?php echo esc_attr($form_id); ?>" name="first_name" required autocomplete="given-name">
                </div>
                
                <div class="form-group">
                    <label for="last-name-<?php echo esc_attr($form_id); ?>">
                        <?php esc_html_e('Last Name', 'autoparts-pro'); ?>
                        <span class="required">*</span>
                    </label>
                    <input type="text" id="last-name-<?php echo esc_attr($form_id); ?>" name="last_name" required autocomplete="family-name">
                </div>
            </div>
            
            <div class="form-grid-2">
                <div class="form-group">
                    <label for="email-<?php echo esc_attr($form_id); ?>">
                        <?php esc_html_e('Email Address', 'autoparts-pro'); ?>
                        <span class="required">*</span>
                    </label>
                    <input type="email" id="email-<?php echo esc_attr($form_id); ?>" name="email" required autocomplete="email">
                </div>
                
                <div class="form-group">
                    <label for="phone-<?php echo esc_attr($form_id); ?>">
                        <?php esc_html_e('Phone Number', 'autoparts-pro'); ?>
                    </label>
                    <input type="tel" id="phone-<?php echo esc_attr($form_id); ?>" name="phone" autocomplete="tel">
                </div>
            </div>

            <?php if ($show_department) : ?>
            <div class="form-group">
                <label for="department-<?php echo esc_attr($form_id); ?>">
                    <?php esc_html_e('Department', 'autoparts-pro'); ?>
                </label>
                <select id="department-<?php echo esc_attr($form_id); ?>" name="department">
                    <option value=""><?php esc_html_e('Select department (optional)', 'autoparts-pro'); ?></option>
                    <?php foreach ($departments as $key => $label) : ?>
                    <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <?php if ($show_subject) : ?>
            <div class="form-group">
                <label for="subject-<?php echo esc_attr($form_id); ?>">
                    <?php esc_html_e('Subject', 'autoparts-pro'); ?>
                    <span class="required">*</span>
                </label>
                <input type="text" id="subject-<?php echo esc_attr($form_id); ?>" name="subject" required>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="message-<?php echo esc_attr($form_id); ?>">
                    <?php esc_html_e('Message', 'autoparts-pro'); ?>
                    <span class="required">*</span>
                </label>
                <textarea id="message-<?php echo esc_attr($form_id); ?>" name="message" rows="6" required placeholder="<?php esc_attr_e('How can we help you?', 'autoparts-pro'); ?>"></textarea>
            </div>

            <?php if ($enable_file_upload) : ?>
            <div class="form-group">
                <label for="file-upload-<?php echo esc_attr($form_id); ?>">
                    <?php esc_html_e('Attach Files', 'autoparts-pro'); ?>
                    <span class="file-info">(<?php printf(__('Max %dMB, Allowed: %s', 'autoparts-pro'), $max_file_size, $allowed_files); ?>)</span>
                </label>
                <div class="file-upload-area" id="file-upload-<?php echo esc_attr($form_id); ?>-dropzone">
                    <input type="file" id="file-upload-<?php echo esc_attr($form_id); ?>" name="attachments[]" multiple accept=".<?php echo str_replace(',', ',.', $allowed_files); ?>">
                    <div class="upload-placeholder">
                        <svg class="upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                        <p><?php esc_html_e('Drag & drop files here or click to browse', 'autoparts-pro'); ?></p>
                    </div>
                    <div class="file-list" id="file-list-<?php echo esc_attr($form_id); ?>"></div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Spam Protection Time Check -->
        <input type="hidden" name="form_completion_time" id="form-completion-time" value="0">

        <div class="form-actions">
            <button type="submit" class="btn btn-primary form-submit-btn" id="form-submit-<?php echo esc_attr($form_id); ?>">
                <span class="btn-text"><?php echo esc_html($submit_text); ?></span>
                <span class="btn-loader" style="display: none;">
                    <span class="loader-spinner"></span>
                    <?php esc_html_e('Sending...', 'autoparts-pro'); ?>
                </span>
            </button>
            
            <div class="form-status-message" id="form-status-<?php echo esc_attr($form_id); ?>" style="display: none;"></div>
        </div>

        <!-- Success Message Template -->
        <script type="application/json" id="form-success-msg-<?php echo esc_attr($form_id); ?>">
            {
                "success": "<?php echo esc_js($success_message); ?>",
                "error": "<?php esc_html_e('Sorry, there was an error sending your message. Please try again.', 'autoparts-pro'); ?>"
            }
        </script>
    </form>
</div>
