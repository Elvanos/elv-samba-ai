<?php

function getFullSiteName() {
  $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ||
    $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
  $domainName = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  return $protocol . $domainName;
}
// Action hooks
add_action('admin_menu', 'sambaAiMenus');
add_action('admin_enqueue_scripts', function () use ($pluginConfig) {
  addAdminScript($pluginConfig);
});
add_action('admin_enqueue_scripts', function () use ($pluginConfig) {
  addAdminStyle($pluginConfig);
});

// Script loader
function addAdminScript($pluginConfig) {

  // Register the script
  wp_register_script('sambaAiaAdminControl', $pluginConfig['pluginURL'] . '/assets/js/dist/adminControl.dev.js', array('jquery'), $pluginConfig['pluginVersion']);

  // Localize the script with new data
  $translationArray = array(
    'generalError' => __("Something went wrong. \n please try again later or contact our support.", 'samba-ai'),
    'sambaIDRegenerateConfirm' => __("Confirm regeneration of the access password.\n\nThis action is irreversible.", 'samba-ai'),
    'successRegeneratedAccessPassword' => __("Access password has been succesfully regenerated. \n\n The page will now reload with new links.", 'samba-ai'),
    'successUserIdSave' => __("Your Samba ID key has been successfully saved.", 'samba-ai'),
    'confirmShortcodeDeletion' => __("Are you sure you want to delete this shortcode?\n\nThis action is irreversible.", 'samba-ai'),
    'shortcodeDynamicEdit' => __('Edit', 'samba-ai'),
    'shortcodeDynamicRemove' => __('Remove', 'samba-ai'),
    'shortcodeSuccessUpdate' => __('Shortcode has been successfully updated.', 'samba-ai'),
    'generatingProducts' => __("Generating XML for products... Don't close the browser window!", 'samba-ai'),
    'generatingProductCategories' => __("Generating XML for produkt categories... Don't close the browser window!", 'samba-ai'),
    'generatingCustomers' => __("Generating XML for customers... Don't close the browser window!", 'samba-ai'),
    'generatingOrders' => __("Generating XML for orders... Don't close the browser window!", 'samba-ai'),
    'reportProducts' => __("Products:", 'samba-ai'),
    'reportProductCategories' => __("Product categories:", 'samba-ai'),
    'reportCustomers' => __("Customers:", 'samba-ai'),
    'reportOrders' => __("Orders:", 'samba-ai'),
    'feedGenerationDone' => __("Data feed generation finished:", 'samba-ai'),
    'feedSuccess' => __("Success", 'samba-ai'),
    'feedFailure' => __("Failure", 'samba-ai'),

  );
  wp_localize_script('sambaAiaAdminControl', 'sambaAiTranslations', $translationArray);

  // Enqueued script with localized data.
  wp_enqueue_script('sambaAiaAdminControl', $pluginConfig['pluginURL'] . '/assets/js/dist/adminControl.dev.js', [], $pluginConfig['pluginVersion']);
}

// Style loader
function addAdminStyle($pluginConfig) {
  wp_register_style('sambaAiaAdminStyle', $pluginConfig['pluginURL'] . '/assets/css/dist/adminStyle.css', false, $pluginConfig['pluginVersion']);
  wp_enqueue_style('sambaAiaAdminStyle', $pluginConfig['pluginURL'] . '/assets/css/dist/adminStyle.css', false, $pluginConfig['pluginVersion']);
}

// AJAX calls list
add_action('wp_ajax_regeneratePasswordHash', 'regeneratePasswordHash');

add_action('wp_ajax_generateXML_customers', 'generateXML_customers');
add_action('wp_ajax_generateXML_orders', 'generateXML_orders');
add_action('wp_ajax_generateXML_products', 'generateXML_products');
add_action('wp_ajax_generateXML_productsCategories', 'generateXML_productsCategories');

add_action('wp_ajax_getTrialMode', 'getTrialMode');
add_action('wp_ajax_setTrialMode', 'setTrialMode');

