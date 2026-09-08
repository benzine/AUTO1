<?php
/**
 * AutoParts Pro Theme Footer
 * 
 * @package AutoParts_Pro
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$autoparts_options = get_option( 'autoparts_theme_options', array() );
$chatbot_enabled = isset( $autoparts_options['chatbot_enabled'] ) ? $autoparts_options['chatbot_enabled'] : true;
$show_back_to_top = isset( $autoparts_options['show_back_to_top'] ) ? $autoparts_options['show_back_to_top'] : true;
$back_to_top_icon = isset( $autoparts_options['back_to_top_icon'] ) ? $autoparts_options['back_to_top_icon'] : 'arrow';

?>
	</div><!-- #content -->

	<footer id="colophon" class="site-footer">
		<div class="footer-widgets">
			<div class="container">
				<div class="footer-grid">
					<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
						<div class="footer-widget-area">
							<?php dynamic_sidebar( 'footer-1' ); ?>
						</div>
					<?php endif; ?>

					<?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
						<div class="footer-widget-area">
							<?php dynamic_sidebar( 'footer-2' ); ?>
						</div>
					<?php endif; ?>

					<?php if ( is_active_sidebar( 'footer-3' ) ) : ?>
						<div class="footer-widget-area">
							<?php dynamic_sidebar( 'footer-3' ); ?>
						</div>
					<?php endif; ?>

					<?php if ( is_active_sidebar( 'footer-4' ) ) : ?>
						<div class="footer-widget-area">
							<?php dynamic_sidebar( 'footer-4' ); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="footer-bottom">
			<div class="container">
				<div class="footer-bottom-content">
					<div class="copyright">
						<p>&copy; <?php echo esc_html( date( 'Y' ) ); ?> <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>. <?php esc_html_e( 'All Rights Reserved.', 'autoparts-pro' ); ?></p>
					</div>
					
					<div class="footer-payment-methods">
						<i class="ap-icon-cc-visa"></i>
						<i class="ap-icon-cc-mastercard"></i>
						<i class="ap-icon-cc-amex"></i>
						<i class="ap-icon-cc-paypal"></i>
						<i class="ap-icon-cc-apple-pay"></i>
					</div>

					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'menu_id'        => 'footer-menu',
							'menu_class'     => 'footer-menu',
							'depth'          => 1,
						)
					);
					?>
				</div>
			</div>
		</div>
	</footer>

	<?php if ( $show_back_to_top ) : ?>
	<button id="back-to-top" class="back-to-top" aria-label="<?php esc_attr_e( 'Back to top', 'autoparts-pro' ); ?>" data-icon="<?php echo esc_attr( $back_to_top_icon ); ?>">
		<?php if ( 'gear' === $back_to_top_icon ) : ?>
			<i class="ap-icon-gear"></i>
		<?php elseif ( 'rocket' === $back_to_top_icon ) : ?>
			<i class="ap-icon-rocket"></i>
		<?php else : ?>
			<i class="ap-icon-arrow-up"></i>
		<?php endif; ?>
	</button>
	<?php endif; ?>

	<?php if ( $chatbot_enabled ) : ?>
		<?php get_template_part( 'template-parts/components/chatbot' ); ?>
	<?php endif; ?>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
