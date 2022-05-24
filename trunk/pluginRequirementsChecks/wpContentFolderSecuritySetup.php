<?php

function sambaaiprefix_wpContentFolderSecuritySetup($forceRewrite = false, $ajaxMode = false) {

  sambaaiprefix_checkExistingPassword($forceRewrite);
  sambaaiprefix_generateAccessFile();
  $pass = sambaaiprefix_generatePasswordFile($ajaxMode);

  if ($ajaxMode) {
    return $pass;
  }
}

function sambaaiprefix_checkExistingPassword($forceRewrite = false) {
  if (!get_option('sambaAiPrehash')) {
    add_option('sambaAiPrehash', bin2hex(openssl_random_pseudo_bytes(16)));
  } else if ($forceRewrite) {
    update_option('sambaAiPrehash', bin2hex(openssl_random_pseudo_bytes(16)));
  }
}

function sambaaiprefix_generateAccessFile() {
  $file = fopen(WP_CONTENT_DIR . "/sambaAiExport/.htaccess", "w");

  fwrite($file, "# RewriteEngine on\n");
  fwrite($file, "# RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization},L]\n");
  fwrite($file, "ErrorDocument 401 \"Authorisation Required\"\n");
  fwrite($file, "\n");
  fwrite($file, "AuthType Basic\n");
  fwrite($file, "AuthName \"SambaExports\"\n");
  fwrite($file, "AuthUserFile " . WP_CONTENT_DIR . "/sambaAiExport/.htpasswd\n");
  fwrite($file, "Require valid-user");

  fclose($file);
}

function sambaaiprefix_generatePasswordFile($ajaxMode = false) {
  $pass = get_option('sambaAiPrehash');
  $hash = sambaaiprefix_generatePasswordHash($pass);

  $file = fopen(WP_CONTENT_DIR . "/sambaAiExport/.htpasswd", "w");

  fwrite($file, "sambaAi:" . $hash);
  fclose($file);

  if ($ajaxMode) {
    return $pass;
  }
}

function sambaaiprefix_generatePasswordHash($pass) {

  // APR1-MD5 encryption method (windows compatible)

  $salt = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz0123456789"), 0, 8);
  $len = strlen($pass);
  $text = $pass . '$apr1$' . $salt;
  $bin = pack("H32", md5($pass . $salt . $pass));
  for ($i = $len; $i > 0; $i -= 16) {
    $text .= substr($bin, 0, min(16, $i));
  }
  for ($i = $len; $i > 0; $i >>= 1) {
    $text .= ($i & 1) ? chr(0) : $pass[0];
  }
  $bin = pack("H32", md5($text));
  for ($i = 0; $i < 1000; $i++) {
    $new = ($i & 1) ? $pass : $bin;
    if ($i % 3) $new .= $salt;
    if ($i % 7) $new .= $pass;
    $new .= ($i & 1) ? $bin : $pass;
    $bin = pack("H32", md5($new));
  }
  for ($i = 0; $i < 5; $i++) {
    $k = $i + 6;
    $j = $i + 12;
    if ($j == 16) $j = 5;
    $tmp = $bin[$i] . $bin[$k] . $bin[$j] . $tmp;
  }
  $tmp = chr(0) . chr(0) . $bin[11] . $tmp;
  $tmp = strtr(
    strrev(substr(base64_encode($tmp), 2)),
    "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",
    "./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"
  );
  return "$" . "apr1" . "$" . $salt . "$" . $tmp;
}
