<?php
/**
 * AutoParts Pro 3-Rail Page Builder
 */

namespace AutoParts_Pro\Backoffice;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Page_Builder {
	
	private static $modules = array();
	
	public static function init() {
		self::register_modules();
		add_action( 'wp_ajax_autoparts_builder_canvas', array( __CLASS__, 'render_canvas' ) );
		add_action( 'wp_ajax_autoparts_save_section', array( __CLASS__, 'save_section' ) );
		add_action( 'wp_ajax_autoparts_delete_section', array( __CLASS__, 'delete_section' ) );
		add_action( 'wp_ajax_autoparts_reorder_sections', array( __CLASS__, 'reorder_sections' ) );
		add_action( 'wp_ajax_autoparts_add_module', array( __CLASS__, 'add_module' ) );
		add_action( 'wp_ajax_autoparts_update_module', array( __CLASS__, 'update_module' ) );
		add_action( 'wp_ajax_autoparts_delete_module', array( __CLASS__, 'delete_module' ) );
	}
	
	private static function register_modules() {
		self::$modules = array(
			'text' => array( 'name' => 'Text', 'icon' => '📝', 'category' => 'content', 'defaults' => array( 'content' => '' ) ),
			'heading' => array( 'name' => 'Heading', 'icon' => '🔤', 'category' => 'content', 'defaults' => array( 'content' => '', 'level' => 'h2' ) ),
			'button' => array( 'name' => 'Button', 'icon' => '🔘', 'category' => 'interactive', 'defaults' => array( 'text' => '', 'url' => '#' ) ),
			'image' => array( 'name' => 'Image', 'icon' => '🖼️', 'category' => 'media', 'defaults' => array( 'src' => '', 'alt' => '' ) ),
			'video' => array( 'name' => 'Video', 'icon' => '🎬', 'category' => 'media', 'defaults' => array( 'src' => '', 'autoplay' => false ) ),
			'icon' => array( 'name' => 'Icon', 'icon' => '⭐', 'category' => 'media', 'defaults' => array( 'name' => 'star', 'size' => '24' ) ),
			'divider' => array( 'name' => 'Divider', 'icon' => '➖', 'category' => 'content', 'defaults' => array( 'style' => 'solid' ) ),
			'spacer' => array( 'name' => 'Spacer', 'icon' => '↕️', 'category' => 'content', 'defaults' => array( 'height' => '50' ) ),
			'form' => array( 'name' => 'Form', 'icon' => '📋', 'category' => 'interactive', 'defaults' => array( 'fields' => array() ) ),
			'testimonials' => array( 'name' => 'Testimonials', 'icon' => '💬', 'category' => 'content', 'defaults' => array( 'items' => array() ) ),
			'team' => array( 'name' => 'Team', 'icon' => '👥', 'category' => 'content', 'defaults' => array( 'members' => array() ) ),
			'pricing' => array( 'name' => 'Pricing', 'icon' => '💰', 'category' => 'interactive', 'defaults' => array( 'plans' => array() ) ),
			'blog' => array( 'name' => 'Blog', 'icon' => '📰', 'category' => 'content', 'defaults' => array( 'posts_per_page' => 3 ) ),
			'accordion' => array( 'name' => 'Accordion', 'icon' => '📂', 'category' => 'interactive', 'defaults' => array( 'items' => array() ) ),
			'features' => array( 'name' => 'Features', 'icon' => '✨', 'category' => 'content', 'defaults' => array( 'items' => array() ) ),
			'stats' => array( 'name' => 'Stats', 'icon' => '📊', 'category' => 'content', 'defaults' => array( 'items' => array() ) ),
			'progress' => array( 'name' => 'Progress Bars', 'icon' => '📈', 'category' => 'interactive', 'defaults' => array( 'bars' => array() ) ),
			'timeline' => array( 'name' => 'Timeline', 'icon' => '⏱️', 'category' => 'content', 'defaults' => array( 'events' => array() ) ),
			'logo-carousel' => array( 'name' => 'Logo Carousel', 'icon' => '🎠', 'category' => 'media', 'defaults' => array( 'logos' => array() ) ),
			'social-feed' => array( 'name' => 'Social Feed', 'icon' => '📱', 'category' => 'interactive', 'defaults' => array( 'platform' => 'instagram' ) ),
			'search' => array( 'name' => 'Search', 'icon' => '🔍', 'category' => 'interactive', 'defaults' => array( 'post_type' => 'product' ) ),
			'breadcrumbs' => array( 'name' => 'Breadcrumbs', 'icon' => '🍞', 'category' => 'interactive', 'defaults' => array( 'separator' => '/' ) ),
			'custom-html' => array( 'name' => 'Custom HTML', 'icon' => '</>', 'category' => 'content', 'defaults' => array( 'html' => '' ) ),
			'3d-hero' => array( 'name' => '3D Hero', 'icon' => '🎮', 'category' => 'media', 'defaults' => array( 'model' => '', 'labels' => array() ) ),
		);
	}
	
