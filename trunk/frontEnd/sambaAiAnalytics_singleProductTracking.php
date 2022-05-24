<?php

add_action('wp_enqueue_scripts', 'sambaaiprefix_sambaAiAnalytics_singleProductTracking');

function sambaaiprefix_sambaAiAnalytics_singleProductTracking() {


  if (is_product()) {

    $productID = get_the_ID();
?>

    <!-- Start Samba.ai product -->
    <script>
      var _yottlyOnload = _yottlyOnload || [];
      _yottlyOnload.push(function() {

        diffAnalytics.productId("<?php echo esc_js($productID) ?>")

      });
    </script>
    <!-- End Samba.ai product -->
<?php
  }
}
