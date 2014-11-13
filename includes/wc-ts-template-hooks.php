<?php
/**
 * Action/filter hooks used for functions/templates
 *
 * @author 		Vendidero
 * @version     1
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Checkout
 */
if ( get_option( 'woocommerce_trusted_shops_id' ) )
	add_action( 'woocommerce_thankyou', 'woocommerce_ts_template_thankyou', 10, 1 );
?>