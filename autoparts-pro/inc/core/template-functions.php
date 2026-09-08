<?php
/**
 * Template Functions
 *
 * @package AutoParts_Pro
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get wishlist page ID.
 */
function autoparts_get_wishlist_page_id() {
	return get_option( 'autoparts_wishlist_page_id', 0 );
}

/**
 * Get wishlist count for current user/session.
 */
function autoparts_get_wishlist_count() {
	if ( is_user_logged_in() ) {
		$wishlist = get_user_meta( get_current_user_id(), '_autoparts_wishlist', true );
		return is_array( $wishlist ) ? count( $wishlist ) : 0;
	} else {
		$session_wishlist = isset( $_COOKIE['autoparts_wishlist'] ) ? json_decode( stripslashes( $_COOKIE['autoparts_wishlist'] ), true ) : array();
		return is_array( $session_wishlist ) ? count( $session_wishlist ) : 0;
	}
}

/**
 * Get wishlist items.
 */
function autoparts_get_wishlist_items() {
	if ( is_user_logged_in() ) {
		return get_user_meta( get_current_user_id(), '_autoparts_wishlist', true ) ?: array();
	} else {
		return isset( $_COOKIE['autoparts_wishlist'] ) ? json_decode( stripslashes( $_COOKIE['autoparts_wishlist'] ), true ) : array();
	}
}

/**
 * Check if product is in wishlist.
 */
function autoparts_is_in_wishlist( $product_id ) {
	$wishlist = autoparts_get_wishlist_items();
	return in_array( absint( $product_id ), $wishlist, true );
}

/**
 * Get compatibility badge HTML.
 */
function autoparts_get_compatibility_badge( $product_id = null ) {
	if ( ! $product_id ) {
		global $product;
		$product_id = $product ? $product->get_id() : 0;
	}
	
	$selected_vehicle = get_transient( 'autoparts_selected_vehicle_' . session_id() );
	if ( ! $selected_vehicle ) {
		return '';
	}
	
	$compatible = autoparts_check_vehicle_compatibility( $product_id, $selected_vehicle );
	
	if ( $compatible ) {
		return '<span class="badge badge-compatible"><i class="ap-icon-check"></i> ' . esc_html__( 'Fits Your Vehicle', 'autoparts-pro' ) . '</span>';
	}
	
	return '<span class="badge badge-not-compatible">' . esc_html__( 'Check Fitment', 'autoparts-pro' ) . '</span>';
}

/**
 * Check vehicle compatibility for a product.
 */
function autoparts_check_vehicle_compatibility( $product_id, $vehicle_data ) {
	if ( ! $product_id || empty( $vehicle_data ) ) {
		return false;
	}
	
	$product_years  = wp_get_post_terms( $product_id, 'vehicle_year', array( 'fields' => 'slugs' ) );
	$product_makes  = wp_get_post_terms( $product_id, 'vehicle_make', array( 'fields' => 'slugs' ) );
	$product_models = wp_get_post_terms( $product_id, 'vehicle_model', array( 'fields' => 'slugs' ) );
	
	$year_match  = empty( $product_years ) || in_array( $vehicle_data['year'], $product_years, true );
	$make_match  = empty( $product_makes ) || in_array( $vehicle_data['make'], $product_makes, true );
	$model_match = empty( $product_models ) || in_array( $vehicle_data['model'], $product_models, true );
	
	return $year_match && $make_match && $model_match;
}

/**
 * Get vehicle selector HTML.
 */
