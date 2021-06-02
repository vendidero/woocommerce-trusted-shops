<?php
/**
 * Admin View: Product Sticker Template
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}
?>

<script type="text/javascript">
  _tsProductReviewsConfig = {
    tsid: '{id}', 
    sku: ['{sku}'],
    variant: 'productreviews',
    borderColor: '{border_color}',
    locale: '{locale}',
    starColor: '{star_color}',
    starSize: '{star_size}px',
    ratingSummary: 'false',
    maxHeight: '1200px',
    element: '{element}',
    hideEmptySticker: 'false',
    richSnippets: 'off',
    introtext: ''  /* optional */
};
</script>
<?php
/*
 * Load Script through WordPress Core function
 * load in footer for increase Core Web Vitals
 */
wp_enqueue_script( 'script', '//widgets.trustedshops.com/reviews/tsSticker/tsProductSticker.js', [], 1.0, true);
?>
