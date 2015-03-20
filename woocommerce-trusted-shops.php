<?php
/**
 * Plugin Name: WooCommerce Trusted Shops
 * Plugin URI: http://www.trustedshops.co.uk/
 * Description: Adds Trusted Shops Integration to your WooCommerce Shop.
 * Version: 1.1.0
 * Author: Vendidero
 * Author URI: http://vendidero.de
 * Requires at least: 3.8
 * Tested up to: 4.1
 *
 * Text Domain: woocommerce-trusted-shops
 * Domain Path: /i18n/languages/
 *
 * @author Vendidero
 */
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

if ( ! class_exists( 'WooCommerce_Trusted_Shops' ) ) :

final class WooCommerce_Trusted_Shops {

	/**
	 * Current WooCommerce Trusted Shops Version
	 *
	 * @var string
	 */
	public $version = '1.1.0';

	/**
	 * Single instance of WooCommerce Trusted Shops Main Class
	 *
	 * @var object
	 */
	protected static $_instance = null;

	public $emails = null;

	public $trusted_shops = null;

	/**
	 * Main WooCommerce_Trusted_Shops Instance
	 *
	 * Ensures that only one instance of WooCommerce_Trusted_Shops is loaded or can be loaded.
	 *
	 * @static
	 * @see WC_trusted_shops()
	 * @return WooCommerce_Trusted_Shops - Main instance
	 */
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

	/**
	 * Global getter
	 *
	 * @param string  $key
	 * @return mixed
	 */
	public function __get( $key ) {
		return self::$key;
	}

