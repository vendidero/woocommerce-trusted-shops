<?php

namespace Vendidero\TrustedShops;

use Exception;
use WC_TS_Install;

defined( 'ABSPATH' ) || exit;

/**
 * Main package class.
 */
class Package {

	/**
	 * Version.
	 *
	 * @var string
	 */
	const VERSION = '4.0.17';

	/**
	 * Init the package - load the REST API Server class.
	 */
	public static function init() {
		if ( ! self::has_dependencies() ) {
			if ( ! self::is_integration() ) {
				add_action( 'admin_notices', array( __CLASS__, 'dependency_notice' ), 20 );
			}

			return;
		}

		add_action( 'admin_notices', array( __CLASS__, 'outdated_notice' ), 20 );
        add_action( 'admin_init', array( __CLASS__, 'check_migration' ) );

		self::init_hooks();
		self::includes();

		PluginsHelper::init();
	}

	protected static function get_locale_mapping() {
		$supported = array(
			'de' => 'de_DE',
			'en' => 'en_GB',
			'fr' => 'fr_FR',
		);

		return $supported;
	}

	public static function get_language() {
		$locale = self::get_locale();

		return substr( $locale, 0, 2 );
	}

	public static function get_locale() {
		$supported = self::get_locale_mapping();

		$locale = 'en_GB';
		$base   = substr( function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale(), 0, 2 );

		if ( isset( $supported[ $base ] ) ) {
			$locale = $supported[ $base ];
		}

		return $locale;
	}

    public static function get_migration_doc_url() {
        return 'de' === self::get_language() ? 'https://help.etrusted.com/hc/de/articles/360046269991-WooCommerce-Plugin-installieren' : 'https://help.etrusted.com/hc/en-gb/articles/360046269991-Installing-the-WooCommerce-plugin';
    }

    public static function get_migration_url() {
        return current_user_can( 'activate_plugins' ) ? wp_nonce_url( add_query_arg( 'ts_migrate_new_gen', 'true', admin_url( 'admin.php?page=wc-settings' ) ), 'ts_migrate_new_gen' ) : admin_url( 'plugin-install.php?s=trusted+shops+easy+integration+for+woocommerce&tab=search&type=term' );
    }

    public static function check_migration() {
        if ( isset( $_GET['_wpnonce'], $_GET['ts_migrate_new_gen'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'ts_migrate_new_gen' ) ) {
            if ( current_user_can(  'activate_plugins' ) ) {
	            PluginsHelper::install_or_activate_trusted_shops();

	            if ( PluginsHelper::is_trusted_shops_plugin_active() ) {
		            wp_safe_redirect( esc_url_raw( Package::is_integration() ? admin_url( 'admin.php?page=wc-settings&tab=germanized-trusted_shops_easy_integration' ) : admin_url( 'admin.php?page=wc-settings&tab=trusted_shops_easy_integration' ) ) );
		            exit();
	            }
            }

	        wp_safe_redirect( esc_url_raw( admin_url( 'plugin-install.php?s=trusted+shops+easy+integration+for+woocommerce&tab=search&type=term' ) ) );
	        exit();
        }
    }

    public static function outdated_notice() {
	    if ( current_user_can( 'activate_plugins' ) ) {
		    include_once self::get_path() . '/includes/admin/views/html-notice-migrate.php';
	    }
    }

	public static function install_integration() {
		self::includes();
		include_once self::get_path() . '/includes/class-wc-ts-install.php';

		WC_TS_Install::install_integration();
	}

	public static function install() {
		if ( self::has_dependencies() ) {
			self::includes();
			include_once self::get_path() . '/includes/class-wc-ts-install.php';

			WC_TS_Install::install();
		}
	}

	public static function dependency_notice() {
		?>
		<div class="notice notice-error">
			<p><?php echo esc_html_x( 'Trustbadge Reviews for WooCommerce needs at least WooCommerce version 3.1 to run.', 'trusted-shops', 'woocommerce-trusted-shops' ); ?></p>
		</div>
		<?php
	}

	public static function has_dependencies() {
		return class_exists( 'WooCommerce' ) && version_compare( WC()->version, '3.1', '>=' ) ? true : false;
	}

	public static function is_integration() {
		return class_exists( 'WooCommerce_Germanized' ) ? true : false;
	}

	private static function includes() {
		include_once self::get_path() . '/includes/class-wc-trusted-shops-core.php';
	}

	public static function init_hooks() {}

	/**
	 * Return the version of the package.
	 *
	 * @return string
	 */
	public static function get_version() {
		return self::VERSION;
	}

	/**
	 * Return the path to the package.
	 *
	 * @return string
	 */
	public static function get_path() {
		return dirname( __DIR__ );
	}

	/**
	 * Return the path to the package.
	 *
	 * @return string
	 */
	public static function get_url() {
		return plugins_url( '', __DIR__ );
	}

	public static function get_assets_url() {
		return self::get_url() . '/assets';
	}

	private static function define_constant( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}
}
