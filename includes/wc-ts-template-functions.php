<?php
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Template functions
 *
 * @author 		Vendidero
 * @version     1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! function_exists( 'woocommerce_ts_template_thankyou' ) ) {

	/**
	 * Add Trusted Shops template to order success
	 */
	function woocommerce_ts_template_thankyou( $order_id ) {
		wc_get_template( 'trusted-shops/thankyou.php', array( 'order_id' => $order_id ) );
	}

}

?>