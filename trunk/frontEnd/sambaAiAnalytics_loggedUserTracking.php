<?php

add_action('wp_enqueue_scripts', 'sambaaiprefix_sambaAiAnalytics_loggedUserTracking');

function sambaaiprefix_sambaAiAnalytics_loggedUserTracking() {

  $loggedUserID = get_current_user_id();

  $woo = WC();

  if ($loggedUserID != 0) {

    $woo_customer = $woo->customer;
    $loggedUserEmail = $woo_customer->get_billing_email();
?>

    <!-- Samba.ai customer -->
    <script>
      var _yottlyOnload = _yottlyOnload || [];
      _yottlyOnload.push(function() {

        diffAnalytics.customerLoggedIn('<?php echo esc_js($loggedUserID) ?>')
        diffAnalytics.submitPopupEmail('<?php echo esc_js($loggedUserEmail) ?>', function(err, value) {
          //console.log(value)
        })
      });
    </script>
    <!-- End Samba.ai customer -->
<?php
  }
}
