<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_TS_Install' ) ) :

/**
 * Installation related functions and hooks
 *
 * @class 		WC_GZD_Install
 * @version		1.0.0
 * @author 		Vendidero
 */
class WC_TS_Install {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		register_activation_hook( WC_TRUSTED_SHOPS_PLUGIN_FILE, array( $this, 'install' ) );
		add_action( 'admin_init', array( $this, 'check_version' ), 5 );
	}

	/**
	 * check_version function.
	 *
	 * @access public
	 * @return void
	 */
	public function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && ( get_option( 'woocommerce_trusted_shops_version' ) != WC_trusted_shops()->version || get_option( 'woocommerce_trusted_shops_db_version' ) != WC_trusted_shops()->version ) ) {
			$this->install();
			do_action( 'woocommerce_trusted_shops_updated' );
		}
	}

	/**
	 * Install WC_Germanized
	 */
	public function install() {
		// Load Translation for default options
		$locale = apply_filters( 'plugin_locale', get_locale() );
		$mofile = WC_trusted_shops()->plugin_path() . '/i18n/languages/woocommerce-trusted-shops.mo';
		if ( file_exists( WC_trusted_shops()->plugin_path() . '/i18n/languages/woocommerce-trusted-shops-' . $locale . '.mo' ) )
			$mofile = WC_trusted_shops()->plugin_path() . '/i18n/languages/woocommerce-trusted-shops-' . $locale . '.mo';
		load_textdomain( 'woocommerce-trusted-shops', $mofile );
		if ( ! WC_trusted_shops()->is_woocommerce_activated() ) {
			deactivate_plugins( WC_GERMANIZED_PLUGIN_FILE );
			wp_die( sprintf( __( 'Please install <a href="%s" target="_blank">WooCommerce</a> before installing WooCommerce Germanized. Thank you!', 'woocommerce-germanized' ), 'http://wordpress.org/plugins/woocommerce/' ) );
		}
		$this->create_options();
		$this->create_cron_jobs();

		// Queue upgrades
		$current_version    = get_option( 'woocommerce_trusted_shops_version', null );
		$current_db_version = get_option( 'woocommerce_trusted_shops_db_version', null );

		update_option( 'woocommerce_trusted_shops_db_version', WC_trusted_shops()->version );

		// Update version
		update_option( 'woocommerce_trusted_shops_version', WC_trusted_shops()->version );

		// Flush rules after install
		flush_rewrite_rules();

	}

	/**
	 * Handle updates
	 */
	public function update() {
		// Do updates
		$current_db_version = get_option( 'woocommerce_trusted_shops_db_version' );
		update_option( 'woocommerce_trusted_shops_db_version', WC_trusted_shops()->version );
	}

	/**
	 * Create cron jobs (clear them first)
	 */
	private function create_cron_jobs() {
		// Cron jobs
		wp_clear_scheduled_hook( 'woocommerce_trusted_shops' );
		wp_schedule_event( time(), 'twicedaily', 'woocommerce_trusted_shops' );
	}

	/**
	 * Default options
	 *
	 * Sets up the default options used on the settings page
	 *
	 * @access public
	 */
	function create_options() {
		// Include settings so that we can run through defaults
		include_once( WC()->plugin_path() . '/includes/admin/settings/class-wc-settings-page.php' );
		include_once( 'admin/settings/class-wc-ts-settings.php' );

		$settings = new WC_TS_Settings();
		$options = $settings->get_settings();

		foreach ( $options as $value ) {
			if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
				$autoload = isset( $value['autoload'] ) ? (bool) $value['autoload'] : true;
				add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
			}
		}
	}

}

endif;

return new WC_TS_Install();
