<?php


function getSambaUserAnalyticsId($ajaxMode = true) {

  $trialModeState = get_option('sambaHelperUserAnalyticsId');

  if ($ajaxMode) {
    echo $trialModeState;
    die();
  } else {
    return $trialModeState;
  }
}

/**
 * @param $ajaxMode boolean
 * @param $input string
 */
function setSambaUserAnalyticsId($ajaxMode = true, $input = '') {

  if (isset($_POST['ajax_mode'])) {
    $ajaxMode = $_POST['ajax_mode'];
  }

  if (isset($_POST['value'])) {
    $input = $_POST['value'];
  }

  update_option('sambaHelperUserAnalyticsId', $input);

  if ($ajaxMode) {
    die();
  }
}
