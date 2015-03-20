<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_TS_Settings' ) ) :

/**
 * Adds Settings Interface to WooCommerce Settings Tabs
 *
 * @class 		WC_GZD_Settings_Germanized
 * @version		1.0.0
 * @author 		Vendidero
 */
class WC_TS_Settings extends WC_Settings_Page {

	/**
	 * Adds Hooks to output and save settings
	 */
	public function __construct() {
		$this->id    = 'trusted-shops';
		$this->label = __( 'Trusted Shops', 'woocommerce-trusted-shops' );

		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );

	}

	/**
	 * Gets setting sections
	 */
	public function get_sections() {
		$sections = array(
			''   		 	=> __( 'Trusted Shops Options', 'woocommerce-trusted-shops' ),
		);
		return $sections;
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {
		return apply_filters( 'woocommerce_trusted_shops_settings', WC_trusted_shops()->trusted_shops->get_settings() ); // End general settings
	}

	public function output() {
		global $current_section;
		$sidebar = WC_trusted_shops()->trusted_shops->get_sidebar();
		$settings = $this->get_settings();
		echo '<div class="wc-gzd-admin-settings">';
		WC_Admin_Settings::output_fields( $settings );
		do_action( 'woocommerce_trusted_shops_after_settings', $settings );
		echo '</div>';
		echo $sidebar;
	}

	/**
	 * Save settings
	 */
	public function save() {

		global $current_section;
		
		$settings = WC_trusted_shops()->trusted_shops->get_settings();

		do_action( 'woocommerce_trusted_shops_before_save', $settings );

		WC_Admin_Settings::save_fields( $settings );

		do_action( 'woocommerce_trusted_shops_after_save', $settings );

	}

}

endif;

?>