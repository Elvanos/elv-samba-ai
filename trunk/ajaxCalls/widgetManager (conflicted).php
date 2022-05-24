<?php

function sambaaiprefix_tryCreateWidgetDatabase() {

  global $wpdb;
  $charsetCollate = $wpdb->get_charset_collate();

  $tableName = $wpdb->prefix . "samba_ai_widgets";

  $sql = "CREATE TABLE $tableName (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    name tinytext NOT NULL,
    content longtext NOT NULL,
    PRIMARY KEY  (id)
  ) $charsetCollate;";

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

  maybe_create_table($tableName, $sql);

  $tableName = $wpdb->prefix . "samba_ai_widgets";
}
sambaaiprefix_tryCreateWidgetDatabase();


function sambaaiprefix_retrieveAllWidgets() {

  global $wpdb;

  $tableName = $wpdb->prefix . "samba_ai_widgets";

  $results = $wpdb->get_results("SELECT * FROM $tableName");

  return $results;
}

function sambaaiprefix_retrieveWidget($ajaxMode = true, $widgetId) {

  global $wpdb;

  $tableName = $wpdb->prefix . "samba_ai_widgets";

  $retrievedWidget = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tableName WHERE id = %d", $widgetId));

  if ($ajaxMode) {
    echo esc_html($retrievedWidget);
    die();
  } else {
    return $retrievedWidget;
  }
}

function sambaaiprefix_createWidget($ajaxMode = true) {

  if (isset($_POST['ajax_mode'])) {
    $ajaxMode = rest_sanitize_boolean($_POST['ajax_mode']);
  }

  global $wpdb;
  $tableName = $wpdb->prefix . "samba_ai_widgets";
  $wpdb->insert(
    $tableName,
    array(
      'time' => current_time('mysql'),
      'name' => '',
      'content' => '',
    )
  );
  $lastid = $wpdb->insert_id;

  if ($ajaxMode) {
    echo esc_textarea($lastid);
    die();
  }
}

function sambaaiprefix_updateWidget($ajaxMode = true, $widgetId = '', $name = '', $content = '') {

  if (isset($_POST['ajax_mode'])) {
    $ajaxMode = rest_sanitize_boolean($_POST['ajax_mode']);
  }

  if (isset($_POST['widget_id'])) {
    $widgetId = sanitize_text_field($_POST['widget_id']);
  }

  if (isset($_POST['name'])) {
    $name = sanitize_text_field($_POST['name']);
  }

  if (isset($_POST['content'])) {
    $content = wp_kses_post($_POST['content']);
  }

  global $wpdb;
  $tableName = $wpdb->prefix . "samba_ai_widgets";

  $wpdb->update(
    $tableName,
    array(
      'name' => $name,
      'content' => $content
    ),
    array('id' => $widgetId),
    array(
      '%s',
      '%s'
    ),
    array('%d')
  );

  if ($ajaxMode) {
    die();
  }
}

function sambaaiprefix_deleteWidget($ajaxMode = true, $widgetId = '') {

  if (isset($_POST['ajax_mode'])) {
    $ajaxMode = rest_sanitize_boolean($_POST['ajax_mode']);
  }

  if (isset($_POST['value'])) {
    $widgetId = sanitize_text_field($_POST['value']);
  }

  global $wpdb;
  $tableName = $wpdb->prefix . "samba_ai_widgets";
  $wpdb->delete(
    $tableName,
    ['id' => $widgetId],
    ['%d'] // integer formatting
  );

  if ($ajaxMode) {
    die();
  }
}
