<?php
/**
 * REST API endpoints for AutoParts Pro Theme
 *
 * @package AutoParts_Pro
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register REST API routes
 */
function autoparts_pro_register_rest_routes() {
    register_rest_route('autoparts-pro/v1', '/wishlist/add', array(
        'methods'             => 'POST',
        'callback'            => 'autoparts_pro_rest_add_to_wishlist',
        'permission_callback' => '__return_true',
        'args'                => array(
            'product_id' => array(
                'required'          => true,
                'validate_callback' => function ($param) {
                    return is_numeric($param);
                },
            ),
        ),
    ));

    register_rest_route('autoparts-pro/v1', '/wishlist/remove', array(
        'methods'             => 'POST',
        'callback'            => 'autoparts_pro_rest_remove_from_wishlist',
        'permission_callback' => '__return_true',
        'args'                => array(
            'product_id' => array(
                'required'          => true,
                'validate_callback' => function ($param) {
                    return is_numeric($param);
                },
            ),
        ),
    ));

    register_rest_route('autoparts-pro/v1', '/wishlist', array(
        'methods'             => 'GET',
        'callback'            => 'autoparts_pro_rest_get_wishlist',
        'permission_callback' => '__return_true',
    ));

    register_rest_route('autoparts-pro/v1', '/vehicle/models', array(
        'methods'             => 'GET',
        'callback'            => 'autoparts_pro_rest_get_vehicle_models',
        'permission_callback' => '__return_true',
        'args'                => array(
            'make_id' => array(
                'required'          => true,
                'validate_callback' => function ($param) {
                    return is_numeric($param);
                },
            ),
        ),
    ));

    register_rest_route('autoparts-pro/v1', '/check-fitment', array(
        'methods'             => 'POST',
        'callback'            => 'autoparts_pro_rest_check_fitment',
        'permission_callback' => '__return_true',
        'args'                => array(
            'product_id' => array(
                'required'          => true,
                'validate_callback' => function ($param) {
                    return is_numeric($param);
                },
            ),
            'year'       => array(
                'required' => false,
            ),
            'make'       => array(
                'required' => false,
            ),
            'model'      => array(
                'required' => false,
            ),
        ),
    ));

    register_rest_route('autoparts-pro/v1', '/chatbot/message', array(
        'methods'             => 'POST',
        'callback'            => 'autoparts_pro_rest_chatbot_message',
        'permission_callback' => '__return_true',
        'args'                => array(
            'message'  => array(
                'required'          => true,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'context'  => array(
                'required' => false,
            ),
        ),
    ));

    register_rest_route('autoparts-pro/v1', '/backoffice/save-section', array(
        'methods'             => 'POST',
        'callback'            => 'autoparts_pro_rest_save_section',
        'permission_callback' => 'autoparts_pro_verify_admin_nonce',
        'args'                => array(
            'section_id'   => array('required' => true),
            'section_data' => array('required' => true),
        ),
    ));

    register_rest_route('autoparts-pro/v1', '/backoffice/get-sections', array(
        'methods'             => 'GET',
        'callback'            => 'autoparts_pro_rest_get_sections',
        'permission_callback' => 'autoparts_pro_verify_admin_nonce',
    ));

    register_rest_route('autoparts-pro/v1', '/form/submit', array(
        'methods'             => 'POST',
        'callback'            => 'autoparts_pro_rest_form_submit',
        'permission_callback' => '__return_true',
        'args'                => array(
            'form_id'   => array('required' => true),
            'form_data' => array('required' => true),
        ),
    ));
}
add_action('rest_api_init', 'autoparts_pro_register_rest_routes');

/**
 * Verify admin nonce for backoffice operations
 */
function autoparts_pro_verify_admin_nonce($request) {
    $nonce = $request->get_header('X-WP-Nonce');
    if (!$nonce || !wp_verify_nonce($nonce, 'autoparts_pro_admin_nonce')) {
        return new WP_Error('invalid_nonce', __('Invalid nonce', 'autoparts-pro'), array('status' => 403));
    }
    if (!current_user_can('manage_options')) {
        return new WP_Error('forbidden', __('Forbidden', 'autoparts-pro'), array('status' => 403));
    }
    return true;
}

/**
 * Add product to wishlist
 */
function autoparts_pro_rest_add_to_wishlist($request) {
    $product_id = absint($request->get_param('product_id'));
    
    if (!$product_id) {
        return new WP_Error('invalid_product', __('Invalid product ID', 'autoparts-pro'), array('status' => 400));
    }
    
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $wishlist = get_user_meta($user_id, '_autoparts_pro_wishlist', true);
        
        if (!is_array($wishlist)) {
            $wishlist = array();
        }
        
        if (!in_array($product_id, $wishlist)) {
            $wishlist[] = $product_id;
            update_user_meta($user_id, '_autoparts_pro_wishlist', $wishlist);
        }
        
        return rest_ensure_response(array(
            'success' => true,
            'count'   => count($wishlist),
            'message' => __('Added to wishlist', 'autoparts-pro'),
        ));
    } else {
        // Guest wishlist via cookie
        if (isset($_COOKIE['autoparts_pro_guest_wishlist'])) {
            $wishlist = json_decode(stripslashes($_COOKIE['autoparts_pro_guest_wishlist']), true);
        } else {
            $wishlist = array();
        }
        
        if (!is_array($wishlist)) {
            $wishlist = array();
        }
        
        if (!in_array($product_id, $wishlist)) {
            $wishlist[] = $product_id;
            setcookie('autoparts_pro_guest_wishlist', json_encode($wishlist), time() + YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);
        }
        
        return rest_ensure_response(array(
            'success' => true,
            'count'   => count($wishlist),
            'message' => __('Added to wishlist', 'autoparts-pro'),
        ));
    }
}

