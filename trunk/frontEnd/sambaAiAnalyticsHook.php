<?php

add_action('wp_head', 'sambaAiAnalyticsHook');

function sambaAiAnalyticsHook() {

  $userID = getSambaUserAnalyticsId(false);

  if ($userID === '') {
    return;
  }

?>
  <script src="https://yottlyscript.com/script.js?tp=<?= $userID ?>"></script>
<?php
}