	public static function get_modules() {
		return self::$modules;
	}
	
	public static function render_canvas() {
		check_ajax_referer( 'autoparts_admin_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Permission denied' );
		$page_id = isset( $_GET['page_id'] ) ? absint( $_GET['page_id'] ) : 0;
		$saved = get_post_meta( $page_id, '_autoparts_builder_state_' . $page_id, true );
		$sections = ! empty( $saved['current']['sections'] ) ? $saved['current']['sections'] : array();
		echo '<!DOCTYPE html><html><head><meta charset="' . esc_attr( get_bloginfo( 'charset' ) ) . '">';
		echo '<style>body{margin:0;padding:20px;font-family:sans-serif;background:#f5f5f5}.canvas-section{position:relative;margin-bottom:20px;padding:20px;background:#fff;border:2px solid transparent;border-radius:4px}.canvas-section:hover{border-color:#DC2626}.section-toolbar{position:absolute;top:-40px;left:0;display:flex;gap:5px;opacity:0}.canvas-section:hover .section-toolbar{opacity:1}.section-toolbar button{padding:5px 10px;border:none;border-radius:3px;cursor:pointer;background:#2D2D2D;color:#fff}.empty-canvas{text-align:center;padding:100px;color:#6B7280}</style></head><body>';
		if ( empty( $sections ) ) {
			echo '<div class="empty-canvas"><h3>Start Building Your Page</h3><p>Drag modules from left rail</p></div>';
		} else {
			foreach ( $sections as $index => $section ) {
				echo '<div class="canvas-section" data-section-id="' . esc_attr( $section['id'] ) . '" data-index="' . esc_attr( $index ) . '">';
				echo '<div class="section-toolbar"><button class="move-up">⬆️</button><button class="move-down">⬇️</button><button class="duplicate">📋</button><button class="delete">🗑️</button></div>';
				echo '<div class="section-content">' . self::render_section_content( $section ) . '</div></div>';
			}
		}
		echo '<script>document.querySelectorAll(".canvas-section").forEach(function(s){s.addEventListener("click",function(e){if(e.target.closest("button"))return;document.querySelectorAll(".canvas-section").forEach(x=>x.classList.remove("selected"));this.classList.add("selected");window.parent.postMessage({type:"sectionSelected",sectionId:this.dataset.sectionId},"*")})});document.querySelectorAll(".delete").forEach(function(b){b.addEventListener("click",function(e){e.stopPropagation();if(confirm("Delete?")){window.parent.postMessage({type:"deleteSection",sectionId:this.closest(".canvas-section").dataset.sectionId},"*")}})})</script>';
		echo '</body></html>';
		wp_die();
	}
	
	private static function render_section_content( $section ) {
		$output = '';
		if ( ! empty( $section['modules'] ) ) {
			foreach ( $section['modules'] as $module ) {
				$output .= self::render_module( $module );
			}
		}
		return $output;
	}
	
	private static function render_module( $module ) {
		$type = $module['type'];
		$data = $module['data'];
		switch ( $type ) {
			case 'text': return '<p>' . wp_kses_post( $data['content'] ?? '' ) . '</p>';
			case 'heading':
				$level = in_array( $data['level'] ?? 'h2', array( 'h1','h2','h3','h4','h5','h6' ) ) ? $data['level'] : 'h2';
				return '<' . $level . '>' . esc_html( $data['content'] ?? '' ) . '</' . $level . '>';
			case 'button': return '<a href="' . esc_url( $data['url'] ?? '#' ) . '" class="btn">' . esc_html( $data['text'] ?? '' ) . '</a>';
			case 'image': return '<img src="' . esc_url( $data['src'] ?? '' ) . '" alt="' . esc_attr( $data['alt'] ?? '' ) . '">';
			case 'divider': return '<hr style="border-top:1px solid #6B7280">';
			case 'spacer': return '<div style="height:' . ( $data['height'] ?? 50 ) . 'px"></div>';
			case 'custom-html': return wp_kses_post( $data['html'] ?? '' );
			default: return '<div class="module-' . esc_attr( $type ) . '">[' . esc_html( $type ) . ']</div>';
		}
	}
	
