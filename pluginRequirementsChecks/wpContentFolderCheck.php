<?php
function wpContentFolderCheck() {
  if (!file_exists(WP_CONTENT_DIR . '/sambaHelperExport')) {
    mkdir(WP_CONTENT_DIR . '/sambaHelperExport', 0755);
  }
}
wpContentFolderCheck();