/**
 * Remove product from wishlist
 */
function autoparts_pro_rest_remove_from_wishlist($request) {
    $product_id = absint($request->get_param('product_id'));
    
    if (!$product_id) {
        return new WP_Error('invalid_product', __('Invalid product ID', 'autoparts-pro'), array('status' => 400));
    }
    
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $wishlist = get_user_meta($user_id, '_autoparts_pro_wishlist', true);
        
        if (is_array($wishlist)) {
            $key = array_search($product_id, $wishlist);
            if ($key !== false) {
                unset($wishlist[$key]);
                $wishlist = array_values($wishlist);
                update_user_meta($user_id, '_autoparts_pro_wishlist', $wishlist);
            }
        }
        
        return rest_ensure_response(array(
            'success' => true,
            'count'   => count($wishlist),
            'message' => __('Removed from wishlist', 'autoparts-pro'),
        ));
    } else {
        if (isset($_COOKIE['autoparts_pro_guest_wishlist'])) {
            $wishlist = json_decode(stripslashes($_COOKIE['autoparts_pro_guest_wishlist']), true);
        } else {
            $wishlist = array();
        }
        
        if (is_array($wishlist)) {
            $key = array_search($product_id, $wishlist);
            if ($key !== false) {
                unset($wishlist[$key]);
                $wishlist = array_values($wishlist);
                setcookie('autoparts_pro_guest_wishlist', json_encode($wishlist), time() + YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);
            }
        }
        
        return rest_ensure_response(array(
            'success' => true,
            'count'   => count($wishlist),
            'message' => __('Removed from wishlist', 'autoparts-pro'),
        ));
    }
}

/**
 * Get wishlist items
 */
function autoparts_pro_rest_get_wishlist($request) {
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $wishlist = get_user_meta($user_id, '_autoparts_pro_wishlist', true);
    } else {
        if (isset($_COOKIE['autoparts_pro_guest_wishlist'])) {
            $wishlist = json_decode(stripslashes($_COOKIE['autoparts_pro_guest_wishlist']), true);
        } else {
            $wishlist = array();
        }
    }
    
    if (!is_array($wishlist)) {
        $wishlist = array();
    }
    
    $products = array();
    foreach ($wishlist as $product_id) {
        $product = wc_get_product($product_id);
        if ($product) {
            $products[] = array(
                'id'         => $product->get_id(),
                'name'       => $product->get_name(),
                'price'      => $product->get_price_html(),
                'image'      => wp_get_attachment_image_url($product->get_image_id(), 'thumbnail'),
                'in_stock'   => $product->is_in_stock(),
                'permalink'  => $product->get_permalink(),
            );
        }
    }
    
    return rest_ensure_response(array(
        'success' => true,
        'items'   => $products,
        'count'   => count($products),
    ));
}

/**
 * Get vehicle models by make
 */
function autoparts_pro_rest_get_vehicle_models($request) {
    $make_id = absint($request->get_param('make_id'));
    
    if (!$make_id) {
        return new WP_Error('invalid_make', __('Invalid make ID', 'autoparts-pro'), array('status' => 400));
    }
    
    $models = get_terms(array(
        'taxonomy'   => 'vehicle_model',
        'hide_empty' => true,
        'meta_query' => array(
            array(
                'key'     => '_parent_make_id',
                'value'   => $make_id,
                'compare' => '=',
            ),
        ),
    ));
    
    if (is_wp_error($models)) {
        return $models;
    }
    
    $response = array();
    foreach ($models as $model) {
        $response[] = array(
            'id'   => $model->term_id,
            'name' => $model->name,
        );
    }
    
    return rest_ensure_response($response);
}

/**
 * Check product fitment
 */