add_action('wp_ajax_getSambaUserAnalyticsId', 'getSambaUserAnalyticsId');
add_action('wp_ajax_setSambaUserAnalyticsId', 'setSambaUserAnalyticsId');

add_action('wp_ajax_createWidget', 'createWidget');
add_action('wp_ajax_updateWidget', 'updateWidget');
add_action('wp_ajax_deleteWidget', 'deleteWidget');

add_action('wp_ajax_nopriv_retrieveWCCartContent', 'retrieveWCCartContent');
add_action('wp_ajax_retrieveWCCartContent', 'retrieveWCCartContent');


// Admin page menu items
function sambaAiMenus($pluginConfig) {

  if (!is_plugin_active('woocommerce/woocommerce.php')) {
    return;
  }

  add_menu_page(
    __('Samba.ai', 'samba-ai'),
    __('Samba.ai', 'samba-ai'),
    'manage_options',
    'samba-ai-admin-page',
    'renderSambaAiPage',
    'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAAH6ji2bAAAACXBIWXMAAA7DAAAOwwHHb6hkAAABTUlEQVR4nGNgQAb/QaBN8/+/Gfr/Gbhlff6DMIN4OpKSP/vl/v/v0IZIw6X+HDH8D8O/jxhsBJl0G2YAu6wnAwhjB0AVp5C1/18VCnVIi/r//+1a//936kDcATMPxUwUV8IwSQBkFbID0PHPffraEAcBwcePXzDcgIxBClEcinCfMwMK5pL1IQrTCNy+/Qinj0FRgIgGAsEDMY7N6v/uu////5sJjK9WDXAigsXd/y5dsEJgKJyFm4gvDGHhiBGGWBUSFdggjF0hUqqB4QGOGSQAciK+sEeJtDPGDOgYAhQcOIiJSLyGI6WIWxtv/AdHNk4DjwLxcSg+isfQi3qQeAYZdvE7MPXAnDk3DJL7YSkJlppAGFj2gFMVNGX979aD4B59SIz+RwLYohwe9UCAK/URlaOxYaIMJNYwLlnvBUxaTQyEMNEJluiETW0DAdRcMnhFKRrBAAAAAElFTkSuQmCC'
  );
  add_submenu_page(
    'samba-ai-admin-page',
    __('Data feeds', 'samba-ai'),
    __('Data feeds', 'samba-ai'),
    'manage_options',
    '?page=samba-ai-admin-page'
  );
  add_submenu_page(
    'samba-ai-admin-page',
    __('Analytics', 'samba-ai'),
    __('Analytics', 'samba-ai'),
    'manage_options',
    '?page=samba-ai-admin-page&tab=analytics'
  );
  add_submenu_page(
    'samba-ai-admin-page',
    __('Personalization', 'samba-ai'),
    __('Personalization', 'samba-ai'),
    'manage_options',
    '?page=samba-ai-admin-page&tab=personalization'
  );

  // Prevent parent page appearing as child of itself (wtf???)
  remove_submenu_page('samba-ai-admin-page', 'samba-ai-admin-page');
}


