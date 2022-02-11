<?php


function generateXML_customers($ajaxMode = true) {

  if (!is_plugin_active('woocommerce/woocommerce.php')) {
    return;
  }

  global $wpdb;

  $sambaNewsletterFrequency = [
    'everyDay' => 'every day',
    'special' => 'special occasions',
    'never' => 'never'
  ];

  $userCount = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM " . $wpdb->users . " WHERE 1=%d;", 1));
  if ($userCount <= 0) {
    die('Error: no users found');
  }

  $tempUserList = [];

  $lastUsersPage = 0;
  $totalUsersChecked = 0;
  $defaultMetaData = [
    'meta' => [
      'first_name'                   => '',
      'last_name'                    => '',
      'billing_first_name'           => '',
      'billing_last_name'            => '',
      'billing_email'                => '',
      'billing_phone'                => '',
      'billing_country'              => '',
      'billing_postcode'             => '',
    ]
  ];

  $trialMode = getTrialMode(false);

  if ($trialMode == 'checked') {
    $userCount = 200;
  }

  while ($lastUsersPage * 200 < $userCount) {
    $offset = $lastUsersPage * 200;
    $wordpressUsers = $wpdb->get_results($wpdb->prepare("SELECT ID, user_nicename, user_email, user_registered, display_name FROM " . $wpdb->prefix . "users WHERE 1=%d LIMIT %d OFFSET %d", 1, 200, $offset), ARRAY_A);
    if (count($wordpressUsers) <= 0) {
      break;
    }
    $wpUsersIds = [];
    foreach ($wordpressUsers as $wpUser) {
      array_push($wpUsersIds, $wpUser['ID']);
      $tempUserList[$wpUser['ID']] = array_merge($wpUser, $defaultMetaData);
    }

    // get user meta
    $wordpressUsersMeta = $wpdb->get_results($wpdb->prepare("SELECT user_id, meta_key, meta_value FROM " . $wpdb->prefix . "usermeta WHERE meta_key IN ( 'first_name', 'last_name', 'wc-user-newsletter', 'billing_first_name', 'billing_last_name', 'billing_email', 'billing_phone', 'billing_country', 'billing_postcode', 'hell-salesmanago-is-employee' ) AND user_id IN (" . implode(',', $wpUsersIds) . ") AND 1=%d", 1), ARRAY_A);
    foreach ($wordpressUsersMeta as $wpUserMeta) {
      $tempUserList[$wpUserMeta['user_id']]['meta'][$wpUserMeta['meta_key']] = $wpUserMeta['meta_value'];
    }

    $totalUsersChecked += count($wordpressUsers);
    $lastUsersPage += 1;
  }
  unset($wpUsersIds, $wordpressUsers, $wordpressUsersMeta);

  $xmlContent    = '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
  $xmlContent    .= '<CUSTOMERS>' . PHP_EOL;

  foreach ($tempUserList as $tempUser) {
    if ((!isset($tempUser['ID']) || $tempUser['ID'] == '') && (!isset($tempUser['meta']['billing_email']) || $tempUser['meta']['billing_email'] == '') && (!isset($tempUser['user_email']) || $tempUser['user_email'] == '')) {
      echo '<pre>' . print_r($tempUser, true) . '</pre>';
      continue;
    }

    if (isset($tempUser['ID']) && $tempUser['ID'] != '') {
      // Registred user
      $xmlCustomerId = $tempUser['ID'];
      $xmlCustomerEmail = $tempUser['meta']['billing_email'] !== '' ? $tempUser['meta']['billing_email'] : $tempUser['user_email'];
      $xmlRegistrationDate = date(DATE_RFC3339_EXTENDED, strtotime($tempUser['user_registered']));
      $xmlCustomerFirstName = isset($tempUser['meta']['billing_first_name']) && $tempUser['meta']['billing_first_name'] !== '' ? $tempUser['meta']['billing_first_name'] : (isset($tempUser['meta']['first_name']) ? $tempUser['meta']['first_name'] : '');
      $xmlCustomerLastName  = isset($tempUser['meta']['billing_last_name']) && $tempUser['meta']['billing_last_name'] !== '' ? $tempUser['meta']['billing_last_name'] : (isset($tempUser['meta']['last_name']) ? $tempUser['meta']['last_name'] : '');
      $xmlCustomerPhone      = isset($tempUser['meta']['billing_phone']) ? $tempUser['meta']['billing_phone'] : '';
    } else {
      // Unregistred user
      $xmlCustomerId = '';
      $xmlCustomerEmail = $tempUser['meta']['billing_email'];
      $xmlRegistrationDate = '';
      $xmlCustomerFirstName = isset($tempUser['meta']['billing_first_name']) ? $tempUser['meta']['billing_first_name'] : '';
      $xmlCustomerLastName = isset($tempUser['meta']['billing_last_name']) ? $tempUser['meta']['billing_last_name'] : '';
      $xmlCustomerPhone = isset($tempUser['meta']['billing_phone']) ? $tempUser['meta']['billing_phone'] : '';
    }

    // Skip generation if the email is busted
    if (!is_email($xmlCustomerEmail)) {
      continue;
    }

    $xmlContent .= '<CUSTOMER>' .
      ($xmlCustomerId != '' ? '<CUSTOMER_ID>' . $xmlCustomerId . '</CUSTOMER_ID>' : '') .
      '<EMAIL>' . $xmlCustomerEmail . '</EMAIL>' .
      ($xmlRegistrationDate != '' ? '<REGISTRATION>' . $xmlRegistrationDate . '</REGISTRATION>' : '') .
      '<NEWSLETTER_FREQUENCY>' . $sambaNewsletterFrequency['everyDay'] . '</NEWSLETTER_FREQUENCY>' .
      ($xmlCustomerFirstName != '' ? '<FIRST_NAME>' . $xmlCustomerFirstName . '</FIRST_NAME>' : '') .
      ($xmlCustomerLastName != '' ? '<LAST_NAME>' . $xmlCustomerLastName . '</LAST_NAME>' : '') .
      ($xmlCustomerPhone != '' ? '<PHONE>' . $xmlCustomerPhone . '</PHONE>' : '') .
      '<SMS_FREQUENCY>every day</SMS_FREQUENCY>' .
      '</CUSTOMER>' . PHP_EOL;
  }

  $xmlContent .= '</CUSTOMERS>';

  $xmlFile = fopen(WP_CONTENT_DIR . '/sambaHelperExport/sambaHelperCustomers.xml', 'w+');
  fwrite($xmlFile, $xmlContent);
  fclose($xmlFile);

  if ($ajaxMode) {
    die();
  }
}
