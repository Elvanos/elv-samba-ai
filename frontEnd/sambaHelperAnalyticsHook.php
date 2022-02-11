<?php

add_action('wp_head', 'sambaHelperAnalyticsHook');

function sambaHelperAnalyticsHook() {

  $userID = getSambaUserAnalyticsId(false);

  if ($userID === '') {
    return;
  }

?>
  <script src="https://yottlyscript.com/script.js?tp=<?= $userID ?>"></script>
<?php
}