// Renderer
function renderSambaAiPage($urlArray) {

  if (!is_plugin_active('woocommerce/woocommerce.php')) {
    return;
  }

  // Trial mode
  $trialMode = get_option('sambaAiTrialMode') !== 'checked' ? '' : 'checked';

  // Basic link
  $urlArray = parse_url(get_site_url());
  $preHash = get_option('sambaAiPrehash');
  $loginDetails = $urlArray['scheme'] . '://sambaAi:' . $preHash . '@' . $urlArray['host'] . $urlArray['path'] . '/wp-content/sambaAiExport/';

  // File links
  $productsLink = $loginDetails . 'sambaAiProducts.xml';
  $productsCategoriesLink = $loginDetails . 'sambaAiProductsCategories.xml';
  $customersLink = $loginDetails . 'sambaAiCustomers.xml';
  $ordersLink = $loginDetails . 'sambaAiOrders.xml';

  // Analytics ID
  $sambaUserID = getSambaUserAnalyticsId(false);

  // Widget list (ineffective to call it here, but it likely won't matter since it a very small DB)
  $widgets = retrieveAllWidgets();

  // Tab management
  $default_tab = 'feeds';
  $tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
  $widgettabid = isset($_GET['widgettabid']) ? $_GET['widgettabid'] : '';

?>
  <script>
    window.sambaAicurrentlySelectedTab = '<?= $tab ?>'
  </script>

  <style class="sambaToRemove">
    #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu {
      background-color: transparent !important;
      color: #f0f0f1 !important;
    }

    ul#adminmenu a.wp-has-current-submenu:after,
    ul#adminmenu>li.current>a.current:after,
    .wp-submenu.wp-submenu-wrap {
      display: none !important;
    }
  </style>

  <div class="wrap">

    <h1 class="sambaAi_title">
      <?php _e('Samba.ai', 'samba-ai') ?>
    </h1>
    <br>

    <nav class=" nav-tab-wrapper">
      <a href="?page=samba-ai-admin-page" class="nav-tab <?php if ($tab === 'feeds') : ?>nav-tab-active<?php endif; ?>">
        <?php _e('Data feeds', 'samba-ai') ?>
      </a>
      <a href="?page=samba-ai-admin-page&tab=analytics" class="nav-tab <?php if ($tab === 'analytics') : ?>nav-tab-active<?php endif; ?>">
        <?php _e('Analytics', 'samba-ai') ?>
      </a>
      <a href="?page=samba-ai-admin-page&tab=personalization" class="nav-tab <?php if ($tab === 'personalization') : ?>nav-tab-active<?php endif; ?>">
        <?php _e('Personalization', 'samba-ai') ?>
      </a>
    </nav>

    <div class="tab-content">

      <?php
      // FEEDS tab
      if ($tab == 'feeds') { ?>
        <h2 class="sambaAi_subtitle"><?php _e('Data feed links', 'samba-ai') ?></h2>
        <br><br>

        <label for="sambaAi_autoInputProducts">
          <h3><?php _e('Products feed link', 'samba-ai') ?></h3>
        </label>
        <input type="text" id="sambaAi_autoInputProducts" class="sambaAi_autoInput" value="<?= $productsLink ?>" readonly>
        <br><br>

        <!-- 
          <label for="sambaAi_autoInputProductCategories">
            <?php _e('Products categories feed link', 'samba-ai') ?>
          </label>
          <br>
          <input type="text" id="sambaAi_autoInputProductCategories" class="sambaAi_autoInput" value="<?= $productsCategoriesLink ?>" readonly>
          <br><br> -->

        <label for="sambaAi_autoInputOrders">
          <h3><?php _e('Orders feed link', 'samba-ai') ?></h3>
        </label>
        <input type="text" id="sambaAi_autoInputOrders" class="sambaAi_autoInput" value="<?= $ordersLink ?>" readonly>
        <br><br>

        <label for="sambaAi_autoInputCustomers">
          <h3><?php _e('Customers feed link', 'samba-ai') ?></h3>
        </label>
        <input type="text" id="sambaAi_autoInputCustomers" class="sambaAi_autoInput" value="<?= $customersLink ?>" readonly>
        <br><br>

        <input type="checkbox" <?= $trialMode ?> id="sambaAi_trialMode" class="js-setTrialMode"> <label for="sambaAi_trialMode" class="sambaAi_checkboxLabel">
          <?php _e('Create data feeds only for 200 first customers (Samba trial mode)', 'samba-ai') ?>
        </label>

        <br><br><br>

        <button class="button button-primary button-large js-regenerateFeedXML">
          <?php _e('Manually update data feed files', 'samba-ai') ?>
        </button><span class="sambaAi_regenerateMessage js-reportButtonText"></span>

        <br><br><br>

        <button class="button button-primary button-large js-regeneratePasswordHash">
          <?php _e('Regenerate access password', 'samba-ai') ?>
        </button>

      <?php } ?>

      <?php
      // ANALYTICS tab
      if ($tab == 'analytics') { ?>
        <h2 class="sambaAi_subtitle"><?php _e('Analytics', 'samba-ai') ?></h2>
        <br><br>

        <label for="sambaAi_userID">
          <h3><?php _e('Your Samba ID key', 'samba-ai') ?></h3>
        </label>
        <input type="text" id="sambaAi_userID" class="sambaAi_autoInput sambaAi_userID js-saveUserID-value" value="<?= $sambaUserID ?>">
        <br><br>
        <button class="button button-primary button-large js-saveUserID-trigger">
          <?php _e('Save the ID key', 'samba-ai') ?>
        </button>

      <?php } ?>

      <?php
      // PERSONALIZATION tab
      if ($tab == 'personalization') { ?>

        <h2 class="sambaAi_subtitle"><?php _e('Personalization', 'samba-ai') ?></h2>
        <br><br>
        <?php if (empty($widgettabid)) { ?>

          <div class="sambaAi_widgetAddNew">
            <button class="button button-primary button-medium js-createWidget-trigger">
              <?php _e('Add new widget', 'samba-ai') ?>
            </button>
          </div>

          <table class="sambaAi_widgetList js-widgetList">
            <tr>
              <th>
                <h3><?php _e('Name', 'samba-ai') ?></h3>
              </th>
              <th>
                <h3><?php _e('Shortcode', 'samba-ai') ?></h3>
              </th>
              <th>
              </th>
            </tr>
            <?php foreach ($widgets as $widget) { ?>
              <tr class="sambaAi_widgetLine js-widgetLine" data-id="<?= $widget->id; ?>">
                <td>
                  <?= $widget->name; ?>
                </td>
                <td>
                  <input type="text" readonly value='[shwidget id="<?= $widget->id; ?>"]'>
                </td>
                <td class="sambaAi_widgetControlTd">
                  <a href="<?= getFullSiteName() . "&widgettabid=" . $widget->id; ?>">
                    <button class="button button-primary button-medium">
                      <?php _e('Edit', 'samba-ai') ?>
                    </button>
                  </a>
                </td>
                <td class="sambaAi_widgetControlTd">
                  <button class="button button-secondary button-medium js-deleteWidget-trigger">
                    <?php _e('Remove', 'samba-ai') ?>
                  </button>
                </td>
              </tr>
            <?php } ?>
          </table>

          <div class="sambaAi_widgetAddNew">
            <button class="button button-primary button-medium js-createWidget-trigger">
              <?php _e('Add new widget', 'samba-ai') ?>
            </button>
          </div>
        <?php } ?>


        <?php if (!empty($widgettabid)) {
          $widgetData = retrieveWidget(false, $widgettabid);
        ?>
          <div class="sambaAi_singleWidget js-singleWidget" data-id="<?= $widgetData->id; ?>">
            <div class="sambaAi_singleWidgetDuo">
              <input type="text" readonly value='[shwidget id="<?= $widgetData->id; ?>"]' style="width:auto;">
            </div>
            <div class="sambaAi_singleWidgetDuo">
              <label for="widgetname">
                <h3><?php _e('Name', 'samba-ai') ?></h3>
              </label>
              <input type="text" value="<?= $widgetData->name ?>" id="widgetname" name="widgetname" class="js-widgetUpdateName">
            </div>
            <div class="sambaAi_singleWidgetDuo">
              <label for="widgetcontent">
                <h3><?php _e('Content', 'samba-ai') ?></h3>
              </label>
              <textarea type="text" id="widgetcontent" name="widgetcontent" class="js-widgetUpdateContent"><?= $widgetData->content ?></textarea>
            </div>

            <div class="sambaAi_singleWidgetDuo -controlButtons">
              <button class="button button-primary button-medium js-updateWidgetSingle-trigger"><?php _e('Save widget', 'samba-ai') ?></button>
              <button class="button button-secondary button-medium js-deleteWidget-trigger"><?php _e('Remove widget', 'samba-ai') ?></button>
            </div>
          </div>

        <?php } ?>

      <?php } ?>

    </div>
  </div>

<?php
}
