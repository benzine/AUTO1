<?php
/**
 * AutoParts Pro Page Builder State Manager
 * 
 * Handles undo/redo functionality and state persistence for the 3-Rail Builder
 *
 * @package AutoParts_Pro
 * @since 1.0.0
 */

namespace AutoParts_Pro\Backoffice;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class State_Manager {
	
	/**
	 * Maximum history states
	 */
	const MAX_HISTORY = 50;
	
	/**
	 * Initialize
	 */
	public static function init() {
		add_action( 'wp_ajax_autoparts_save_state', array( __CLASS__, 'save_state' ) );
		add_action( 'wp_ajax_autoparts_get_state', array( __CLASS__, 'get_state' ) );
		add_action( 'wp_ajax_autoparts_undo', array( __CLASS__, 'undo' ) );
		add_action( 'wp_ajax_autoparts_redo', array( __CLASS__, 'redo' ) );
	}
	
	/**
	 * Get storage key for a page
	 */
	private static function get_storage_key( $page_id ) {
		return '_autoparts_builder_state_' . $page_id;
	}
	
	/**
	 * Save state
	 */
	public static function save_state() {
		check_ajax_referer( 'autoparts_admin_nonce', 'nonce' );
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied', 'autoparts-pro' ) ) );
		}
		
		$page_id   = isset( $_POST['page_id'] ) ? absint( $_POST['page_id'] ) : 0;
		$state     = isset( $_POST['state'] ) ? wp_unslash( $_POST['state'] ) : array();
		$history   = isset( $_POST['history'] ) ? wp_unslash( $_POST['history'] ) : array();
		
		if ( ! $page_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid page ID', 'autoparts-pro' ) ) );
		}
		
		// Limit history size
		$history = array_slice( $history, -self::MAX_HISTORY );
		
		// Store current state and history
		update_post_meta( $page_id, self::get_storage_key( $page_id ), array(
			'current' => $state,
			'history' => $history,
			'pointer' => count( $history ) - 1,
		) );
		
		wp_send_json_success( array( 'message' => __( 'State saved', 'autoparts-pro' ) ) );
	}
	
	/**
	 * Get state
	 */
	public static function get_state() {
		check_ajax_referer( 'autoparts_admin_nonce', 'nonce' );
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied', 'autoparts-pro' ) ) );
		}
		
		$page_id = isset( $_GET['page_id'] ) ? absint( $_GET['page_id'] ) : 0;
		
		if ( ! $page_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid page ID', 'autoparts-pro' ) ) );
		}
		
		$saved = get_post_meta( $page_id, self::get_storage_key( $page_id ), true );
		
		if ( empty( $saved ) ) {
			// Return default empty state
			wp_send_json_success( array(
				'current' => array( 'sections' => array() ),
				'history' => array(),
				'pointer' => -1,
			) );
		}
		
		wp_send_json_success( $saved );
	}
	
	/**
	 * Undo action
	 */
	public static function undo() {
		check_ajax_referer( 'autoparts_admin_nonce', 'nonce' );
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied', 'autoparts-pro' ) ) );
		}
		
		$page_id = isset( $_POST['page_id'] ) ? absint( $_POST['page_id'] ) : 0;
		
		if ( ! $page_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid page ID', 'autoparts-pro' ) ) );
		}
		
		$saved = get_post_meta( $page_id, self::get_storage_key( $page_id ), true );
		
		if ( empty( $saved ) || $saved['pointer'] <= 0 ) {
			wp_send_json_error( array( 'message' => __( 'Nothing to undo', 'autoparts-pro' ) ) );
		}
		
		$saved['pointer']--;
		$saved['current'] = $saved['history'][ $saved['pointer'] ];
		
		update_post_meta( $page_id, self::get_storage_key( $page_id ), $saved );
		
		wp_send_json_success( array(
			'state'   => $saved['current'],
			'pointer' => $saved['pointer'],
		) );
	}
	
	/**
	 * Redo action
	 */
	public static function redo() {
		check_ajax_referer( 'autoparts_admin_nonce', 'nonce' );
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied', 'autoparts-pro' ) ) );
		}
		
		$page_id = isset( $_POST['page_id'] ) ? absint( $_POST['page_id'] ) : 0;
		
		if ( ! $page_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid page ID', 'autoparts-pro' ) ) );
		}
		
		$saved = get_post_meta( $page_id, self::get_storage_key( $page_id ), true );
		
		if ( empty( $saved ) || $saved['pointer'] >= count( $saved['history'] ) - 1 ) {
			wp_send_json_error( array( 'message' => __( 'Nothing to redo', 'autoparts-pro' ) ) );
		}
		
		$saved['pointer']++;
		$saved['current'] = $saved['history'][ $saved['pointer'] ];
		
		update_post_meta( $page_id, self::get_storage_key( $page_id ), $saved );
		
		wp_send_json_success( array(
			'state'   => $saved['current'],
			'pointer' => $saved['pointer'],
		) );
	}
	
	/**
	 * Get section by ID
	 */
	public static function get_section( $page_id, $section_id ) {
		$saved = get_post_meta( $page_id, self::get_storage_key( $page_id ), true );
		
		if ( empty( $saved ) || empty( $saved['current']['sections'] ) ) {
			return null;
		}
		
		foreach ( $saved['current']['sections'] as $section ) {
			if ( $section['id'] === $section_id ) {
				return $section;
			}
		}
		
		return null;
	}
	
	/**
	 * Delete page state
	 */
	public static function delete_state( $page_id ) {
		delete_post_meta( $page_id, self::get_storage_key( $page_id ) );
	}
}

State_Manager::init();
