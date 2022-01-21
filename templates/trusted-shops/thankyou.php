<?php
/**
 * Thankyou page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     4.0.13
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$order = wc_get_order( $order_id );
?>
<!-- Module: WooCommerce Trusted Shops -->
<div id="trustedShopsCheckout" style="display: none;">
	<span id="tsCheckoutOrderNr"><?php echo $order->get_order_number(); ?></span>
	<span id="tsCheckoutBuyerEmail"><?php echo wc_ts_get_crud_data( $order, 'billing_email' ); ?></span>
	<span id="tsCheckoutBuyerId"><?php echo wc_ts_get_crud_data( $order, 'user_id' ); ?></span>
	<span id="tsCheckoutOrderAmount"><?php echo $order->get_total(); ?></span>
	<span id="tsCheckoutOrderCurrency"><?php echo wc_ts_get_order_currency( $order ); ?></span>
	<span id="tsCheckoutOrderPaymentType"><?php echo $order->get_payment_method_title(); ?></span>
	<span id="tsCheckoutOrderEstDeliveryDate"></span>
	<?php if ( $plugin->is_product_reviews_enabled() ) : ?>
		<?php foreach( $order->get_items() as $item_id => $item ) : 

            if ( ! is_a( $item, 'WC_Order_Item_Product' ) ) {
                continue;
            }

			$org_product    = $item->get_product();
		    $parent_product = $org_product;

	        if ( ! $org_product ) {
	            continue;
	        }
			
			// Currently not supporting reviews for variations	
			if ( $org_product->is_type( 'variation' ) ) {
				$parent_product = wc_get_product( wc_ts_get_crud_data( $org_product, 'parent' ) );
			}

            $sku = $parent_product->get_sku() ? $parent_product->get_sku() : wc_ts_get_crud_data( $parent_product, 'id' );
			?>
			<span class="tsCheckoutProductItem">
				<span class="tsCheckoutProductUrl"><?php echo get_permalink( wc_ts_get_crud_data( $parent_product, 'id' ) ); ?></span>
				<span class="tsCheckoutProductImageUrl"><?php echo $plugin->get_product_image( $org_product ); ?></span>
				<span class="tsCheckoutProductName"><?php echo get_the_title( wc_ts_get_crud_data( $parent_product, 'id' ) ); ?></span>
                <span class="tsCheckoutProductSKU"><?php echo $sku; ?></span>
                <span class="tsCheckoutProductGTIN"><?php echo apply_filters( 'woocommerce_gzd_trusted_shops_product_gtin', ( $plugin->get_product_gtin( $parent_product ) ? $plugin->get_product_gtin( $parent_product ) : $sku ), $parent_product ); ?></span>
                <span class="tsCheckoutProductBrand"><?php echo apply_filters( 'woocommerce_gzd_trusted_shops_product_brand', $plugin->get_product_brand( $parent_product ), $parent_product ); ?></span>
                <span class="tsCheckoutProductMPN"><?php echo apply_filters( 'woocommerce_gzd_trusted_shops_product_mpn', $plugin->get_product_mpn( $parent_product ), $parent_product ); ?></span>
            </span>
		<?php endforeach; ?>
	<?php endif; ?>
</div>