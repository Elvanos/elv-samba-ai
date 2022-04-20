<?php


function generateXML_products($ajaxMode = true) {

  if (!is_plugin_active('woocommerce/woocommerce.php')) {
    return;
  }

  function get_primary_category($post = 0) {
    $term_list = wp_get_post_terms($post, 'product_cat', array('fields' => 'ids'));
    $cat_id = (int)$term_list[0];
    return get_term($cat_id)->name;
  }

  $products = wc_get_products(
    [
      'limit'  => -1,
      'status' => ['publish'],
      // simple, variable, grouped, external
      'type'   => ['simple']
    ]
  );

  function fixEncoding($input) {
    $specialCharList = [
      ':' => '%3A',
      ';' => '%3B',
      '?' => '%3F',
      '@' => '%40',
      '&' => '%26',
      '=' => '%3D',
      '+' => '%2B',
      '$' => '%24',
      '#' => '%23',
      '%' => '%25',
      ',' => '%2C',
      '/' => '%2F',
      '\\' => '%5C',
      '"' => '%22',
      '!' => '%21',
      '^' => '%5E',
      '*' => '%2A',
      '(' => '%28',
      ')' => '%29',
      ' ' => '%20',
      '\'' => '%27',
      '[' => '%5B',
      ']' => '%5D',
      '{' => '%7B',
      '}' => '%7D',
      '|' => '%7C',
      '~' => '%7E',
      '`' => '%60',
      '<' => '%3C',
      '>' => '%3E',
      '.' => '%2E',
      ',' => '%2C',
      '_' => '%5F',
      '-' => '%2D'
    ];

    $input = rawurlencode($input);

    foreach ($specialCharList as $key => $value) {
      $input = str_replace($value, $key, $input);
    }

    return $input;
  }

  $xmlExport = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><PRODUCTS></PRODUCTS>');
  foreach ($products as $product) {

    // Basic data
    $xmlProduct = $xmlExport->addChild('PRODUCT');
    $xmlProduct->addChild('PRODUCT_ID', $product->get_id());
    $xmlProduct->addChild('TITLE', htmlspecialchars($product->get_name()));
    $xmlProduct->addChild('PRICE', ($product->is_on_sale()) ? $product->get_sale_price() : $product->get_regular_price());
    $xmlProduct->addChild('PRICE_BEFORE_DISCOUNT', $product->get_regular_price());
    $xmlProduct->addChild('URL', fixEncoding($product->get_permalink()));
    $xmlProduct->addChild('IMAGE', fixEncoding(wp_get_attachment_image_url($product->get_image_id(), 'thumbnail')));
    //$xmlProduct->addChild('BRAND', get_bloginfo('blogtitle'));

    $catText = get_primary_category($product->get_id());
    $xmlProduct->addChild('CATEGORYTEXT', $catText);

    // Poduct description - Content will be without html tags and limited to 100 characters
    $productDescription = str_replace("\n", ' ', strip_tags($product->get_description()));
    if (function_exists('mb_strlen') && (mb_strlen($productDescription) > 100)) {
      $shortenedDescription = mb_substr($productDescription, 0, 100);
      $productDescription    = mb_substr($shortenedDescription, 0, mb_strrpos($shortenedDescription, ' ')) . '...';
    } else if (strlen($productDescription) > 100) {
      $shortenedDescription = substr($productDescription, 0, 100);
      $productDescription    = substr($shortenedDescription, 0, strrpos($shortenedDescription, ' ')) . '...';
    }
    $xmlProduct->addChild('DESCRIPTION', htmlspecialchars($productDescription));

    // Determine stock amount
    $productStockAmount = ($product->get_stock_status() == 'outofstock') ? 0 : $product->get_stock_quantity();
    $xmlProduct->addChild('STOCK', $productStockAmount === '' || is_null($productStockAmount) ? 999999 : (int) $productStockAmount);
  }

  $xmlExport->asXML(WP_CONTENT_DIR . '/sambaAiExport/sambaAiProducts.xml');
  if ($ajaxMode) {
    die();
  }
}
