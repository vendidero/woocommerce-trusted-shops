<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_TS_Settings_Handler' ) ) :

/**
 * Adds Settings Interface to WooCommerce Settings Tabs
 *
 * @class 		WC_GZD_Settings_Germanized
 * @version		1.0.0
 * @author 		Vendidero
 */
class WC_TS_Settings_Handler extends WC_Settings_Page {

	/**
	 * Adds Hooks to output and save settings
	 */
	public function __construct() {
		$this->id    = 'trusted-shops';
		$this->label = _x( 'Trusted Shops', 'trusted-shops', 'woocommerce-trusted-shops' );

		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 * Gets setting sections
	 */
	public function get_sections() {
		return apply_filters( 'woocommerce_gzd_settings_sections', array() );
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {
		return array();
	}

	public function output() {
		global $current_section;
		$settings = $this->get_settings();
		$sidebar = $this->get_sidebar();
		
		if ( $this->get_sections() ) {
			foreach ( $this->get_sections() as $section => $name ) {
				if ( $section == $current_section ) {
					$settings = apply_filters( 'woocommerce_gzd_get_settings_' . $section, $this->get_settings() );
					$sidebar = apply_filters( 'woocommerce_gzd_get_sidebar_' . $section, $sidebar );
				}
			}
		}

		do_action( 'woocommerce_gzd_before_section_output', $current_section );

		include_once( WC_trusted_shops()->plugin_path() . '/includes/admin/views/html-settings-section.php' );
	}

	public function get_sidebar() {
		ob_start();
		include_once( WC_trusted_shops()->plugin_path() . '/includes/admin/views/html-settings-sidebar.php' );
		$content = ob_get_clean();
		return $content;
	}

	/**
	 * Save settings
	 */
	public function save() {

		global $current_section;

		$settings = array();

		if ( $this->get_sections() ) {
			foreach ( $this->get_sections() as $section => $name ) {
				if ( $section == $current_section ) {
					$settings = apply_filters( 'woocommerce_gzd_get_settings_' . $section, $this->get_settings() );
				}
			}
		}
		if ( empty( $settings ) ) {
            return;
        }

		do_action( 'woocommerce_gzd_before_save_section_' . $current_section, $settings );

		WC_Admin_Settings::save_fields( $settings );

		do_action( 'woocommerce_gzd_after_save_section_' . $current_section, $settings );
	}

}

endif;

?>