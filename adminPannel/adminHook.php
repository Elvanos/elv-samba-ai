<?php

function getFullSiteName() {
  $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ||
    $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
  $domainName = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  return $protocol . $domainName;
}
// Action hooks
add_action('admin_menu', 'sambaHelperMenus');
add_action('admin_enqueue_scripts', function () use ($pluginConfig) {
  addAdminScript($pluginConfig);
});
add_action('admin_enqueue_scripts', function () use ($pluginConfig) {
  addAdminStyle($pluginConfig);
});

// Script loader
function addAdminScript($pluginConfig) {

  // Register the script
  wp_register_script('elvSambaHelperaAdminControl', $pluginConfig['pluginURL'] . '/assets/js/dist/adminControl.dev.js', array('jquery'), $pluginConfig['pluginVersion']);

  // Localize the script with new data
  $translationArray = array(
    'generalError' => __("Something went wrong. \n please try again later or contact our support.", 'elv-samba-helper'),
    'sambaIDRegenerateConfirm' => __("Confirm regeneration of the access password.\n\nThis action is irreversible.", 'elv-samba-helper'),
    'successRegeneratedAccessPassword' => __("Access password has been succesfully regenerated. \n\n The page will now reload with new links.", 'elv-samba-helper'),
    'successUserIdSave' => __("Your Samba ID key has been successfully saved.", 'elv-samba-helper'),
    'confirmShortcodeDeletion' => __("Are you sure you want to delete this shortcode?\n\nThis action is irreversible.", 'elv-samba-helper'),
    'shortcodeDynamicEdit' => __('Edit', 'elv-samba-helper'),
    'shortcodeDynamicRemove' => __('Remove', 'elv-samba-helper'),
    'shortcodeSuccessUpdate' => __('Shortcode has been successfully updated.', 'elv-samba-helper'),
    'generatingProducts' => __("Generating XML for products... Don't close the browser window!"),
    'generatingProductCategories' => __("Generating XML for produkt categories... Don't close the browser window!"),
    'generatingCustomers' => __("Generating XML for customers... Don't close the browser window!"),
    'generatingOrders' => __("Generating XML for orders... Don't close the browser window!"),
    'reportProducts' => __("Products:"),
    'reportProductCategories' => __("Product categories:"),
    'reportCustomers' => __("Customers:"),
    'reportOrders' => __("Orders:"),
    'feedGenerationDone' => __("Data feed generation finished:"),
    'feedSuccess' => __("Success"),
    'feedFailure' => __("Failure"),

  );
  wp_localize_script('elvSambaHelperaAdminControl', 'elvSambaHelperTranslations', $translationArray);

  // Enqueued script with localized data.
  wp_enqueue_script('elvSambaHelperaAdminControl', $pluginConfig['pluginURL'] . '/assets/js/dist/adminControl.dev.js', [], $pluginConfig['pluginVersion']);
}

