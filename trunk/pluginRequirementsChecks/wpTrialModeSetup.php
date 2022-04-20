<?php

function wpTrialModeSetup() {
  if (!get_option('sambaAiTrialMode')) {
    add_option('sambaAiTrialMode', 'non-checked');
  }
}
wpTrialModeSetup();
