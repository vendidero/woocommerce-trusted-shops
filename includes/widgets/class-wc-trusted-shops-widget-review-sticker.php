<?php
/**
 * Trusted Shops Reviews Widget
 *
 * Displays Trusted Shops reviews as graphic
 *
 * @author      Vendidero
 * @category    Widgets
 * @version     1.0
 * @extends     WC_Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Trusted_Shops_Widget_Review_Sticker extends WC_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'woocommerce woocommerce_gzd widget_trusted_shops_review_sticker';
		$this->widget_description = _x( 'Show your TS shop review sticker.', 'trusted-shops', 'woocommerce-trusted-shops' );
		$this->widget_id          = 'woocommerce_gzd_widget_trusted_shops_shop_review_sticker';
		$this->widget_name        = _x( 'Trusted Shops Shop Review Sticker', 'trusted-shops', 'woocommerce-trusted-shops' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => _x( 'Trusted Shops Reviews', 'trusted-shops', 'woocommerce-trusted-shops' ),
				'label' => _x( 'Title', 'trusted-shops', 'woocommerce-trusted-shops' ),
			),
		);
		parent::__construct();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

		$element = "#ts_review_sticker_{$this->number}";

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? _x( 'Trusted Shops Reviews', 'trusted-shops', 'woocommerce-trusted-shops' ) : $instance['title'], $instance, $this->id_base );

		echo wp_kses_post( $before_widget );

		if ( $title ) {
			echo wp_kses_post( $before_title . $title . $after_title );
		}

		echo '<div class="widget_trusted_shops_review_sticker_content">';

		echo do_shortcode( '[trusted_shops_review_sticker element="' . $element . '"]' );

		echo '</div>';

		echo wp_kses_post( $after_widget );
	}
}


