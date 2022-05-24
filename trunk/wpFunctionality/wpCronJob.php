<?php
function sambaaiprefix_sambaAiCronJob() {

  // Register a trigger
  add_action('sambaAi_cron_hook', 'sambaaiprefix_sambaAi_cron_exec');
  if (!wp_next_scheduled('sambaAi_cron_hook')) {

    $localTimeToRun = 'midnight';
    $timestamp = strtotime($localTimeToRun) - (get_option('gmt_offset') * HOUR_IN_SECONDS);
    wp_schedule_event(
      $timestamp,
      'daily',
      'sambaAi_cron_hook'
    );

    //wp_schedule_event(time(), 'every_minute', 'sambaAi_cron_hook');
  }

  // Run the XLS generator using Cron
  function sambaaiprefix_sambaAi_cron_exec() {

    // If the Customer XML generator isnt loaded
    if (!function_exists('sambaaiprefix_generateXML_customers')) {
      require_once plugin_dir_path(__FILE__) . '../ajaxCalls/generateXML_customers.php';
      sambaaiprefix_generateXML_customers(false);
    }
    // If the Customer XML generator is loaded
    else {
      sambaaiprefix_generateXML_customers(false);
    }

    // If the Orders XML generator isnt loaded
    if (!function_exists('sambaaiprefix_generateXML_orders')) {
      require_once plugin_dir_path(__FILE__) . '../ajaxCalls/generateXML_orders.php';
      sambaaiprefix_generateXML_orders(false);
    }
    // If the Orders XML generator is loaded
    else {
      sambaaiprefix_generateXML_orders(false);
    }

    // If the Products XML generator isnt loaded
    if (!function_exists('sambaaiprefix_generateXML_products')) {
      require_once plugin_dir_path(__FILE__) . '../ajaxCalls/generateXML_products.php';
      sambaaiprefix_generateXML_products(false);
    }
    // If the Products XML generator is loaded
    else {
      sambaaiprefix_generateXML_products(false);
    }

    // If the Product categories XML generator isnt loaded
    if (!function_exists('sambaaiprefix_generateXML_productCategories')) {
      require_once plugin_dir_path(__FILE__) . '../ajaxCalls/generateXML_productCategories.php';
      sambaaiprefix_generateXML_productCategories(false);
    }
    // If the Product categories XML generator is loaded
    else {
      sambaaiprefix_generateXML_productCategories(false);
    }
  }
}
sambaaiprefix_sambaAiCronJob();
