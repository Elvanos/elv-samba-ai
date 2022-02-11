<?php

/**
 * @package   Sambla Helper
 * @author    Elvanos
 * @license   GPL-2.0+
 * @link      
 * @copyright 2022 Elvanos
 *
 * Plugin Name:       Sambla Helper
 * Plugin URI:        
 * Description:       Helps with connection to the Samba automation services
 * Version:           1.1.0
 * Requires at least: 5.7.4
 * Requires PHP:      7.2
 * Author:            Elvanos
 * Author URI:        
 * Text Domain:       elv-samba-helper
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
  die;
}

// Encapsulate our plugin's internals, because we aren't a band of savages
function runSambaHelper() {

  // Cleanup
  register_deactivation_hook(__FILE__, 'elvSambaHelper_deactivate');
  function elvSambaHelper_deactivate() {

    // Deregister the CRON
    $timestamp = wp_next_scheduled('elvSambaHelper_cron_hook');
    wp_unschedule_event($timestamp, 'elvSambaHelper_cron_hook');
    wp_clear_scheduled_hook('elvSambaHelper_cron_hook');

    // Delete any exports along with the folder
    array_map('unlink', glob(WP_CONTENT_DIR . '/sambaHelperExport/*.*'));
    rmdir(WP_CONTENT_DIR . '/sambaHelperExport');
  }

  // Plugin configuration
  $pluginConfig = [
    'pluginBaseName' => 'elv-samba-helper/elv-samba-helper.php',
    'pluginURL' => plugin_dir_url(__FILE__),
    'pluginDir' => plugin_dir_path(__FILE__),
    'pluginVersion' => '1.1.0',
    'requiredWooCommerceVersion' => '4.0.0',
  ];

  // Plugin init
  function loadTranslations() {
    load_plugin_textdomain('elv-samba-helper', false, dirname(plugin_basename(__FILE__)) . '/languages');
  }
  add_action('init', 'loadTranslations');


  // Check for requirements
  require_once  $pluginConfig['pluginDir'] . '/pluginRequirementsChecks/woocommerceCheck.php';
  wooCommerceAutoCheck($pluginConfig);

  // Create plugin folder and setup security
  require_once  $pluginConfig['pluginDir'] . '/pluginRequirementsChecks/wpContentFolderCheck.php';
  require_once  $pluginConfig['pluginDir'] . '/pluginRequirementsChecks/wpContentFolderSecuritySetup.php';
  wpContentFolderSecuritySetup(false);

  // Check if trial version is hooked up and set up the appropriate option in the DB
  require_once  $pluginConfig['pluginDir'] . '/pluginRequirementsChecks/wpTrialModeSetup.php';

  // Setup analytics ID field
  require_once  $pluginConfig['pluginDir'] . '/pluginRequirementsChecks/wpUserAnalyticsIdSetup.php';

  // Hook up CRON jobs
  require_once  $pluginConfig['pluginDir'] . '/wpFunctionality/wpCronJob.php';

  // Main admin dashboard
  require_once  $pluginConfig['pluginDir'] . '/adminPannel/adminHook.php';

  // Admin Dashboard AJAX calls
  require_once  $pluginConfig['pluginDir'] . '/ajaxCalls/regeneratePasswordHash.php';
  require_once  $pluginConfig['pluginDir'] . '/ajaxCalls/trialModeManager.php';
  require_once  $pluginConfig['pluginDir'] . '/ajaxCalls/sambaUserIdManager.php';

  // AJAX scripts - Special WC scripts
  require_once  $pluginConfig['pluginDir'] . '/ajaxCalls/retrieveWCCartContent.php';

  // AJAX scripts - XML exports
  require_once  $pluginConfig['pluginDir'] . '/ajaxCalls/generateXML_customers.php';
  require_once  $pluginConfig['pluginDir'] . '/ajaxCalls/generateXML_orders.php';
  require_once  $pluginConfig['pluginDir'] . '/ajaxCalls/generateXML_products.php';
  require_once  $pluginConfig['pluginDir'] . '/ajaxCalls/generateXML_productsCategories.php';

  // Widget Scripts
  require_once  $pluginConfig['pluginDir'] . '/ajaxCalls/widgetManager.php';
  require_once  $pluginConfig['pluginDir'] . '/ajaxCalls/widgetShortcodes.php';

  // Kill this if we are in admin
  if (is_admin()) {
    return;
  }

  // Frontend hooks
  require_once  $pluginConfig['pluginDir'] . '/frontEnd/sambaHelperAnalyticsHook.php';
  require_once  $pluginConfig['pluginDir'] . '/frontEnd/sambaHelperAnalytics_loggedUserTracking.php';
  require_once  $pluginConfig['pluginDir'] . '/frontEnd/sambaHelperAnalytics_orderTracking.php';
  require_once  $pluginConfig['pluginDir'] . '/frontEnd/sambaHelperAnalytics_cartInteractionsTracking.php';
  require_once  $pluginConfig['pluginDir'] . '/frontEnd/sambaHelperAnalytics_singleProductTracking.php';
}
runSambaHelper();
