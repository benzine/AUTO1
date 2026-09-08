<?php
/**
 * State Manager - Handles undo/redo and state persistence
 */
if (!defined('ABSPATH')) exit;

class AutoParts_Pro_State_Manager {
    private static $instance = null;
    private $history = array();
    private $current_index = -1;
    private $max_history = 50;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        add_action('wp_ajax_app_undo', array($this, 'ajax_undo'));
        add_action('wp_ajax_app_redo', array($this, 'ajax_redo'));
        add_action('wp_ajax_app_get_history', array($this, 'ajax_get_history'));
    }
    
    public function push_state($state, $page_id) {
        // Remove any future states if we're not at the end
        while ($this->current_index < count($this->history) - 1) {
            array_pop($this->history);
        }
        
        $this->history[] = array(
            'state' => $state,
            'page_id' => $page_id,
            'timestamp' => time()
        );
        
        // Limit history size
        if (count($this->history) > $this->max_history) {
            array_shift($this->history);
        } else {
            $this->current_index++;
        }
        
        return $this->current_index;
    }
    
    public function undo($page_id) {
        if ($this->current_index > 0) {
            $this->current_index--;
            return $this->history[$this->current_index];
        }
        return false;
    }
    
    public function redo($page_id) {
        if ($this->current_index < count($this->history) - 1) {
            $this->current_index++;
            return $this->history[$this->current_index];
        }
        return false;
    }
    
    public function ajax_undo() {
        check_ajax_referer('app_builder_nonce', 'nonce');
        $page_id = isset($_POST['page_id']) ? absint($_POST['page_id']) : 0;
        $result = $this->undo($page_id);
        if ($result) {
            wp_send_json_success(array('state' => $result['state'], 'index' => $this->current_index));
        } else {
            wp_send_json_error(array('message' => __('Nothing to undo', 'autoparts-pro')));
        }
    }
    
    public function ajax_redo() {
        check_ajax_referer('app_builder_nonce', 'nonce');
        $page_id = isset($_POST['page_id']) ? absint($_POST['page_id']) : 0;
        $result = $this->redo($page_id);
        if ($result) {
            wp_send_json_success(array('state' => $result['state'], 'index' => $this->current_index));
        } else {
            wp_send_json_error(array('message' => __('Nothing to redo', 'autoparts-pro')));
        }
    }
    
    public function ajax_get_history() {
        check_ajax_referer('app_builder_nonce', 'nonce');
        wp_send_json_success(array(
            'current_index' => $this->current_index,
            'total' => count($this->history)
        ));
    }
}

AutoParts_Pro_State_Manager::get_instance();
