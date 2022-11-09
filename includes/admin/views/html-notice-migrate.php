<?php
/**
 * Admin View: Notice - Update
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use Vendidero\TrustedShops\Package;
?>
<div id="message" class="error">
	<h3><?php echo esc_html_x( 'Attention! A new plugin is available', 'trusted-shops', 'woocommerce-trusted-shops' ); ?></h3>
	<p><?php echo wp_kses_post( sprintf( _x( 'The Trusted Shops plugin you are using is <a href="%s" target="_blank">outdated</a>. Please switch to the new plugin as soon as possible.', 'trusted-shops', 'woocommerce-trusted-shops' ), esc_url( Package::get_migration_doc_url() ) ) ); ?></p>

	<p>
		<a class="button button-primary" href="<?php echo esc_url( Package::get_migration_url() ); ?>"><?php echo esc_html_x( 'Switch to the new plugin now', 'trusted-shops', 'woocommerce-trusted-shops' ); ?></a>
	</p>
</div>