	public static function save_section() {
		check_ajax_referer( 'autoparts_admin_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( array( 'message' => 'Permission denied' ) );
		$page_id = isset( $_POST['page_id'] ) ? absint( $_POST['page_id'] ) : 0;
		$section_data = isset( $_POST['section_data'] ) ? wp_unslash( $_POST['section_data'] ) : array();
		if ( ! $page_id ) wp_send_json_error( array( 'message' => 'Invalid page ID' ) );
		$saved = get_post_meta( $page_id, '_autoparts_builder_state_' . $page_id, true );
		$current = ! empty( $saved['current'] ) ? $saved['current'] : array( 'sections' => array() );
		$found = false;
		foreach ( $current['sections'] as &$section ) {
			if ( $section['id'] === $section_data['id'] ) { $section = $section_data; $found = true; break; }
		}
		if ( ! $found && ! empty( $section_data ) ) $current['sections'][] = $section_data;
		$history = ! empty( $saved['history'] ) ? $saved['history'] : array();
		$history[] = json_decode( json_encode( $current ), true );
		$history = array_slice( $history, -50 );
		update_post_meta( $page_id, '_autoparts_builder_state_' . $page_id, array( 'current' => $current, 'history' => $history, 'pointer' => count( $history ) - 1 ) );
		wp_send_json_success( array( 'message' => 'Section saved' ) );
	}
	
	public static function delete_section() {
		check_ajax_referer( 'autoparts_admin_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( array( 'message' => 'Permission denied' ) );
		$page_id = isset( $_POST['page_id'] ) ? absint( $_POST['page_id'] ) : 0;
		$section_id = isset( $_POST['section_id'] ) ? sanitize_text_field( $_POST['section_id'] ) : '';
		if ( ! $page_id || ! $section_id ) wp_send_json_error( array( 'message' => 'Invalid parameters' ) );
		$saved = get_post_meta( $page_id, '_autoparts_builder_state_' . $page_id, true );
		$current = ! empty( $saved['current'] ) ? $saved['current'] : array( 'sections' => array() );
		$current['sections'] = array_values( array_filter( $current['sections'], function( $s ) use ( $section_id ) { return $s['id'] !== $section_id; } ) );
		$history = ! empty( $saved['history'] ) ? $saved['history'] : array();
		$history[] = json_decode( json_encode( $current ), true );
		$history = array_slice( $history, -50 );
		update_post_meta( $page_id, '_autoparts_builder_state_' . $page_id, array( 'current' => $current, 'history' => $history, 'pointer' => count( $history ) - 1 ) );
		wp_send_json_success( array( 'message' => 'Section deleted' ) );
	}
	
	public static function reorder_sections() {
		check_ajax_referer( 'autoparts_admin_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( array( 'message' => 'Permission denied' ) );
		$page_id = isset( $_POST['page_id'] ) ? absint( $_POST['page_id'] ) : 0;
		$order = isset( $_POST['order'] ) ? wp_unslash( $_POST['order'] ) : array();
		if ( ! $page_id ) wp_send_json_error( array( 'message' => 'Invalid page ID' ) );
		$saved = get_post_meta( $page_id, '_autoparts_builder_state_' . $page_id, true );
		$current = ! empty( $saved['current'] ) ? $saved['current'] : array( 'sections' => array() );
		$ordered = array();
		foreach ( $order as $id ) { foreach ( $current['sections'] as $s ) { if ( $s['id'] === $id ) { $ordered[] = $s; break; } } }
		$current['sections'] = $ordered;
		$history = ! empty( $saved['history'] ) ? $saved['history'] : array();
		$history[] = json_decode( json_encode( $current ), true );
		$history = array_slice( $history, -50 );
		update_post_meta( $page_id, '_autoparts_builder_state_' . $page_id, array( 'current' => $current, 'history' => $history, 'pointer' => count( $history ) - 1 ) );
		wp_send_json_success( array( 'message' => 'Sections reordered' ) );
	}
	
	public static function add_module() {
		check_ajax_referer( 'autoparts_admin_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( array( 'message' => 'Permission denied' ) );
		$page_id = isset( $_POST['page_id'] ) ? absint( $_POST['page_id'] ) : 0;
		$section_id = isset( $_POST['section_id'] ) ? sanitize_text_field( $_POST['section_id'] ) : '';
		$module_type = isset( $_POST['module_type'] ) ? sanitize_text_field( $_POST['module_type'] ) : '';
		if ( ! $page_id || ! $section_id || ! $module_type ) wp_send_json_error( array( 'message' => 'Invalid parameters' ) );
		$saved = get_post_meta( $page_id, '_autoparts_builder_state_' . $page_id, true );
		$current = ! empty( $saved['current'] ) ? $saved['current'] : array( 'sections' => array() );
		$new_module = array( 'id' => 'module-' . uniqid(), 'type' => $module_type, 'data' => self::$modules[ $module_type ]['defaults'] ?? array() );
		foreach ( $current['sections'] as &$section ) {
			if ( $section['id'] === $section_id ) { if ( ! isset( $section['modules'] ) ) $section['modules'] = array(); $section['modules'][] = $new_module; break; }
		}
		$history = ! empty( $saved['history'] ) ? $saved['history'] : array();
		$history[] = json_decode( json_encode( $current ), true );
		$history = array_slice( $history, -50 );
		update_post_meta( $page_id, '_autoparts_builder_state_' . $page_id, array( 'current' => $current, 'history' => $history, 'pointer' => count( $history ) - 1 ) );
		wp_send_json_success( array( 'message' => 'Module added', 'module' => $new_module ) );
	}
	
	public static function update_module() {
		check_ajax_referer( 'autoparts_admin_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( array( 'message' => 'Permission denied' ) );
		$page_id = isset( $_POST['page_id'] ) ? absint( $_POST['page_id'] ) : 0;
		$section_id = isset( $_POST['section_id'] ) ? sanitize_text_field( $_POST['section_id'] ) : '';
		$module_id = isset( $_POST['module_id'] ) ? sanitize_text_field( $_POST['module_id'] ) : '';
		$module_data = isset( $_POST['module_data'] ) ? wp_unslash( $_POST['module_data'] ) : array();
		if ( ! $page_id || ! $section_id || ! $module_id ) wp_send_json_error( array( 'message' => 'Invalid parameters' ) );
		$saved = get_post_meta( $page_id, '_autoparts_builder_state_' . $page_id, true );
		$current = ! empty( $saved['current'] ) ? $saved['current'] : array( 'sections' => array() );
		foreach ( $current['sections'] as &$section ) {
			if ( $section['id'] === $section_id && ! empty( $section['modules'] ) ) {
				foreach ( $section['modules'] as &$module ) { if ( $module['id'] === $module_id ) { $module['data'] = array_merge( $module['data'], $module_data ); break 2; } }
			}
		}
		$history = ! empty( $saved['history'] ) ? $saved['history'] : array();
		$history[] = json_decode( json_encode( $current ), true );
		$history = array_slice( $history, -50 );
		update_post_meta( $page_id, '_autoparts_builder_state_' . $page_id, array( 'current' => $current, 'history' => $history, 'pointer' => count( $history ) - 1 ) );
		wp_send_json_success( array( 'message' => 'Module updated' ) );
	}
	
	public static function delete_module() {
		check_ajax_referer( 'autoparts_admin_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( array( 'message' => 'Permission denied' ) );
		$page_id = isset( $_POST['page_id'] ) ? absint( $_POST['page_id'] ) : 0;
		$section_id = isset( $_POST['section_id'] ) ? sanitize_text_field( $_POST['section_id'] ) : '';
		$module_id = isset( $_POST['module_id'] ) ? sanitize_text_field( $_POST['module_id'] ) : '';
		if ( ! $page_id || ! $section_id || ! $module_id ) wp_send_json_error( array( 'message' => 'Invalid parameters' ) );
		$saved = get_post_meta( $page_id, '_autoparts_builder_state_' . $page_id, true );
		$current = ! empty( $saved['current'] ) ? $saved['current'] : array( 'sections' => array() );
		foreach ( $current['sections'] as &$section ) {
			if ( $section['id'] === $section_id && ! empty( $section['modules'] ) ) {
				$section['modules'] = array_values( array_filter( $section['modules'], function( $m ) use ( $module_id ) { return $m['id'] !== $module_id; } ) );
				break;
			}
		}
		$history = ! empty( $saved['history'] ) ? $saved['history'] : array();
		$history[] = json_decode( json_encode( $current ), true );
		$history = array_slice( $history, -50 );
		update_post_meta( $page_id, '_autoparts_builder_state_' . $page_id, array( 'current' => $current, 'history' => $history, 'pointer' => count( $history ) - 1 ) );
		wp_send_json_success( array( 'message' => 'Module deleted' ) );
	}
}

Page_Builder::init();
