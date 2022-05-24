<?php

function sambaaiprefix_wpTrialModeSetup() {
  if (!get_option('sambaAiTrialMode')) {
    add_option('sambaAiTrialMode', 'non-checked');
  }
}
sambaaiprefix_wpTrialModeSetup();
