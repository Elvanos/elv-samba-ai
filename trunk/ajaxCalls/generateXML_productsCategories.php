<?php

// TODO maybe later
function sambaaiprefix_generateXML_productCategories($ajaxMode = true) {

  if (!is_plugin_active('woocommerce/woocommerce.php')) {
    return;
  }

  if ($ajaxMode) {
    die();
  }
}
