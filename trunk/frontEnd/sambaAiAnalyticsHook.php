<?php

add_action('wp_enqueue_scripts', 'sambaaiprefix_sambaAiAnalyticsHook');

function sambaaiprefix_sambaAiAnalyticsHook() {

  $userID = sambaaiprefix_getSambaUserAnalyticsId(false);

  if ($userID === '') {
    return;
  }

  wp_enqueue_script('sambaaiprefix_sambaAiAnalyticsHook', 'https://yottlyscript.com/script.js?tp=' . esc_attr($userID));
}