function autoparts_get_vehicle_selector() {
	ob_start();
	?>
	<div class="vehicle-selector" id="vehicle-selector">
		<select class="vehicle-select" id="vehicle-year" data-placeholder="<?php esc_attr_e( 'Year', 'autoparts-pro' ); ?>">
			<option value=""><?php esc_html_e( 'Select Year', 'autoparts-pro' ); ?></option>
			<?php
			$current_year = date( 'Y' );
			for ( $y = $current_year + 1; $y >= 1980; $y-- ) :
				?>
				<option value="<?php echo esc_attr( $y ); ?>"><?php echo esc_html( $y ); ?></option>
			<?php endfor; ?>
		</select>
		
		<select class="vehicle-select" id="vehicle-make" disabled>
			<option value=""><?php esc_html_e( 'Select Make', 'autoparts-pro' ); ?></option>
		</select>
		
		<select class="vehicle-select" id="vehicle-model" disabled>
			<option value=""><?php esc_html_e( 'Select Model', 'autoparts-pro' ); ?></option>
		</select>
		
		<select class="vehicle-select" id="vehicle-engine" disabled>
			<option value=""><?php esc_html_e( 'Select Engine', 'autoparts-pro' ); ?></option>
		</select>
		
		<button class="btn btn-primary btn-sm" id="vehicle-save">
			<?php esc_html_e( 'Save Garage', 'autoparts-pro' ); ?>
		</button>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Get chatbot widget HTML.
 */
function autoparts_get_chatbot_widget() {
	$options = get_option( 'autoparts_theme_options', array() );
	$chatbot_enabled = isset( $options['chatbot_enabled'] ) && $options['chatbot_enabled'];
	$chatbot_avatar = isset( $options['chatbot_avatar'] ) ? $options['chatbot_avatar'] : '';
	$chatbot_greeting = isset( $options['chatbot_greeting'] ) ? $options['chatbot_greeting'] : __( "Hi! I'm your AutoParts assistant. How can I help you today?", 'autoparts-pro' );
	
	if ( ! $chatbot_enabled ) {
		return;
	}
	?>
	<div class="chatbot-widget" id="chatbot-widget">
		<button class="chatbot-button" id="chatbot-toggle" aria-label="<?php esc_attr_e( 'Toggle chat', 'autoparts-pro' ); ?>">
			<i class="ap-icon-chat"></i>
		</button>
		
		<div class="chatbot-window" id="chatbot-window">
			<div class="chatbot-header">
				<div class="chatbot-title">
					<div class="chatbot-avatar">
						<?php if ( $chatbot_avatar ) : ?>
							<img src="<?php echo esc_url( $chatbot_avatar ); ?>" alt="<?php esc_attr_e( 'Chatbot', 'autoparts-pro' ); ?>">
						<?php else : ?>
							<i class="ap-icon-robot"></i>
						<?php endif; ?>
					</div>
					<span><?php esc_html_e( 'AutoParts Assistant', 'autoparts-pro' ); ?></span>
				</div>
				<button class="chatbot-close" id="chatbot-close" aria-label="<?php esc_attr_e( 'Close chat', 'autoparts-pro' ); ?>">
					<i class="ap-icon-close"></i>
				</button>
			</div>
			
			<div class="chatbot-messages" id="chatbot-messages">
				<div class="message bot">
					<?php echo esc_html( $chatbot_greeting ); ?>
				</div>
			</div>
			
			<div class="chatbot-input">
				<input type="text" id="chatbot-input-field" placeholder="<?php esc_attr_e( 'Ask about parts, fitment, orders...', 'autoparts-pro' ); ?>" aria-label="<?php esc_attr_e( 'Chat message', 'autoparts-pro' ); ?>">
				<button class="chatbot-send" id="chatbot-send" aria-label="<?php esc_attr_e( 'Send message', 'autoparts-pro' ); ?>">
					<i class="ap-icon-send"></i>
				</button>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Get breadcrumb HTML.
 */
function autoparts_get_breadcrumb() {
	if ( is_front_page() ) {
		return;
	}
	
	echo '<nav class="breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'autoparts-pro' ) . '">';
	echo '<ul class="breadcrumb-list">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'autoparts-pro' ) . '</a></li>';
	
	if ( is_category() || is_single() ) {
		the_archive_title( '<li>', '</li>' );
	} elseif ( is_single() ) {
		$categories = get_the_category();
		if ( $categories ) {
			echo '<li><a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">' . esc_html( $categories[0]->name ) . '</a></li>';
		}
		echo '<li>' . get_the_title() . '</li>';
	} elseif ( is_page() ) {
		echo '<li>' . get_the_title() . '</li>';
	} elseif ( is_search() ) {
		echo '<li>' . sprintf( esc_html__( 'Search Results for "%s"', 'autoparts-pro' ), get_search_query() ) . '</li>';
	} elseif ( is_404() ) {
		echo '<li>' . esc_html__( '404 Not Found', 'autoparts-pro' ) . '</li>';
	}
	
	echo '</ul>';
	echo '</nav>';
}

/**
 * Get social share buttons.
 */
function autoparts_get_social_share( $url = null, $title = null ) {
	if ( ! $url ) {
		$url = get_permalink();
	}
	if ( ! $title ) {
		$title = get_the_title();
	}
	
	$encoded_url  = urlencode( $url );
	$encoded_title = urlencode( $title );
	?>
	<div class="social-share">
		<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener" class="share-facebook" aria-label="<?php esc_attr_e( 'Share on Facebook', 'autoparts-pro' ); ?>">
			<i class="ap-icon-facebook"></i>
		</a>
		<a href="https://twitter.com/intent/tweet?url=<?php echo esc_url( $url ); ?>&text=<?php echo esc_attr( $title ); ?>" target="_blank" rel="noopener" class="share-twitter" aria-label="<?php esc_attr_e( 'Share on Twitter', 'autoparts-pro' ); ?>">
			<i class="ap-icon-twitter"></i>
		</a>
		<a href="https://pinterest.com/pin/create/button/?url=<?php echo esc_url( $url ); ?>&description=<?php echo esc_attr( $title ); ?>" target="_blank" rel="noopener" class="share-pinterest" aria-label="<?php esc_attr_e( 'Share on Pinterest', 'autoparts-pro' ); ?>">
			<i class="ap-icon-pinterest"></i>
		</a>
		<a href="mailto:?subject=<?php echo esc_attr( $title ); ?>&body=<?php echo esc_url( $url ); ?>" class="share-email" aria-label="<?php esc_attr_e( 'Share via email', 'autoparts-pro' ); ?>">
			<i class="ap-icon-email"></i>
		</a>
		<button class="share-copy" data-url="<?php echo esc_url( $url ); ?>" aria-label="<?php esc_attr_e( 'Copy link', 'autoparts-pro' ); ?>">
			<i class="ap-icon-link"></i>
		</button>
	</div>
	<?php
}

/**
 * Get stock status HTML.
 */
function autoparts_get_stock_status( $product ) {
	if ( ! $product ) {
		return '';
	}
	
	$stock_status = $product->get_stock_status();
	$stock_qty    = $product->get_stock_quantity();
	
	switch ( $stock_status ) {
		case 'instock':
			$class = 'stock-in';
			$text  = $stock_qty && $stock_qty < 10 
				? sprintf( esc_html__( 'Only %d left in stock', 'autoparts-pro' ), $stock_qty )
				: esc_html__( 'In Stock', 'autoparts-pro' );
			break;
		case 'onbackorder':
			$class = 'stock-low';
			$text  = esc_html__( 'Available on Backorder', 'autoparts-pro' );
			break;
		default:
			$class = 'stock-out';
			$text  = esc_html__( 'Out of Stock', 'autoparts-pro' );
	}
	
	return '<span class="stock-status ' . esc_attr( $class ) . '">' . esc_html( $text ) . '</span>';
}

/**
 * Get flash deal countdown.
 */
function autoparts_get_flash_deal_countdown( $end_date ) {
	$end_timestamp = strtotime( $end_date );
	$now = time();
	
	if ( $end_timestamp <= $now ) {
		return '';
	}
	
	$diff = $end_timestamp - $now;
	$days = floor( $diff / DAY_IN_SECONDS );
	$hours = floor( ( $diff % DAY_IN_SECONDS ) / HOUR_IN_SECONDS );
	$minutes = floor( ( $diff % HOUR_IN_SECONDS ) / MINUTE_IN_SECONDS );
	$seconds = $diff % MINUTE_IN_SECONDS;
	?>
	<div class="flash-countdown" data-end="<?php echo esc_attr( $end_timestamp ); ?>">
		<div class="countdown-segment">
			<span class="countdown-number" data-days><?php echo str_pad( $days, 2, '0', STR_PAD_LEFT ); ?></span>
			<span class="countdown-label"><?php esc_html_e( 'Days', 'autoparts-pro' ); ?></span>
		</div>
		<div class="countdown-segment">
			<span class="countdown-number" data-hours><?php echo str_pad( $hours, 2, '0', STR_PAD_LEFT ); ?></span>
			<span class="countdown-label"><?php esc_html_e( 'Hours', 'autoparts-pro' ); ?></span>
		</div>
		<div class="countdown-segment">
			<span class="countdown-number" data-minutes><?php echo str_pad( $minutes, 2, '0', STR_PAD_LEFT ); ?></span>
			<span class="countdown-label"><?php esc_html_e( 'Minutes', 'autoparts-pro' ); ?></span>
		</div>
		<div class="countdown-segment">
			<span class="countdown-number" data-seconds><?php echo str_pad( $seconds, 2, '0', STR_PAD_LEFT ); ?></span>
			<span class="countdown-label"><?php esc_html_e( 'Seconds', 'autoparts-pro' ); ?></span>
		</div>
	</div>
	<?php
}

/**
 * Get builder pages for admin.
 */
function autoparts_get_builder_pages() {
	$pages = get_pages( array( 'post_status' => 'publish' ) );
	$result = array();
	
	foreach ( $pages as $page ) {
		$result[] = array(
			'id'    => $page->ID,
			'title' => $page->post_title,
			'url'   => get_permalink( $page->ID ),
		);
	}
	
	return $result;
}
