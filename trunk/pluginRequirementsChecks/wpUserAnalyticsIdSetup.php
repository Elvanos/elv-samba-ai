<?php

function wpUserAnalyticsIdSetup() {
  if (!get_option('sambaAiUserAnalyticsId')) {
    add_option('sambaAiUserAnalyticsId', '');
  }
}
wpUserAnalyticsIdSetup();
