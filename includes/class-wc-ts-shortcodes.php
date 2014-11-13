<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Adds Germanized Shortcodes
 *
 * @class 		WC_GZD_Shortcodes
 * @version		1.0.0
 * @author 		Vendidero
 */
class WC_TS_Shortcodes {
	
	/**
	 * Initializes Shortcodes
	 */
	public static function init() {
		
		// Define shortcodes
		$shortcodes = array(
			'trusted_shops_rich_snippets'=> __CLASS__ . '::trusted_shops_rich_snippets',
			'trusted_shops_reviews'		 => __CLASS__ . '::trusted_shops_reviews',
			'trusted_shops_badge'		 => __CLASS__ . '::trusted_shops_badge',
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}

	}

	/**
	 * Returns Trusted Shops rich snippet review html
	 *  
	 * @param  array $atts 
	 * @return string       
	 */
	public static function trusted_shops_rich_snippets( $atts ) {
		
		ob_start();
		woocommerce_get_template( 'trusted-shops/rich-snippets.php' );
		$html = ob_get_clean();
		return WC_trusted_shops()->trusted_shops->is_enabled() ? '<div class="woocommerce woocommerce-gzd">' . $html . '</div>' : '';
	
	}

	/**
	 * Returns Trusted Shops reviews graphic
	 *  
	 * @param  array $atts 
	 * @return string       
	 */
	public static function trusted_shops_reviews( $atts ) {
		
		ob_start();
		woocommerce_get_template( 'trusted-shops/reviews.php' );
		$html = ob_get_clean();
		return WC_trusted_shops()->trusted_shops->is_enabled() ? '<div class="woocommerce woocommerce-gzd">' . $html . '</div>' : '';
	
	}

	/**
	 * Returns Trusted Shops Badge html
	 *  
	 * @param  array $atts 
	 * @return string       
	 */
	public static function trusted_shops_badge( $atts ) {

		extract( shortcode_atts( array('width' => ''), $atts ) );
		return WC_trusted_shops()->trusted_shops->is_enabled() ? '<a class="trusted-shops-badge" style="' . ( $width ? 'background-size:' . ( $width - 1 ) . 'px auto; width: ' . $width . 'px; height: ' . $width . 'px;' : '' ) . '" href="' . WC_germanized()->trusted_shops->get_certificate_link() . '" target="_blank"></a>' : '';
	
	}

}