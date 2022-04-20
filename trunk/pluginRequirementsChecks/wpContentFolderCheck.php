<?php
function wpContentFolderCheck() {
  if (!file_exists(WP_CONTENT_DIR . '/sambaAiExport')) {
    mkdir(WP_CONTENT_DIR . '/sambaAiExport', 0755);
  }
}
wpContentFolderCheck();
