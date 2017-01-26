<?php
/**
 * Plugin Name: Trustbadge Reviews for WooCommerce
 * Plugin URI: http://www.trustedshops.co.uk/
 * Description: Adds Seller and Product Reviews or Trusted Shops Integration to your WooCommerce Shop.
 * Version: 2.0.3
 * Author: Vendidero
 * Author URI: http://vendidero.de
 * Requires at least: 3.8
 * Tested up to: 4.5
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
	public $version = '2.0.3';

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

		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
			
		// Check if dependecies are installed
		$init = WC_TS_Dependencies::instance();
		
		if ( ! $init->is_loadable() )
			return;

		// Define constants
		$this->define_constants();

		// Include required files
		$this->includes();

		// Hooks
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
		add_action( 'init', array( $this, 'init' ), 1 );
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
		add_filter( 'woocommerce_locate_template', array( $this, 'filter_templates' ), 0, 3 );

		// Initialize Trusted Shops module
		$this->trusted_shops = new WC_Trusted_Shops( $this, array(
				'supports'	  	  => array(),
				'signup_params'	  => array( 'utm_source' => 'woocommerce-app', 'utm_campaign' => 'woocommerce-app' ),
				'urls'		  	  => array( 
					'integration' 		=> 'http://support.trustedshops.com/en/apps/woocommerce',
					'signup' 			=> 'http://www.trustbadge.com/en/pricing/',
					'trustbadge_custom' => 'http://www.trustedshops.co.uk/support/trustbadge/trustbadge-custom/', 
					'reviews' 			=> 'http://www.trustedshops.co.uk/support/product-reviews/', 
				),
			)
		);

		// Loaded action
		do_action( 'woocommerce_trusted_shops_loaded' );
	}

	/**
	 * Init Trusted Shops when WordPress initializes.
	 */
	public function init() {
		
		// Before init action
		do_action( 'before_woocommerce_trusted_shops_init' );
		
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_styles' ) );
		add_action( 'admin_init', array( $this, 'redirect_settings' ) );
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 15 );
		// Change email template path if is germanized email template
		add_filter( 'woocommerce_template_directory', array( $this, 'set_woocommerce_template_dir' ), 10, 2 );
		add_filter( 'woocommerce_locate_core_template', array( $this, 'set_woocommerce_core_template_dir' ), 10, 3 );

		// Init action
		do_action( 'woocommerce_trusted_shops_init' );
	}

	public function set_woocommerce_core_template_dir( $core_file, $template, $template_base ) {
		if ( ! file_exists( $template_base . $template ) && file_exists( $this->plugin_path() . '/templates/' . $template ) )
			$core_file = $this->plugin_path() . '/templates/' . $template;

		return $core_file;
	}

	public function set_woocommerce_template_dir( $dir, $template ) {
		if ( file_exists( WC_trusted_shops()->plugin_path() . '/templates/' . $template ) )
			return 'woocommerce-trusted-shops';
		return $dir;
	}

	public function admin_footer_text( $footer_text ) {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		// Check to make sure we're on a WooCommerce admin page
		if ( isset( $_GET[ 'tab' ] ) && 'trusted-shops' === $_GET[ 'tab' ] ) {
			$footer_text = sprintf( _x( 'If the <strong>App</strong> helped you, please leave a %s&#9733;&#9733;&#9733;&#9733;&#9733;%s in the Wordpress plugin repository.', 'trusted-shops', 'woocommerce-trusted-shops' ), '<a href="https://wordpress.org/support/view/plugin-reviews/woocommerce-trusted-shops?rate=5#postform" target="_blank" class="wc-rating-link">', '</a>' );
		}

		return $footer_text;
	}

	public function redirect_settings() {
		if ( ( isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] === 'trusted-shops' && ( ! isset( $_GET[ 'section' ] ) || empty( $_GET[ 'section' ] ) ) ) ) {
			// Redirect
			wp_safe_redirect( admin_url( 'admin.php?page=wc-settings&tab=trusted-shops&section=trusted_shops' ) );
		} 
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

		include_once ( 'includes/wc-trusted-shops-core-functions.php' );

		include_once 'includes/class-wc-ts-install.php';
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

		// Check Theme
		$theme_template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name
			)
		);

		// Make filter gzd_compatible
		$template_name = apply_filters( 'woocommerce_trusted_shops_template_name', $template_name );

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
	 * Show action links on the plugin screen
	 *
	 * @param mixed   $links
	 * @return array
	 */
	public function action_links( $links ) {
		return array_merge( array(
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=trusted-shops&section=trusted_shops' ) . '">' . _x( 'Settings', 'trusted-shops', 'woocommerce-trusted-shops' ) . '</a>',
		), $links );
	}

	/**
	 * Add custom styles to Admin
	 */
	public function add_admin_styles() {
		
		$screen = get_current_screen();
		
		if ( isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] == 'trusted-shops' ) {

			if ( isset( $_GET[ 'section' ] ) )
				$section = sanitize_text_field( $_GET[ 'section' ] );

			if ( $section === 'trusted_shops' )
				do_action( 'woocommerce_gzd_load_trusted_shops_script' );

		}

	}

	/**
	 * Add WooCommerce Germanized Settings Tab
	 *
	 * @param array   $integrations
	 * @return array
	 */
	public function add_settings( $integrations ) {
		$integrations[] = new WC_TS_Settings_Handler();
		return $integrations;
	}

	/**
	 * Add Custom Email templates
	 *
	 * @param array   $mails
	 * @return array
	 */
	public function add_emails( $mails ) {
		$mails[ 'WC_TS_Email_Customer_Trusted_Shops' ] = include 'includes/emails/class-wc-ts-email-customer-trusted-shops.php';
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
