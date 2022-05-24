<?php


function sambaaiprefix_regeneratePasswordHash() {

  if (!function_exists('sambaaiprefix_wpContentFolderSecuritySetup')) {
    require_once '../pluginRequirementsChecks/wpContentFolderSecuritySetup.php';
  }

  $newPass = sambaaiprefix_wpContentFolderSecuritySetup(true, true);

  echo esc_textarea($newPass);

  die();
}
