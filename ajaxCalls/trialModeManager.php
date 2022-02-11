<?php


function getTrialMode($ajaxMode = true) {

  $trialModeState = get_option('sambaHelperTrialMode');

  if ($ajaxMode) {
    echo $trialModeState;
    die();
  } else {
    return $trialModeState;
  }
}

/**
 * @param $ajaxMode boolean
 * @param $input 'non-checked' || 'checked'
 */
function setTrialMode($ajaxMode = true, $input = 'non-checked') {

  if (isset($_POST['ajax_mode'])) {
    $ajaxMode = $_POST['ajax_mode'];
  }

  if (isset($_POST['value'])) {
    $input = $_POST['value'];
  }

  update_option('sambaHelperTrialMode', $input);

  if ($ajaxMode) {
    die();
  }
}
