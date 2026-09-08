<?php
/**
 * REST API Endpoints
 *
 * @package AutoParts_Pro
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register REST API routes.
 */
function autoparts_pro_register_rest_routes() {
	// Wishlist endpoints.
	register_rest_route(
		'autoparts-pro/v1',
		'/wishlist',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'autoparts_rest_get_wishlist',
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		'autoparts-pro/v1',
		'/wishlist',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'autoparts_rest_update_wishlist',
			'permission_callback' => 'autoparts_rest_verify_nonce',
			'args'                => array(
				'product_id' => array(
					'required'          => true,
					'type'              => 'integer',
					'sanitize_callback' => 'absint',
				),
				'action'     => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
					'enum'              => array( 'add', 'remove' ),
				),
			),
		)
	);

	// Vehicle fitment endpoint.
	register_rest_route(
		'autoparts-pro/v1',
		'/fitment/check',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'autoparts_rest_check_fitment',
			'permission_callback' => '__return_true',
			'args'                => array(
				'product_id' => array(
					'required'          => true,
					'type'              => 'integer',
					'sanitize_callback' => 'absint',
				),
				'year'       => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'make'       => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'model'      => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
		)
	);

	register_rest_route(
		'autoparts-pro/v1',
		'/vehicles/makes',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'autoparts_rest_get_makes',
			'permission_callback' => '__return_true',
			'args'                => array(
				'year' => array(
					'required'          => false,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
		)
	);

	register_rest_route(
		'autoparts-pro/v1',
		'/vehicles/models',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'autoparts_rest_get_models',
			'permission_callback' => '__return_true',
			'args'                => array(
				'year' => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'make' => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
		)
	);

	// Chatbot endpoint.
	register_rest_route(
		'autoparts-pro/v1',
		'/chatbot/message',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'autoparts_rest_chatbot_message',
			'permission_callback' => 'autoparts_rest_verify_nonce',
			'args'                => array(
				'message' => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_textarea_field',
				),
				'context' => array(
					'required'          => false,
					'type'              => 'array',
					'default'           => array(),
				),
			),
		)
	);

	// Form submission endpoint.
	register_rest_route(
		'autoparts-pro/v1',
		'/form/submit',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'autoparts_rest_submit_form',
			'permission_callback' => 'autoparts_rest_verify_nonce',
			'args'                => array(
				'form_type'   => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'form_data'   => array(
					'required'          => true,
					'type'              => 'array',
					'sanitize_callback' => 'autoparts_sanitize_form_data',
				),
				'vehicle_data' => array(
					'required'          => false,
					'type'              => 'array',
					'default'           => array(),
				),
			),
		)
	);

	// Builder save endpoint.
	register_rest_route(
		'autoparts-pro/v1',
		'/builder/save',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'autoparts_rest_save_builder',
			'permission_callback' => 'autoparts_rest_admin_verify',
			'args'                => array(
				'page_id'   => array(
					'required'          => true,
					'type'              => 'integer',
					'sanitize_callback' => 'absint',
				),
				'sections'  => array(
					'required'          => true,
					'type'              => 'array',
					'sanitize_callback' => 'wp_kses_post',
				),
			),
		)
	);

	register_rest_route(
		'autoparts-pro/v1',
		'/builder/get/(?P<page_id>\d+)',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'autoparts_rest_get_builder',
			'permission_callback' => 'autoparts_rest_admin_verify',
			'args'                => array(
				'page_id' => array(
					'required'          => true,
					'type'              => 'integer',
					'sanitize_callback' => 'absint',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'autoparts_pro_register_rest_routes' );

/**
 * Verify nonce for REST API.
 */
function autoparts_rest_verify_nonce( $request ) {
	$nonce = $request->get_header( 'X-WP-Nonce' );
	if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
		return new WP_Error( 'rest_forbidden', esc_html__( 'Invalid nonce.', 'autoparts-pro' ), array( 'status' => 403 ) );
	}
	return true;
}

/**
 * Admin verify for REST API.
 */
function autoparts_rest_admin_verify() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return new WP_Error( 'rest_forbidden', esc_html__( 'You do not have permission to do this.', 'autoparts-pro' ), array( 'status' => 403 ) );
	}
	return true;
}

/**
 * Sanitize form data.
 */
function autoparts_sanitize_form_data( $data ) {
	if ( ! is_array( $data ) ) {
		return array();
	}
	
	$sanitized = array();
	foreach ( $data as $key => $value ) {
		$key = sanitize_text_field( $key );
		if ( is_array( $value ) ) {
			$sanitized[ $key ] = autoparts_sanitize_form_data( $value );
		} else {
			$sanitized[ $key ] = sanitize_textarea_field( $value );
		}
	}
	return $sanitized;
}

