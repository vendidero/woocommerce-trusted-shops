<?php

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class WC_TS_Admin {

	/**
	 * Single instance of WooCommerce Germanized Main Class
	 *
	 * @var object
	 */
	protected static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce-trusted-shops' ), '1.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce-trusted-shops' ), '1.0' );
	}
	
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'add_scripts' ) );
	}

	public function add_scripts() {
		$assets_path = WC_trusted_shops()->plugin_url() . '/assets/js/admin/';
		if ( isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] == 'trusted-shops' )
			wp_enqueue_script( 'wc-ts-admin', $assets_path . 'settings.js', array( 'jquery', 'woocommerce_settings' ), WC_TRUSTED_SHOPS_VERSION, true );
	}

}

WC_TS_Admin::instance();