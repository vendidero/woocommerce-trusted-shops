<?php
/**
 * Trusted Shops Review Sticker
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
<div <?php echo $plugin->get_selector( 'review_sticker', $element ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>></div>

<script type="text/javascript">
	<?php echo $plugin->get_review_sticker_code( true, array( 'element' => $element ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</script>