/**
 * Get wishlist via REST.
 */
function autoparts_rest_get_wishlist( $request ) {
	$items = autoparts_get_wishlist_items();
	$count = count( $items );
	
	return rest_ensure_response(
		array(
			'success' => true,
			'items'   => $items,
			'count'   => $count,
		)
	);
}

/**
 * Update wishlist via REST.
 */
function autoparts_rest_update_wishlist( $request ) {
	$product_id = $request->get_param( 'product_id' );
	$action     = $request->get_param( 'action' );
	
	if ( ! $product_id ) {
		return new WP_Error( 'rest_invalid', esc_html__( 'Invalid product ID.', 'autoparts-pro' ), array( 'status' => 400 ) );
	}
	
	$wishlist = autoparts_get_wishlist_items();
	
	if ( 'add' === $action ) {
		if ( ! in_array( $product_id, $wishlist, true ) ) {
			$wishlist[] = $product_id;
		}
	} elseif ( 'remove' === $action ) {
		$wishlist = array_diff( $wishlist, array( $product_id ) );
	}
	
	$wishlist = array_values( $wishlist );
	
	if ( is_user_logged_in() ) {
		update_user_meta( get_current_user_id(), '_autoparts_wishlist', $wishlist );
	} else {
		setcookie( 'autoparts_wishlist', json_encode( $wishlist ), time() + YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
	}
	
	return rest_ensure_response(
		array(
			'success' => true,
			'items'   => $wishlist,
			'count'   => count( $wishlist ),
		)
	);
}

/**
 * Check fitment via REST.
 */
function autoparts_rest_check_fitment( $request ) {
	$product_id = $request->get_param( 'product_id' );
	$vehicle_data = array(
		'year'  => $request->get_param( 'year' ),
		'make'  => $request->get_param( 'make' ),
		'model' => $request->get_param( 'model' ),
	);
	
	$compatible = autoparts_check_vehicle_compatibility( $product_id, $vehicle_data );
	
	return rest_ensure_response(
		array(
			'success'    => true,
			'compatible' => $compatible,
			'message'    => $compatible ? __( 'This part fits your vehicle!', 'autoparts-pro' ) : __( 'This part may not fit your vehicle.', 'autoparts-pro' ),
		)
	);
}

/**
 * Get makes via REST.
 */
function autoparts_rest_get_makes( $request ) {
	$year = $request->get_param( 'year' );
	
	$args = array(
		'taxonomy'   => 'vehicle_make',
		'hide_empty' => true,
		'orderby'    => 'name',
		'order'      => 'ASC',
	);
	
	if ( $year ) {
		$args['meta_query'] = array(
			array(
				'key'     => '_makes_for_year_' . $year,
				'value'   => '1',
				'compare' => '=',
			),
		);
	}
	
	$makes = get_terms( $args );
	
	if ( is_wp_error( $makes ) ) {
		return rest_ensure_response( array( 'success' => false, 'makes' => array() ) );
	}
	
	$result = array();
	foreach ( $makes as $make ) {
		$result[] = array(
			'id'   => $make->term_id,
			'name' => $make->name,
			'slug' => $make->slug,
		);
	}
	
	return rest_ensure_response(
		array(
			'success' => true,
			'makes'   => $result,
		)
	);
}

/**
 * Get models via REST.
 */
function autoparts_rest_get_models( $request ) {
	$year = $request->get_param( 'year' );
	$make = $request->get_param( 'make' );
	
	$args = array(
		'taxonomy'   => 'vehicle_model',
		'hide_empty' => true,
		'orderby'    => 'name',
		'order'      => 'ASC',
	);
	
	if ( $make ) {
		$make_term = get_term_by( 'slug', $make, 'vehicle_make' );
		if ( $make_term ) {
			$args['parent'] = $make_term->term_id;
		}
	}
	
	$models = get_terms( $args );
	
	if ( is_wp_error( $models ) ) {
		return rest_ensure_response( array( 'success' => false, 'models' => array() ) );
	}
	
	$result = array();
	foreach ( $models as $model ) {
		$result[] = array(
			'id'   => $model->term_id,
			'name' => $model->name,
			'slug' => $model->slug,
		);
	}
	
	return rest_ensure_response(
		array(
			'success' => true,
			'models'  => $result,
		)
	);
}

/**
 * Chatbot message handler.
 */
function autoparts_rest_chatbot_message( $request ) {
	$message = $request->get_param( 'message' );
	$context = $request->get_param( 'context' );
	
	$options = get_option( 'autoparts_theme_options', array() );
	$api_key = isset( $options['chatbot_api_key'] ) ? $options['chatbot_api_key'] : '';
	$api_endpoint = isset( $options['chatbot_api_endpoint'] ) ? $options['chatbot_api_endpoint'] : '';
	
	$response_text = __( "I understand you're asking about: \"{$message}\". Let me help you find the right parts!", 'autoparts-pro' );
	
	if ( $api_key && $api_endpoint ) {
		$api_response = wp_remote_post(
			$api_endpoint,
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $api_key,
					'Content-Type'  => 'application/json',
				),
				'body'    => json_encode(
					array(
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
					)
				),
			)
		);
		
		if ( ! is_wp_error( $api_response ) ) {
			$body = json_decode( wp_remote_retrieve_body( $api_response ), true );
			if ( isset( $body['choices'][0]['message']['content'] ) ) {
				$response_text = $body['choices'][0]['message']['content'];
			}
		}
	}
	
	return rest_ensure_response(
		array(
			'success' => true,
			'message' => $response_text,
		)
	);
}