	/**
	 * adds some initialization hooks and inits WooCommerce Trusted Shops
	 */
	public function __construct() {

		// Auto-load classes on demand
		if ( function_exists( "__autoload" ) ) {
			spl_autoload_register( "__autoload" );
		}
		spl_autoload_register( array( $this, 'autoload' ) );

		// Define constants
		$this->define_constants();

		include_once 'includes/class-wc-ts-install.php';

		// Hooks
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
		add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );
		add_action( 'init', array( $this, 'init' ), 1 );
		add_action( 'init', array( 'WC_TS_Shortcodes', 'init' ), 2 );
		add_action( 'widgets_init', array( $this, 'include_widgets' ), 25 );
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );

		$this->trusted_shops  = new WC_TS();

		// Loaded action
		do_action( 'woocommerce_trusted_shops_loaded' );
	}

	public function deactivate() {
		if ( current_user_can( 'activate_plugins' ) )
			deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	/**
	 * Init Trusted Shops when WordPress initializes.
	 */
	public function init() {
		if ( $this->is_woocommerce_activated() ) {
			// Before init action
			do_action( 'before_woocommerce_trusted_shops_init' );
			// Include required files
			$this->includes();
			add_filter( 'woocommerce_locate_template', array( $this, 'filter_templates' ), 0, 3 );
			add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_settings' ) );
			add_filter( 'woocommerce_enqueue_styles', array( $this, 'add_styles' ) );
			//add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_styles' ) );
			//add_action( 'wp_print_scripts', array( $this, 'localize_scripts' ), 5 );
			//add_filter( 'woocommerce_email_classes', array( $this, 'add_emails' ) );

			// Init action
			do_action( 'woocommerce_trusted_shops_init' );
		} else {
			add_action( 'admin_init', array( $this, 'deactivate' ), 0 );
		}
	}

	/**
	 * Checks if WooCommerce is activated
	 *  
	 * @return boolean true if WooCommerce is activated
	 */
	public function is_woocommerce_activated() {
		if ( is_multisite() )
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		if ( is_multisite() && ! is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) )
			return false;
		else if ( ! is_multisite() && ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
			return false;
		return true;
	}

	/**
	 * Auto-load WC_Trusted_Shops classes on demand to reduce memory consumption.
	 *
	 * @param mixed   $class
	 * @return void
	 */
	public function autoload( $class ) {
		$path = $this->plugin_path() . '/includes/';
		$class = strtolower( $class );
		$file = 'class-' . str_replace( '_', '-', $class ) . '.php';

		if ( $path && is_readable( $path . $file ) ) {
			include_once $path . $file;
			return;
		}
	}

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Get the language path
	 *
	 * @return string
	 */
	public function language_path() {
		return $this->plugin_path() . '/i18n/languages';
	}

	/**
	 * Define WC_Germanized Constants
	 */
	private function define_constants() {
		define( 'WC_TRUSTED_SHOPS_PLUGIN_FILE', __FILE__ );
		define( 'WC_TRUSTED_SHOPS_VERSION', $this->version );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	private function includes() {

		if ( is_admin() ) {
			include_once 'includes/admin/class-wc-ts-admin.php';
		}

		if ( defined( 'DOING_AJAX' ) ) {
			// $this->ajax_includes();
		}

		if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {
			$this->frontend_includes();
		}

	}

	/**
	 * Include required frontend files.
	 */
	public function frontend_includes() {
		include_once 'includes/wc-ts-template-hooks.php';
	}

	/**
	 * Function used to Init WooCommerceGermanized Template Functions - This makes them pluggable by plugins and themes.
	 */
	public function include_template_functions() {
		if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {
			include_once 'includes/wc-ts-template-functions.php';
		}
	}

	/**
	 * Filter WooCommerce Templates to look into /templates before looking within theme folder
	 *
	 * @param string  $template
	 * @param string  $template_name
	 * @param string  $template_path
	 * @return string
	 */
	public function filter_templates( $template, $template_name, $template_path ) {

		if ( ! $template_path ) {
			$template_path = WC()->template_path();
		}

		if ( empty( $GLOBALS[ 'template_name' ] ) )
			$GLOBALS['template_name'] = array();
		$GLOBALS['template_name'][] = $template_name;

		// Check Theme
		$theme_template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name
			)
		);
		// Load Default
		if ( ! $theme_template ) {
			if ( file_exists( $this->plugin_path() . '/templates/' . $template_name ) )
				$template = $this->plugin_path() . '/templates/' . $template_name;
		} else
			$template = $theme_template;

		return apply_filters( 'woocommerce_trusted_shops_filter_template', $template, $template_name, $template_path );
	}

	/**
	 * Load Localisation files for WooCommerce Germanized.
	 */
	public function load_plugin_textdomain() {

		$domain = 'woocommerce-trusted-shops';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/i18n/languages/' );

	}

	/**
	 * Include WooCommerce Germanized Widgets
	 */
	public function include_widgets() {
		if ( is_object( $this->trusted_shops) && $this->trusted_shops->is_rich_snippets_enabled() )
			include_once 'includes/widgets/class-wc-ts-widget-rich-snippets.php';
		if ( is_object( $this->trusted_shops) && $this->trusted_shops->is_review_widget_enabled() )
			include_once 'includes/widgets/class-wc-ts-widget-reviews.php';
	}

	/**
	 * Show action links on the plugin screen
	 *
	 * @param mixed   $links
	 * @return array
	 */
	public function action_links( $links ) {
		return array_merge( array(
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=trusted-shops' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>',
		), $links );
	}

	/**
	 * Add custom styles to Admin
	 */
	public function add_admin_styles() {
		wp_register_style( 'woocommerce-trusted-shops-admin', WC_trusted_shops()->plugin_url() . '/assets/css/woocommerce-trusted-shops-admin.css', false, WC_trusted_shops()->version );
		wp_enqueue_style( 'woocommerce-trusted-shops-admin' );
	}

	/**
	 * Add styles to frontend
	 *
	 * @param array   $styles
	 */
	public function add_styles( $styles ) {
		$styles['woocommerce-trusted-shops-layout'] = array(
			'src'     => str_replace( array( 'http:', 'https:' ), '', WC_trusted_shops()->plugin_url() ) . '/assets/css/woocommerce-trusted-shops-layout.css',
			'deps'    => '',
			'version' => WC_TRUSTED_SHOPS_VERSION,
			'media'   => 'all'
		);
		return $styles;
	}

	/**
	 * Add WooCommerce Germanized Settings Tab
	 *
	 * @param array   $integrations
	 * @return array
	 */
	public function add_settings( $integrations ) {
		include_once 'includes/admin/settings/class-wc-ts-settings.php';
		$integrations[] = new WC_TS_Settings();
		return $integrations;
	}

	/**
	 * Add Custom Email templates
	 *
	 * @param array   $mails
	 * @return array
	 */
	public function add_emails( $mails ) {
		//$mails[] = include 'includes/emails/class-wc-gzd-email-customer-revocation.php';
		//$mails[] = include 'includes/emails/class-wc-gzd-email-customer-ekomi.php';
		return $mails;
	}

}

endif;

/**
 * Returns the global instance of WooCommerce Germanized
 */
function WC_trusted_shops() {
	return WooCommerce_Trusted_Shops::instance();
}

$GLOBALS['woocommerce_trusted_shops'] = WC_trusted_shops();

?>
