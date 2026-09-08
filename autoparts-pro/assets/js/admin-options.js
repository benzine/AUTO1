/**
 * AutoParts Pro - Theme Options Admin JavaScript
 * 
 * @package AutoParts_Pro
 * @since 1.0.0
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Initialize color pickers
        $('.app-color-picker').wpColorPicker();

        // Range slider value display
        $('.app-range-slider').on('input', function() {
            $(this).next('.app-range-value').text($(this).val());
        });

        // Media uploader for image fields
        $('.app-upload-btn').on('click', function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const targetField = $button.data('target');
            const $input = $('input[name*="[' + targetField + ']"]');
            
            const mediaUploader = wp.media({
                title: appOptionsData.strings.selectMedia || 'Choose Image',
                button: { text: appOptionsData.strings.useMedia || 'Use this' },
                multiple: false
            });

            mediaUploader.on('select', function() {
                const attachment = mediaUploader.state().get('selection').first().toJSON();
                $input.val(attachment.url);
                
                // Show preview if there's a preview container
                const $preview = $('#' + targetField + '-preview');
                if ($preview.length) {
                    $preview.html('<img src="' + attachment.url + '" style="max-width:200px;height:auto;" />');
                }
            });

            mediaUploader.open();
        });

        // Reset settings button
        $('#app-reset-settings').on('click', function() {
            if (!confirm(appOptionsData.strings.confirmReset)) {
                return;
            }

            $.ajax({
                url: appOptionsData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'app_reset_settings',
                    nonce: appOptionsData.nonce,
                    settings_type: 'all'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error resetting settings');
                    }
                },
                error: function() {
                    alert('Error resetting settings');
                }
            });
        });

        // Form validation before submit
        $('form[action="options.php"]').on('submit', function(e) {
            const $requiredFields = $(this).find('[required]');
            let isValid = true;

            $requiredFields.each(function() {
                if (!$(this).val()) {
                    isValid = false;
                    $(this).closest('.form-field').addClass('error');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields');
            }
        });

        // Show save notification on successful save
        if (window.location.search.indexOf('settings-updated') > -1) {
            showNotification(appOptionsData.strings.saveSuccess || 'Settings saved successfully!');
        }
    });

    function showNotification(message) {
        const $notification = $('<div class="notice notice-success is-dismissible"><p>' + message + '</p></div>');
        $('.app-theme-options h1').after($notification);
        
        $notification.find('.notice-dismiss').on('click', function() {
            $notification.fadeOut(200, function() {
                $(this).remove();
            });
        });

        setTimeout(function() {
            $notification.fadeOut(200, function() {
                $(this).remove();
            });
        }, 5000);
    }

})(jQuery);
