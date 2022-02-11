<?php

function generateXML_orders($ajaxMode = true) {

  if (!is_plugin_active('woocommerce/woocommerce.php')) {
    return;
  }

  global $wpdb;

  $perFile = 100;
  $loop = 0;
  $totalOrders = $wpdb->get_var("SELECT COUNT(*) FROM `" . $wpdb->prefix . "posts` WHERE post_type = 'shop_order'");

  // Open the XML file
  file_put_contents(WP_CONTENT_DIR . '/sambaHelperExport/sambaHelperOrders.xml', '<?xml version="1.0" encoding="utf-8"?><ORDERS>');

  // Loop order file by file
  while ($loop * $perFile < $totalOrders) {
    $xmlExport = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><ORDERS></ORDERS>');

    $orders = wc_get_orders([
      'limit' => $perFile,
      'offset' => $loop * $perFile
    ]);

    // Loop each individual order
    foreach ($orders as $order) {

      $orderItems = $order->get_items();
      $filteredOrderItems = [];

      // Loop order items
      if (count($orderItems)) {

        foreach ($orderItems as $orderItem) {

          $orderItemID = $orderItem->get_product_id();

          // Skip invalid products
          if ($orderItemID < 1) {
            continue;
          }

          $orderItemProduct = wc_get_product($orderItemID);

          // Skip non-supported types
          // TODO add support
          if ($orderItemProduct->is_type(['variable', 'grouped', 'external'])) {
            continue;
          }

          $orderItemVariationID = $orderItem->get_variation_id();
          $orderItemQuantity          = $orderItem->get_quantity();

          array_push($filteredOrderItems, [
            'PRODUCT_ID' => $orderItemVariationID > 0 ? $orderItemVariationID : $orderItemID,
            'AMOUNT'     => $orderItemQuantity,
            'PRICE'      => $order->get_item_total($orderItem, true, true) * $orderItemQuantity,
          ]);
        }
      }

      $orderCustomerID  = (int) $order->get_user_id();
      $orderStatus       = $order->get_status();
      $orderDateCreated = $order->get_date_created();

      if (is_a($order, 'WC_Order_Refund')) {

        // WC_Order_Refund class is missing some WC_Order functions
        $orderDateCompleted = get_post_meta($order->get_id(), '_date_completed', true);

        if (!$orderDateCompleted) {
          $orderDateCompleted = get_post_meta($order->get_id(), '_completed_date', true);
        }

        if ($orderDateCompleted) {
          $orderDateCompleted = wc_string_to_datetime($orderDateCompleted);
        }

        $orderBillingEmail     = get_post_meta($order->get_id(), '_billing_email', true);
        $orderBillingPhone     = get_post_meta($order->get_id(), '_billing_phone', true);
        $orderShippingPostcode = str_replace(' ', '', get_post_meta($order->get_id(), '_shipping_postcode', true));

        $orderShippingCountry  = get_post_meta($order->get_id(), '_shipping_country', true);
      } else {

        $orderDateCompleted    = $order->get_date_completed();
        $orderBillingEmail     = $order->get_billing_email();
        $orderBillingPhone     = $order->get_billing_phone();
        $orderShippingPostcode = str_replace(' ', '', $order->get_shipping_postcode());
        $orderShippingCountry  = $order->get_shipping_country();
      }

      $xmlOrder = $xmlExport->addChild('ORDER');
      $xmlOrder->addChild('ORDER_ID', $order->get_id());

      if ($orderCustomerID > 0) {
        $xmlOrder->addChild('CUSTOMER_ID', $orderCustomerID);
      }

      $xmlOrder->addChild('CREATED_ON', $orderDateCreated->format(DATE_RFC3339_EXTENDED));

      if (in_array($orderStatus, apply_filters('hell_samba_orders_feed_completed_states', ['completed']))) {
        $xmlOrder->addChild('STATUS', 'finished');

        if ($orderDateCompleted) {
          $xmlOrder->addChild('FINISHED_ON', $orderDateCompleted->format(DATE_RFC3339_EXTENDED));
        }
      } else if ($orderStatus == 'cancelled') {
        $xmlOrder->addChild('STATUS', 'canceled');
      } else {
        $xmlOrder->addChild('STATUS', 'created');
      }

      $xmlOrder->addChild('EMAIL', $orderBillingEmail);

      if ($orderBillingPhone) {

        $xmlOrder->addChild('PHONE', $orderBillingPhone);
      }

      if ($orderShippingPostcode) {

        $xmlOrder->addChild('ZIP_CODE', $orderShippingPostcode);
      }

      if ($orderShippingCountry) {

        $xmlOrder->addChild('COUNTRY_CODE', $orderShippingCountry);
      }

      $xmlOrderItems = $xmlOrder->addChild('ITEMS');

      foreach ($filteredOrderItems as $filteredOrderItem) {

        $xmlOrderItem = $xmlOrderItems->addChild('ITEM');

        foreach ($filteredOrderItem as $itemAttributeKey => $itemAttribute) {
          $xmlOrderItem->addChild($itemAttributeKey, $itemAttribute);
        }
      }
    }

    $xmlTemp = $xmlExport->asXML();
    $xmlTemp = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $xmlTemp);
    $xmlTemp = str_replace('<ORDERS>', '', $xmlTemp);
    $xmlTemp = str_replace('</ORDERS>', '', $xmlTemp);

    file_put_contents(WP_CONTENT_DIR . '/sambaHelperExport/sambaHelperOrders.xml', $xmlTemp, FILE_APPEND);

    $loop += 1;
  }

  // Close the XML file
  file_put_contents(WP_CONTENT_DIR . '/sambaHelperExport/sambaHelperOrders.xml', '</ORDERS>', FILE_APPEND);

  if ($ajaxMode) {
    die();
  }
}
