<?php

add_action('wp_enqueue_scripts', 'sambaaiprefix_sambaAiAnalytics_orderTracking');

function sambaaiprefix_sambaAiAnalytics_orderTracking() {


  if (is_wc_endpoint_url('order-received')) {

    global $wp;
    $order_id = $wp->query_vars['order-received'];
    $order = new WC_Order($order_id);

    $orderItems = $order->get_items();
?>


    <!-- Start Samba.ai order -->
    <script>
      var _yottlyOnload = _yottlyOnload || [];
      _yottlyOnload.push(function() {

        var products = [];
        <?php foreach ($orderItems as $item) { ?>
          products.push({
            productId: '<?php echo esc_js($item->get_product_id()) ?>',
            price: <?php echo esc_js(intval($order->get_line_total($item, true, true))) ?>
          });
        <?php } ?>

        //console.log(products)

        diffAnalytics.order({
          content: products
        });

      });
    </script>
    <!-- End Samba.ai order -->
<?php
  }
}
