<?php
/**
 * Core Functions
 *
 * WC_GZD core functions.
 *
 * @author 		Vendidero
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function wc_ts_get_crud_data( $object, $key, $suppress_suffix = false ) {

	if ( is_a( $object, 'WC_GZD_Product' ) ) {
		$object = $object->get_wc_product();
	}

	$value = null;

	$getter = substr( $key, 0, 3 ) === "get" ? $key : "get_$key";
	$key = substr( $key, 0, 3 ) === "get" ? substr( $key, 3 ) : $key;

	if ( 'id' === $key && is_callable( array( $object, 'is_type' ) ) && $object->is_type( 'variation' ) && ! WC_TS_Dependencies::instance()->woocommerce_version_supports_crud() ) {
		$key = 'variation_id';
	} elseif ( 'parent' === $key && is_callable( array( $object, 'is_type' ) ) && $object->is_type( 'variation' ) && ! WC_TS_Dependencies::instance()->woocommerce_version_supports_crud() ) {
		// Set getter to parent so that it is not being used for pre 2.7
		$key = 'id';
		$getter = 'parent';
	}

	$getter_mapping = array(
		'parent' => 'get_parent_id',
		'completed_date' => 'get_date_completed',
		'order_date' => 'get_date_created',
		'product_type' => 'get_type',
		'order_type' => 'get_type',
	);

	if ( array_key_exists( $key, $getter_mapping ) ) {
		$getter = $getter_mapping[ $key ];
	}

	if ( is_callable( array( $object, $getter ) ) ) {
		$reflection = new ReflectionMethod( $object, $getter );
		if ( $reflection->isPublic() ) {
			$value = $object->{$getter}();
		}
	} elseif ( WC_TS_Dependencies::instance()->woocommerce_version_supports_crud() ) {
		// Prefix meta if suppress_suffix is not set
		if ( substr( $key, 0, 1 ) !== '_' && ! $suppress_suffix )
			$key = '_' . $key;

		$value = $object->get_meta( $key );
	} else {
		$key = substr( $key, 0, 1 ) === "_" ? substr( $key, 1 ) : $key;
		$value = $object->{$key};
	}

	return $value;
}

function wc_ts_get_order_currency( $order ) {
	if ( WC_TS_Dependencies::instance()->woocommerce_version_supports_crud() )
		return $order->get_currency();
	return $order->get_order_currency();
}