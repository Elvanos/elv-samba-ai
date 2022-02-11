<?php

function wpTrialModeSetup() {
  if (!get_option('sambaHelperTrialMode')) {
    add_option('sambaHelperTrialMode', 'non-checked');
  }
}
wpTrialModeSetup();
