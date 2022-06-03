<?php
/**
 * Trusted Shops Cancel Review Reminder
 *
 * @author      Vendidero
 * @package     WooCommerceGermanized/Templates
 * @version     1.1
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<!-- Module: WooCommerce Trusted Shops -->
<div class="wc-ts-cancel-review-reminder">
	<p><?php echo wp_kses_post( sprintf( _x( 'If you do not want to receive the review reminder e-mail, please follow the %s link.', 'trusted-shops', 'woocommerce-trusted-shops' ), '<a href="' . esc_url( $link ) . '" target="_blank">' . esc_html_x( 'cancel review reminder', 'trusted-shops', 'woocommerce-trusted-shops' ) . '</a>' ) ); ?></p>
</div>
