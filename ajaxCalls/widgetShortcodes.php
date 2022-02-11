<?php

function sambaHelperShortcode($atts) {

  $id = $atts['id'];

  global $wpdb;

  $tableName = $wpdb->prefix . "samba_helper_widgets";

  $retrievedWidget = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tableName WHERE id = %d", $id));

  // Unescape DB-friendly format
  echo (str_replace("\\", "", $retrievedWidget->content));
}

add_shortcode('shwidget', 'sambaHelperShortcode');