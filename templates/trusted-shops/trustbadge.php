<?php
/**
 * Trusted Shops Trustbadge
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
<script type="text/javascript">
	<?php echo $plugin->get_trustbadge_code(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</script>
