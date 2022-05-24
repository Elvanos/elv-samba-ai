<?php


function sambaaiprefix_getTrialMode($ajaxMode = true) {

  $trialModeState = get_option('sambaAiTrialMode');

  if ($ajaxMode) {
    echo esc_textarea($trialModeState);
    die();
  } else {
    return $trialModeState;
  }
}

/**
 * @param $ajaxMode boolean
 * @param $input 'non-checked' || 'checked'
 */
function sambaaiprefix_setTrialMode($ajaxMode = true, $input = 'non-checked') {

  if (isset($_POST['ajax_mode'])) {
    $ajaxMode = rest_sanitize_boolean($_POST['ajax_mode']);
  }

  if (isset($_POST['value'])) {
    $input = $_POST['value'];
  }

  // Check for 'non-checked' input
  if ($input === 'non-checked') {
    update_option('sambaAiTrialMode', 'non-checked');
  }

  // Check for 'checked' input
  if ($input === 'checked') {
    update_option('sambaAiTrialMode', 'checked');
  }

  if ($ajaxMode) {
    die();
  }
}
