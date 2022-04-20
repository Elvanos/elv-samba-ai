<?php

/**
 * Autocheck WP action hook that gets called outside the file
 * Checks for requirements to activate the plugin
 *
 * @return void
 */
function wooCommerceAutoCheck($pluginConfig) {
  add_action('admin_init', function () use ($pluginConfig) {

    // Advanced check for WP version with different message
    $wcVersion = (defined('WC_VERSION')) ? WC_VERSION : '0.0.0';
    if (!is_admin() || !current_user_can('activate_plugins') || !is_plugin_active('woocommerce/woocommerce.php') || version_compare($wcVersion, $pluginConfig['requiredWooCommerceVersion'], '<')) {

      add_action('admin_notices', function () use ($pluginConfig) {
        wooCommerceTooOldNottice($pluginConfig);
      });
    }
  });
}

/**
 * Message for the missing WooCommerce
 *
 * @return void
 */
function wooCommerceMissingNottice($pluginConfig) { ?>
  <div class="error">
    <p><?php _e('Samba.ai requires WooCommerce installed and activated.', 'samba-ai'); ?></p>
  </div>
<?php }

/**
 * Message for WoocCommerce being too old
 *
 * @return void
 */
function wooCommerceTooOldNottice($pluginConfig) { ?>
  <div class="error">
    <p><?php printf(
          _e('Samba.ai requires at least %s version of Woocommerce to run.', 'samba-ai'),
          $pluginConfig['requiredWooCommerceVersion']
        ); ?></p>
  </div>
<?php } ?>