<?php

function tryCreateWidgetDatabase() {

  global $wpdb;
  $charsetCollate = $wpdb->get_charset_collate();

  $tableName = $wpdb->prefix . "samba_helper_widgets";

  $sql = "CREATE TABLE $tableName (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    name tinytext NOT NULL,
    content longtext NOT NULL,
    PRIMARY KEY  (id)
  ) $charsetCollate;";

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

  maybe_create_table($tableName, $sql);

  $tableName = $wpdb->prefix . "samba_helper_widgets";
}
tryCreateWidgetDatabase();


function retrieveAllWidgets() {

  global $wpdb;

  $tableName = $wpdb->prefix . "samba_helper_widgets";

  $results = $wpdb->get_results("SELECT * FROM $tableName");

  return $results;
}

function retrieveWidget($ajaxMode = true, $widgetId) {

  global $wpdb;

  $tableName = $wpdb->prefix . "samba_helper_widgets";

  $retrievedWidget = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tableName WHERE id = %d", $widgetId));

  if ($ajaxMode) {
    echo $retrievedWidget;
    die();
  } else {
    return $retrievedWidget;
  }
}

function createWidget($ajaxMode = true) {

  if (isset($_POST['ajax_mode'])) {
    $ajaxMode = $_POST['ajax_mode'];
  }

  global $wpdb;
  $tableName = $wpdb->prefix . "samba_helper_widgets";
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
    echo $lastid;
    die();
  }
}

function updateWidget($ajaxMode = true, $widgetId = '', $name = '', $content = '') {

  if (isset($_POST['ajax_mode'])) {
    $ajaxMode = $_POST['ajax_mode'];
  }

  if (isset($_POST['widget_id'])) {
    $widgetId = $_POST['widget_id'];
  }

  if (isset($_POST['name'])) {
    $name = $_POST['name'];
  }

  if (isset($_POST['content'])) {
    $content = $_POST['content'];
  }

  global $wpdb;
  $tableName = $wpdb->prefix . "samba_helper_widgets";

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

function deleteWidget($ajaxMode = true, $widgetId = '') {

  if (isset($_POST['ajax_mode'])) {
    $ajaxMode = $_POST['ajax_mode'];
  }

  if (isset($_POST['value'])) {
    $widgetId = $_POST['value'];
  }

  global $wpdb;
  $tableName = $wpdb->prefix . "samba_helper_widgets";
  $wpdb->delete(
    $tableName,
    ['id' => $widgetId],
    ['%d'] // integer formatting
  );

  if ($ajaxMode) {
    die();
  }
}