// Style loader
function addAdminStyle($pluginConfig) {
  wp_register_style('elvSambaHelperaAdminStyle', $pluginConfig['pluginURL'] . '/assets/css/dist/adminStyle.css', false, $pluginConfig['pluginVersion']);
  wp_enqueue_style('elvSambaHelperaAdminStyle', $pluginConfig['pluginURL'] . '/assets/css/dist/adminStyle.css', false, $pluginConfig['pluginVersion']);
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
function sambaHelperMenus($pluginConfig) {

  if (!is_plugin_active('woocommerce/woocommerce.php')) {
    return;
  }

  add_menu_page(
    __('Samba Helper', 'elv-samba-helper'),
    __('Samba Helper', 'elv-samba-helper'),
    'manage_options',
    'elv-samba-helper-admin-page',
    'renderSambaHelperPage',
    'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAAH6ji2bAAAACXBIWXMAAA7DAAAOwwHHb6hkAAABTUlEQVR4nGNgQAb/QaBN8/+/Gfr/Gbhlff6DMIN4OpKSP/vl/v/v0IZIw6X+HDH8D8O/jxhsBJl0G2YAu6wnAwhjB0AVp5C1/18VCnVIi/r//+1a//936kDcATMPxUwUV8IwSQBkFbID0PHPffraEAcBwcePXzDcgIxBClEcinCfMwMK5pL1IQrTCNy+/Qinj0FRgIgGAsEDMY7N6v/uu////5sJjK9WDXAigsXd/y5dsEJgKJyFm4gvDGHhiBGGWBUSFdggjF0hUqqB4QGOGSQAciK+sEeJtDPGDOgYAhQcOIiJSLyGI6WIWxtv/AdHNk4DjwLxcSg+isfQi3qQeAYZdvE7MPXAnDk3DJL7YSkJlppAGFj2gFMVNGX979aD4B59SIz+RwLYohwe9UCAK/URlaOxYaIMJNYwLlnvBUxaTQyEMNEJluiETW0DAdRcMnhFKRrBAAAAAElFTkSuQmCC'
  );
  add_submenu_page(
    'elv-samba-helper-admin-page',
    __('Data feeds', 'elv-samba-helper'),
    __('Data feeds', 'elv-samba-helper'),
    'manage_options',
    '?page=elv-samba-helper-admin-page'
  );
  add_submenu_page(
    'elv-samba-helper-admin-page',
    __('Analytics', 'elv-samba-helper'),
    __('Analytics', 'elv-samba-helper'),
    'manage_options',
    '?page=elv-samba-helper-admin-page&tab=analytics'
  );
  add_submenu_page(
    'elv-samba-helper-admin-page',
    __('Personalization', 'elv-samba-helper'),
    __('Personalization', 'elv-samba-helper'),
    'manage_options',
    '?page=elv-samba-helper-admin-page&tab=personalization'
  );

  // Prevent parent page appearing as child of itself (wtf???)
  remove_submenu_page('elv-samba-helper-admin-page', 'elv-samba-helper-admin-page');
}


// Renderer
function renderSambaHelperPage($urlArray) {

  if (!is_plugin_active('woocommerce/woocommerce.php')) {
    return;
  }

  // Trial mode
  $trialMode = get_option('sambaHelperTrialMode') !== 'checked' ? '' : 'checked';

  // Basic link
  $urlArray = parse_url(get_site_url());
  $preHash = get_option('sambaHelperPrehash');
  $loginDetails = $urlArray['scheme'] . '://sambaHelper:' . $preHash . '@' . $urlArray['host'] . $urlArray['path'] . '/wp-content/sambaHelperExport/';

  // File links
  $productsLink = $loginDetails . 'sambaHelperProducts.xml';
  $productsCategoriesLink = $loginDetails . 'sambaHelperProductsCategories.xml';
  $customersLink = $loginDetails . 'sambaHelperCustomers.xml';
  $ordersLink = $loginDetails . 'sambaHelperOrders.xml';

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
    window.elvSambaHelpercurrentlySelectedTab = '<?= $tab ?>'
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

    <h1 class="elv_sambaHelper_title">
      <?php _e('Samba Helper', 'elv-samba-helper') ?>
    </h1>
    <br>

    <nav class=" nav-tab-wrapper">
      <a href="?page=elv-samba-helper-admin-page" class="nav-tab <?php if ($tab === 'feeds') : ?>nav-tab-active<?php endif; ?>">
        <?php _e('Data feeds', 'elv-samba-helper') ?>
      </a>
      <a href="?page=elv-samba-helper-admin-page&tab=analytics" class="nav-tab <?php if ($tab === 'analytics') : ?>nav-tab-active<?php endif; ?>">
        <?php _e('Analytics', 'elv-samba-helper') ?>
      </a>
      <a href="?page=elv-samba-helper-admin-page&tab=personalization" class="nav-tab <?php if ($tab === 'personalization') : ?>nav-tab-active<?php endif; ?>">
        <?php _e('Personalization', 'elv-samba-helper') ?>
      </a>
    </nav>

    <div class="tab-content">

      <?php
      // FEEDS tab
      if ($tab == 'feeds') { ?>
        <h2 class="elv_sambaHelper_subtitle"><?php _e('Data feed links', 'elv-samba-helper') ?></h2>
        <br><br>

        <label for="elv_sambaHelper_autoInputProducts">
          <h3><?php _e('Products feed link', 'elv-samba-helper') ?></h3>
        </label>
        <input type="text" id="elv_sambaHelper_autoInputProducts" class="elv_sambaHelper_autoInput" value="<?= $productsLink ?>" readonly>
        <br><br>

        <!-- 
          <label for="elv_sambaHelper_autoInputProductCategories">
            <?php _e('Products categories feed link', 'elv-samba-helper') ?>
          </label>
          <br>
          <input type="text" id="elv_sambaHelper_autoInputProductCategories" class="elv_sambaHelper_autoInput" value="<?= $productsCategoriesLink ?>" readonly>
          <br><br> -->

        <label for="elv_sambaHelper_autoInputOrders">
          <h3><?php _e('Orders feed link', 'elv-samba-helper') ?></h3>
        </label>
        <input type="text" id="elv_sambaHelper_autoInputOrders" class="elv_sambaHelper_autoInput" value="<?= $ordersLink ?>" readonly>
        <br><br>

        <label for="elv_sambaHelper_autoInputCustomers">
          <h3><?php _e('Customers feed link', 'elv-samba-helper') ?></h3>
        </label>
        <input type="text" id="elv_sambaHelper_autoInputCustomers" class="elv_sambaHelper_autoInput" value="<?= $customersLink ?>" readonly>
        <br><br>

        <input type="checkbox" <?= $trialMode ?> id="elv_sambaHelper_trialMode" class="js-setTrialMode"> <label for="elv_sambaHelper_trialMode" class="elv_sambaHelper_checkboxLabel">
          <?php _e('Create data feeds only for 200 first customers (Samba trial mode)', 'elv-samba-helper') ?>
        </label>

        <br><br><br>

        <button class="button button-primary button-large js-regenerateFeedXML">
          <?php _e('Manually update data feed files', 'elv-samba-helper') ?>
        </button><span class="elv_sambaHelper_regenerateMessage js-reportButtonText"></span>

        <br><br><br>

        <button class="button button-primary button-large js-regeneratePasswordHash">
          <?php _e('Regenerate access password', 'elv-samba-helper') ?>
        </button>

      <?php } ?>

      <?php
      // ANALYTICS tab
      if ($tab == 'analytics') { ?>
        <h2 class="elv_sambaHelper_subtitle"><?php _e('Analytics', 'elv-samba-helper') ?></h2>
        <br><br>

        <label for="elv_sambaHelper_userID">
          <h3><?php _e('Your Samba ID key', 'elv-samba-helper') ?></h3>
        </label>
        <input type="text" id="elv_sambaHelper_userID" class="elv_sambaHelper_autoInput elv_sambaHelper_userID js-saveUserID-value" value="<?= $sambaUserID ?>">
        <br><br>
        <button class="button button-primary button-large js-saveUserID-trigger">
          <?php _e('Save the ID key', 'elv-samba-helper') ?>
        </button>

      <?php } ?>

      <?php
      // PERSONALIZATION tab
      if ($tab == 'personalization') { ?>

        <h2 class="elv_sambaHelper_subtitle"><?php _e('Personalization', 'elv-samba-helper') ?></h2>
        <br><br>
        <?php if (empty($widgettabid)) { ?>

          <div class="elv_sambaHelper_widgetAddNew">
            <button class="button button-primary button-medium js-createWidget-trigger">
              <?php _e('Add new widget', 'elv-samba-helper') ?>
            </button>
          </div>

          <table class="elv_sambaHelper_widgetList js-widgetList">
            <tr>
              <th>
                <h3><?php _e('Name', 'elv-samba-helper') ?></h3>
              </th>
              <th>
                <h3><?php _e('Shortcode', 'elv-samba-helper') ?></h3>
              </th>
              <th>
              </th>
            </tr>
            <?php foreach ($widgets as $widget) { ?>
              <tr class="elv_sambaHelper_widgetLine js-widgetLine" data-id="<?= $widget->id; ?>">
                <td>
                  <?= $widget->name; ?>
                </td>
                <td>
                  <input type="text" readonly value='[shwidget id="<?= $widget->id; ?>"]'>
                </td>
                <td class="elv_sambaHelper_widgetControlTd">
                  <a href="<?= getFullSiteName() . "&widgettabid=" . $widget->id; ?>">
                    <button class="button button-primary button-medium">
                      <?php _e('Edit', 'elv-samba-helper') ?>
                    </button>
                  </a>
                </td>
                <td class="elv_sambaHelper_widgetControlTd">
                  <button class="button button-secondary button-medium js-deleteWidget-trigger">
                    <?php _e('Remove', 'elv-samba-helper') ?>
                  </button>
                </td>
              </tr>
            <?php } ?>
          </table>

          <div class="elv_sambaHelper_widgetAddNew">
            <button class="button button-primary button-medium js-createWidget-trigger">
              <?php _e('Add new widget', 'elv-samba-helper') ?>
            </button>
          </div>
        <?php } ?>


        <?php if (!empty($widgettabid)) {
          $widgetData = retrieveWidget(false, $widgettabid);
        ?>
          <div class="elv_sambaHelper_singleWidget js-singleWidget" data-id="<?= $widgetData->id; ?>">
            <div class="elv_sambaHelper_singleWidgetDuo">
              <input type="text" readonly value='[shwidget id="<?= $widgetData->id; ?>"]' style="width:auto;">
            </div>
            <div class="elv_sambaHelper_singleWidgetDuo">
              <label for="widgetname">
                <h3><?php _e('Name', 'elv-samba-helper') ?></h3>
              </label>
              <input type="text" value="<?= $widgetData->name ?>" id="widgetname" name="widgetname" class="js-widgetUpdateName">
            </div>
            <div class="elv_sambaHelper_singleWidgetDuo">
              <label for="widgetcontent">
                <h3><?php _e('Content', 'elv-samba-helper') ?></h3>
              </label>
              <textarea type="text" id="widgetcontent" name="widgetcontent" class="js-widgetUpdateContent"><?= $widgetData->content ?></textarea>
            </div>

            <div class="elv_sambaHelper_singleWidgetDuo -controlButtons">
              <button class="button button-primary button-medium js-updateWidgetSingle-trigger"><?php _e('Save widget', 'elv-samba-helper') ?></button>
              <button class="button button-secondary button-medium js-deleteWidget-trigger"><?php _e('Remove widget', 'elv-samba-helper') ?></button>
            </div>
          </div>

        <?php } ?>

      <?php } ?>

    </div>
  </div>

<?php
}
