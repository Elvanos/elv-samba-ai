<?php


function regeneratePasswordHash() {

  if (!function_exists('wpContentFolderSecuritySetup')) {
    require_once '../pluginRequirementsChecks/wpContentFolderSecuritySetup.php';
  }

  $newPass = wpContentFolderSecuritySetup(true, true);

  echo $newPass;

  die();
}
