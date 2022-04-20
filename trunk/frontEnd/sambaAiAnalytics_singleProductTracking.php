<?php

add_action('wp_head', 'sambaAiAnalytics_singleProductTracking');

function sambaAiAnalytics_singleProductTracking() {


  if (is_product()) {

    $productID = get_the_ID();
?>

    <!-- Start Samba.ai product -->
    <script>
      var _yottlyOnload = _yottlyOnload || [];
      _yottlyOnload.push(function() {

        diffAnalytics.productId("<?= $productID ?>")

      });
    </script>
    <!-- End Samba.ai product -->
<?php
  }
}
