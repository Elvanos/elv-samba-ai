<?php

function wpUserAnalyticsIdSetup() {
  if (!get_option('sambaHelperUserAnalyticsId')) {
    add_option('sambaHelperUserAnalyticsId', '');
  }
}
wpUserAnalyticsIdSetup();
