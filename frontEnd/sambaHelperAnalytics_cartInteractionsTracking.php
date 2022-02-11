<?php
add_action('wp_head', 'sambaHelperAnalytics_cartInteractionsTracking');

function sambaHelperAnalytics_cartInteractionsTracking() {

  $isCheckout = is_checkout() ? 'true' : 'false';

  global $woocommerce;
  $orderItems = $woocommerce->cart->get_cart();
?>

  <!-- Start Samba.ai cart -->
  <script>
    var isOrderPage = <?php echo $isCheckout; ?>;
    var _yottlyOnload = _yottlyOnload || [];
    _yottlyOnload.push(function() {

      var products = [];

      <?php foreach ($orderItems as $item) { ?>
        products.push({
          productId: '<?= $item['product_id'] ?>',
          amount: <?= $item['quantity'] ?>
        });
      <?php } ?>

      diffAnalytics.cartInteraction({
        content: products,
        onOrderPage: window.isOrderPage
      });

    });
  </script>

  <script>
    jQuery(function($) {

      jQuery(document).ready(function() {

        jQuery('body').on('added_to_cart', function() {
          reportCartStatusToSamba();
        });

        jQuery('body').on('removed_from_cart', function() {
          reportCartStatusToSamba();
        });


        jQuery('body').on('updated_cart_totals', function() {
          reportCartStatusToSamba();
        });

        var reportCartStatusToSamba = function() {
          jQuery.ajax({
            type: 'POST',
            url: "<?= admin_url('admin-ajax.php') ?>",
            data: {
              action: 'retrieveWCCartContent',
            },
            success: function(response) {

              // Fix possibly buggy AXAJ output
              if (response.substring(response.length - 1) == "0") {
                response = response.substring(0, response.length - 1);
              }

              // Parse JSON
              response = JSON.parse(response);

              // Report to Samba
              diffAnalytics.cartInteraction({
                content: response,
                onOrderPage: window.isOrderPage
              });
            },
            error: function(request, status, error) {
              console.log(request.responseText)
              console.log(status)
              console.log(error)
            },
            complete: function() {

            }
          })
        };

      })

    })
  </script>
  <!-- End Samba.ai cart -->
<?php
}
