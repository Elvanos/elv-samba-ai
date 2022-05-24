<?php


function sambaaiprefix_getSambaUserAnalyticsId($ajaxMode = true) {

  $trialModeState = get_option('sambaAiUserAnalyticsId');

  if ($ajaxMode) {
    echo esc_html($trialModeState);
    die();
  } else {
    return $trialModeState;
  }
}

/**
 * @param $ajaxMode boolean
 * @param $input string
 */
function sambaaiprefix_setSambaUserAnalyticsId($ajaxMode = true, $input = '') {

  if (isset($_POST['ajax_mode'])) {
    $ajaxMode = rest_sanitize_boolean($_POST['ajax_mode']);
  }

  if (isset($_POST['value'])) {
    $input = sanitize_text_field($_POST['value']);
  }

  update_option('sambaAiUserAnalyticsId', $input);

  if ($ajaxMode) {
    die();
  }
}
