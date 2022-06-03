<?php
/**
 * Trusted Shops Rich Snippets HTML
 *
 * @author      Vendidero
 * @package     WooCommerceGermanized/Templates
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<!-- Module: WooCommerce Trusted Shops -->
<script type="application/ld+json">
	<?php echo $plugin->get_rich_snippets_code( true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</script>
