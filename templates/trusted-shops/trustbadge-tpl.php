<?php
/**
 * Admin View: Trustbadge Template
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}
?>

<script type="text/javascript">
(function () {
  var _tsid = '{id}';
  _tsConfig = {
    'yOffset': '{offset}', /* offset from page bottom */
    'variant': '{variant}', /* reviews, default, custom, custom_reviews */
    'customElementId': '', /* required for variants custom and custom_reviews */
    'trustcardDirection': '', /* for custom variants: topRight, topLeft, bottomRight, bottomLeft */ 'customBadgeWidth': '', /* for custom variants: 40 - 90 (in pixels) */
    'customBadgeHeight': '', /* for custom variants: 40 - 90 (in pixels) */
    'disableResponsive': 'false', /* deactivate responsive behaviour */
    'disableTrustbadge': '{disable}', /* deactivate trustbadge */
    'trustCardTrigger': 'mouseenter', /* set to 'click' if you want the trustcard to be opened on click instead */ 'customCheckoutElementId': ''/* required for custom trustcard */
  };
})();
</script>
<?php
/*
 * Load Script through WordPress Core function
 * load in footer for increase Core Web Vitals
 */
wp_enqueue_script( 'script', '//widgets.trustedshops.com/js/{id}.js', [], 1.0, true);
?>
