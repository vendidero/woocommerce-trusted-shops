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

    /** @var array DB updates that need to be run */
    private static $db_updates = array(
        '2.3.0' => 'updates/woocommerce-ts-update-2.3.0.php'
    );

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		register_activation_hook( WC_TRUSTED_SHOPS_PLUGIN_FILE, array( $this, 'install' ) );

		add_action( 'admin_init', array( $this, 'check_version' ), 10 );
        add_action( 'admin_init', array( __CLASS__, 'install_actions' ), 5 );
	}

    /**
     * Install actions such as installing pages when a button is clicked.
     */
    public static function install_actions() {
        if ( ! empty( $_GET['do_update_woocommerce_ts'] ) ) {
            self::update();

            // Update complete
            delete_option( '_wc_ts_needs_update' );
        }
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
        $locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-trusted-shops' );
		$mofile = WC_trusted_shops()->plugin_path() . '/i18n/languages/woocommerce-trusted-shops.mo';
		
		if ( file_exists( WC_trusted_shops()->plugin_path() . '/i18n/languages/woocommerce-trusted-shops-' . $locale . '.mo' ) )
			$mofile = WC_trusted_shops()->plugin_path() . '/i18n/languages/woocommerce-trusted-shops-' . $locale . '.mo';
		
		load_textdomain( 'woocommerce-trusted-shops', $mofile );
		
		if ( ! WC_TS_Dependencies::instance()->is_woocommerce_activated() ) {
			deactivate_plugins( WC_TRUSTED_SHOPS_PLUGIN_FILE );
			wp_die( sprintf( __( 'Please install <a href="%s" target="_blank">WooCommerce</a> before installing WooCommerce Germanized. Thank you!', 'woocommerce-germanized' ), 'http://wordpress.org/plugins/woocommerce/' ) );
		}
		
		$this->create_options();
		$this->create_cron_jobs();

        // Queue upgrades
        $current_version    = get_option( 'woocommerce_trusted_shops_version', null );
        $current_db_version = get_option( 'woocommerce_trusted_shops_db_version', null );

        if ( ! is_null( $current_db_version ) && version_compare( $current_db_version, max( array_keys( self::$db_updates ) ), '<' ) ) {
            // Update
            update_option( '_wc_ts_needs_update', 1 );
        } else {
            self::update_db_version();
        }

        self::update_wc_gzd_version();

		do_action( 'woocommerce_trusted_shops_installed' );

		// Flush rules after install
		flush_rewrite_rules();
	}

    /**
     * Update WC version to current
     */
    private static function update_wc_gzd_version() {
        delete_option( 'woocommerce_trusted_shops_version' );
        add_option( 'woocommerce_trusted_shops_version', WC_trusted_shops()->version );
    }

    /**
     * Update DB version to current
     */
    private static function update_db_version( $version = null ) {
        delete_option( 'woocommerce_trusted_shops_db_version' );
        add_option( 'woocommerce_trusted_shops_db_version', is_null( $version ) ? WC_trusted_shops()->version : $version );
    }

	/**
	 * Handle updates
	 */
	public function update() {
        $current_db_version = get_option( 'woocommerce_trusted_shops_db_version' );

        foreach ( self::$db_updates as $version => $updater ) {
            if ( version_compare( $current_db_version, $version, '<' ) ) {
                include( $updater );
                self::update_db_version( $version );
            }
        }

        self::update_db_version();
	}

	/**
	 * Create cron jobs (clear them first)
	 */
	private function create_cron_jobs() {
		// Cron jobs
		wp_clear_scheduled_hook( 'woocommerce_gzd_trusted_shops_reviews' );
		wp_schedule_event( time(), 'twicedaily', 'woocommerce_gzd_trusted_shops_reviews' );
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
		$options = apply_filters( 'woocommerce_gzd_installation_default_settings', array() );

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
