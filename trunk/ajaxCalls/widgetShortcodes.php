<?php

function sambaaiprefix_sambaAiShortcode($atts) {

  $id = $atts['id'];

  global $wpdb;

  $tableName = $wpdb->prefix . "samba_ai_widgets";

  $retrievedWidget = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tableName WHERE id = %d", $id));

  // Fix DB-friendly format
?>
    <?php echo str_replace("\\", "", $retrievedWidget->content); ?>
<?php
}

add_shortcode('shwidget', 'sambaaiprefix_sambaAiShortcode');