function autoparts_pro_rest_check_fitment($request) {
    $product_id = absint($request->get_param('product_id'));
    $year       = sanitize_text_field($request->get_param('year'));
    $make       = sanitize_text_field($request->get_param('make'));
    $model      = sanitize_text_field($request->get_param('model'));
    
    $fits = autoparts_pro_check_fitment($product_id, $year, $make, $model);
    
    return rest_ensure_response(array(
        'success' => true,
        'fits'    => $fits,
        'message' => $fits ? __('This part fits your vehicle', 'autoparts-pro') : __('This part does not fit your vehicle', 'autoparts-pro'),
    ));
}

/**
 * Handle chatbot messages
 */
function autoparts_pro_rest_chatbot_message($request) {
    $message = sanitize_text_field($request->get_param('message'));
    $context = $request->get_param('context');
    
    $api_key = autoparts_pro_get_option('chatbot_api_key', '');
    
    if (empty($api_key)) {
        return rest_ensure_response(array(
            'success' => false,
            'message' => __('Chatbot API key not configured', 'autoparts-pro'),
        ));
    }
    
    // Send to AI backend
    $response = wp_remote_post('https://api.openai.com/v1/chat/completions', array(
        'headers' => array(
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
        ),
        'body'    => json_encode(array(
            'model'    => 'gpt-3.5-turbo',
            'messages' => array(
                array(
                    'role'    => 'system',
                    'content' => 'You are an automotive parts expert assistant for AutoParts Pro. Help customers find the right parts for their vehicles.',
                ),
                array(
                    'role'    => 'user',
                    'content' => $message,
                ),
            ),
            'max_tokens' => 150,
        )),
    ));
    
    if (is_wp_error($response)) {
        return rest_ensure_response(array(
            'success' => false,
            'message' => __('Error communicating with AI service', 'autoparts-pro'),
        ));
    }
    
    $body = json_decode(wp_remote_retrieve_body($response), true);
    
    if (isset($body['choices'][0]['message']['content'])) {
        return rest_ensure_response(array(
            'success' => true,
            'message' => $body['choices'][0]['message']['content'],
        ));
    }
    
    return rest_ensure_response(array(
        'success' => false,
        'message' => __('Unable to process request', 'autoparts-pro'),
    ));
}

/**
 * Save section data from backoffice
 */
function autoparts_pro_rest_save_section($request) {
    $section_id   = sanitize_text_field($request->get_param('section_id'));
    $section_data = $request->get_param('section_data');
    
    if (empty($section_id) || empty($section_data)) {
        return new WP_Error('invalid_data', __('Invalid section data', 'autoparts-pro'), array('status' => 400));
    }
    
    $sections = get_option('autoparts_pro_page_sections', array());
    $sections[$section_id] = array(
        'data'      => $section_data,
        'modified'  => current_time('mysql'),
        'modified_by' => get_current_user_id(),
    );
    
    update_option('autoparts_pro_page_sections', $sections, false);
    
    return rest_ensure_response(array(
        'success' => true,
        'message' => __('Section saved successfully', 'autoparts-pro'),
    ));
}

/**
 * Get all sections for backoffice
 */
function autoparts_pro_rest_get_sections($request) {
    $sections = get_option('autoparts_pro_page_sections', array());
    
    return rest_ensure_response(array(
        'success'  => true,
        'sections' => $sections,
    ));
}

/**
 * Handle form submissions
 */
function autoparts_pro_rest_form_submit($request) {
    $form_id   = sanitize_text_field($request->get_param('form_id'));
    $form_data = $request->get_param('form_data');
    
    if (empty($form_id) || empty($form_data)) {
        return new WP_Error('invalid_data', __('Invalid form data', 'autoparts-pro'), array('status' => 400));
    }
    
    // Create form submission post
    $submission_id = wp_insert_post(array(
        'post_type'   => 'form_submission',
        'post_status' => 'publish',
        'post_title'  => sprintf(__('Form Submission #%d', 'autoparts-pro'), time()),
        'meta_input'  => array(
            '_form_id'    => $form_id,
            '_form_data'  => maybe_serialize($form_data),
            '_submitted'  => current_time('mysql'),
            '_user_ip'    => $_SERVER['REMOTE_ADDR'] ?? '',
            '_user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ),
    ));
    
    if (is_wp_error($submission_id)) {
        return $submission_id;
    }
    
    // Send email notification
    $admin_email = get_option('admin_email');
    $subject     = sprintf(__('New Form Submission: %s', 'autoparts-pro'), $form_id);
    $message     = __('A new form has been submitted:', 'autoparts-pro') . "\n\n";
    $message    .= print_r($form_data, true);
    
    wp_mail($admin_email, $subject, $message);
    
    return rest_ensure_response(array(
        'success'         => true,
        'submission_id'   => $submission_id,
        'message'         => __('Form submitted successfully', 'autoparts-pro'),
    ));
}
