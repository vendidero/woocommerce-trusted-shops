<?php
/**
 * Trusted Shops Rich Snippets Widget
 *
 * Displays Trusted Shops reviews as rich snippets
 *
 * @author 		Vendidero
 * @category 	Widgets
 * @version 	1.0
 * @extends 	WC_Widget
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_TS_Widget_Rich_Snippets extends WC_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'woocommerce woocommerce_ts widget_trusted_shops_rich_snippets';
		$this->widget_description = __( "Display your Trusted Shops Reviews as Rich Snippets.", 'woocommerce-trusted-shops' );
		$this->widget_id          = 'woocommerce_ts_widget_rich_snippets';
		$this->widget_name        = __( 'Trusted Shops Rich Snippets', 'woocommerce-trusted-shops' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Trusted Shops Rich Snippets', 'woocommerce-trusted-shops' ),
				'label' => __( 'Title', 'woocommerce' )
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

		extract( $args );

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Trusted Shops Reviews', 'woocommerce-trusted-shops' ) : $instance['title'], $instance, $this->id_base );

		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;

		echo '<div class="widget_trusted_shops_reviews_content">';

		echo do_shortcode( '[trusted_shops_rich_snippets]' );

		echo '</div>';

		echo $after_widget;
	}
}

register_widget( 'WC_TS_Widget_Rich_Snippets' );

?>