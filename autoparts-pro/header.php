<?php
/**
 * AutoParts Pro Theme Header
 * 
 * @package AutoParts_Pro
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$autoparts_options = get_option( 'autoparts_theme_options', array() );
$show_vehicle_selector = isset( $autoparts_options['header_vehicle_selector'] ) ? $autoparts_options['header_vehicle_selector'] : true;
$show_wishlist = isset( $autoparts_options['enable_wishlist'] ) ? $autoparts_options['enable_wishlist'] : true;
$chatbot_enabled = isset( $autoparts_options['chatbot_enabled'] ) ? $autoparts_options['chatbot_enabled'] : true;
$custom_cursor = isset( $autoparts_options['custom_cursor'] ) ? $autoparts_options['custom_cursor'] : true;

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'autoparts-pro' ); ?></a>
	
	<?php if ( $custom_cursor ) : ?>
	<div class="custom-cursor" data-cursor="default">
		<div class="cursor-dot"></div>
		<div class="cursor-ring"></div>
		<div class="cursor-trail"></div>
	</div>
	<?php endif; ?>

	<header id="masthead" class="site-header <?php echo is_front_page() ? 'header-transparent' : ''; ?>">
		<div class="header-top-bar">
			<div class="container">
				<div class="top-bar-content">
					<div class="contact-info">
						<span class="info-item">
							<i class="ap-icon-phone"></i>
							<?php echo esc_html( isset( $autoparts_options['phone_number'] ) ? $autoparts_options['phone_number'] : '+1 (800) PARTS-24' ); ?>
						</span>
						<span class="info-item">
							<i class="ap-icon-email"></i>
							<?php echo esc_html( isset( $autoparts_options['email_address'] ) ? $autoparts_options['email_address'] : 'support@autopartspro.com' ); ?>
						</span>
					</div>
					<div class="header-actions">
						<?php if ( function_exists( 'woocommerce' ) ) : ?>
							<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>" class="order-tracking-link">
								<i class="ap-icon-package"></i>
								<span><?php esc_html_e( 'Track Order', 'autoparts-pro' ); ?></span>
							</a>
						<?php endif; ?>
						
						<button class="theme-toggle" aria-label="<?php esc_attr_e( 'Toggle dark mode', 'autoparts-pro' ); ?>">
							<span class="sun-icon">
								<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
									<circle cx="12" cy="12" r="5"/>
									<path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
								</svg>
							</span>
							<span class="moon-icon">
								<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
									<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
								</svg>
							</span>
						</button>
						
						<?php if ( $show_wishlist && function_exists( 'autoparts_get_wishlist_count' ) ) : ?>
							<a href="<?php echo esc_url( get_permalink( autoparts_get_wishlist_page_id() ) ); ?>" class="wishlist-btn header-wishlist" data-wishlist-count="<?php echo esc_attr( autoparts_get_wishlist_count() ); ?>">
								<i class="ap-icon-heart"></i>
								<span class="wishlist-count"><?php echo esc_html( autoparts_get_wishlist_count() ); ?></span>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

		<div class="header-main">
			<div class="container">
				<div class="header-wrapper">
					<div class="site-branding">
						<?php
						$logo_light = get_theme_mod( 'logo_light' );
						$logo_dark = get_theme_mod( 'logo_dark' );
						$site_title = get_bloginfo( 'name' );
						
						if ( $logo_light || $logo_dark ) :
							?>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo-link">
								<img src="<?php echo esc_url( $logo_light ); ?>" alt="<?php echo esc_attr( $site_title ); ?>" class="logo-light">
								<?php if ( $logo_dark ) : ?>
									<img src="<?php echo esc_url( $logo_dark ); ?>" alt="<?php echo esc_attr( $site_title ); ?>" class="logo-dark">
								<?php endif; ?>
							</a>
						<?php else : ?>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo-text">
								<span class="logo-primary"><?php esc_html_e( 'AUTOPARTS', 'autoparts-pro' ); ?></span>
								<span class="logo-accent"><?php esc_html_e( 'PRO', 'autoparts-pro' ); ?></span>
							</a>
						<?php endif; ?>
						</div>

					<?php if ( $show_vehicle_selector ) : ?>
						<div class="vehicle-selector-container">
							<?php get_template_part( 'template-parts/components/vehicle', 'selector' ); ?>
						</div>
					<?php endif; ?>

					<nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'autoparts-pro' ); ?>">
						<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
							<span class="hamburger-line"></span>
							<span class="hamburger-line"></span>
							<span class="hamburger-line"></span>
							<span class="menu-text"><?php esc_html_e( 'Menu', 'autoparts-pro' ); ?></span>
						</button>
						
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'primary',
								'menu_id'        => 'primary-menu',
								'menu_class'     => 'primary-menu',
								'container'      => false,
								'depth'          => 3,
								'walker'         => new AutoParts_Walker_Nav_Menu(),
							)
						);
						?>
					</nav>

					<div class="header-right">
						<button class="search-toggle" aria-label="<?php esc_attr_e( 'Toggle search', 'autoparts-pro' ); ?>">
							<i class="ap-icon-search"></i>
						</button>
						
						<?php if ( function_exists( 'woocommerce' ) ) : ?>
							<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="cart-toggle" aria-label="<?php esc_attr_e( 'View cart', 'autoparts-pro' ); ?>">
								<i class="ap-icon-cart"></i>
								<span class="cart-count"><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?></span>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

		<div class="search-overlay">
			<div class="search-container">
				<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Search parts, brands, or part numbers...', 'autoparts-pro' ); ?>" value="<?php echo get_search_query(); ?>" name="s">
					<button type="submit" class="search-submit">
						<i class="ap-icon-search"></i>
						<span class="screen-reader-text"><?php esc_html_e( 'Search', 'autoparts-pro' ); ?></span>
					</button>
				</form>
				<button class="search-close" aria-label="<?php esc_attr_e( 'Close search', 'autoparts-pro' ); ?>">
					<i class="ap-icon-close"></i>
				</button>
			</div>
		</div>
	</header>

	<div id="content" class="site-content">