/**
 * Form submission handler.
 */
function autoparts_rest_submit_form( $request ) {
	$form_type = $request->get_param( 'form_type' );
	$form_data = $request->get_param( 'form_data' );
	$vehicle_data = $request->get_param( 'vehicle_data' );
	
	$post_id = wp_insert_post(
		array(
			'post_type'   => 'form_submission',
			'post_status' => 'publish',
			'post_title'  => sprintf( '%s - %s', ucfirst( $form_type ), date( 'Y-m-d H:i:s' ) ),
		)
	);
	
	if ( is_wp_error( $post_id ) ) {
		return new WP_Error( 'rest_error', esc_html__( 'Failed to save submission.', 'autoparts-pro' ), array( 'status' => 500 ) );
	}
	
	update_post_meta( $post_id, '_form_type', sanitize_text_field( $form_type ) );
	update_post_meta( $post_id, '_form_data', $form_data );
	update_post_meta( $post_id, '_vehicle_data', $vehicle_data );
	update_post_meta( $post_id, '_ip_address', sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '' ) );
	update_post_meta( $post_id, '_user_agent', sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ?? '' ) );
	
	$options = get_option( 'autoparts_theme_options', array() );
	$admin_email = isset( $options['admin_email'] ) ? $options['admin_email'] : get_option( 'admin_email' );
	
	$subject = sprintf( '[AutoParts Pro] New %s Submission', ucfirst( $form_type ) );
	$message = "New form submission:\n\n";
	$message .= "Type: {$form_type}\n";
	$message .= "Date: " . date( 'Y-m-d H:i:s' ) . "\n\n";
	$message .= "Form Data:\n";
	foreach ( $form_data as $key => $value ) {
		$message .= "- {$key}: {$value}\n";
	}
	
	if ( ! empty( $vehicle_data ) ) {
		$message .= "\nVehicle:\n";
		foreach ( $vehicle_data as $key => $value ) {
			$message .= "- {$key}: {$value}\n";
		}
	}
	
	wp_mail( $admin_email, $subject, $message );
	
	return rest_ensure_response(
		array(
			'success' => true,
			'message' => __( 'Thank you! Your submission has been received.', 'autoparts-pro' ),
		)
	);
}

/**
 * Save builder data.
 */
function autoparts_rest_save_builder( $request ) {
	$page_id = $request->get_param( 'page_id' );
	$sections = $request->get_param( 'sections' );
	
	if ( ! get_post( $page_id ) ) {
		return new WP_Error( 'rest_invalid', esc_html__( 'Invalid page ID.', 'autoparts-pro' ), array( 'status' => 400 ) );
	}
	
	update_post_meta( $page_id, '_autoparts_builder_sections', $sections );
	update_post_meta( $page_id, '_autoparts_builder_last_modified', current_time( 'mysql' ) );
	
	return rest_ensure_response(
		array(
			'success' => true,
			'message' => __( 'Page saved successfully!', 'autoparts-pro' ),
		)
	);
}

/**
 * Get builder data.
 */
function autoparts_rest_get_builder( $request ) {
	$page_id = $request->get_param( 'page_id' );
	
	if ( ! get_post( $page_id ) ) {
		return new WP_Error( 'rest_invalid', esc_html__( 'Invalid page ID.', 'autoparts-pro' ), array( 'status' => 400 ) );
	}
	
	$sections = get_post_meta( $page_id, '_autoparts_builder_sections', true );
	$last_modified = get_post_meta( $page_id, '_autoparts_builder_last_modified', true );
	
	return rest_ensure_response(
		array(
			'success'       => true,
			'sections'      => $sections ?: array(),
			'last_modified' => $last_modified,
		)
	);
}
