<?php

function retrieveWCCartContent($ajaxMode = true) {

  if (!is_plugin_active('woocommerce/woocommerce.php')) {
    return;
  }

  global $woocommerce;
  $orderItems = $woocommerce->cart->get_cart();

  $products = [];

  foreach ($orderItems as $item) {
    $itemID = $item['product_id'];
    array_push($products, [
      'productId' => "$itemID",
      'amount' => $item['quantity']
    ]);
  }

  echo json_encode($products);

  if ($ajaxMode) {
    die();
  }
}
