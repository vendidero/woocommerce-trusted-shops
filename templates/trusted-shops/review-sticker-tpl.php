<?php
/**
 * Admin View: Product Sticker Template
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}
?>

<script type="text/javascript">
  _tsRatingConfig = {
    tsid: '{id}',
    element: '{element}',
    variant: 'testimonial',
    reviews: '{number}',
    betterThan: '{better_than}',
    richSnippets: 'on',
    backgroundColor: '{bg_color}',
    linkColor: '#000000',
    fontFamily: '{font}',
    reviewMinLength: '10',
    quotationMarkColor: '#FFFFFF'
  };
</script>
<?php
/*
 * Load Script through WordPress Core function
 * load in footer for increase Core Web Vitals
 */
wp_enqueue_script( 'script', '//widgets.trustedshops.com/reviews/tsSticker/tsSticker.js', [], 1.0, true);
?>
