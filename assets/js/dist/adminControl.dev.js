"use strict";

jQuery(function ($) {
  $(document).ready(function () {
    // Make sure this runs on in WP admin page for the plugin... since WP is stupid and loads it everywhere
    var findString = 'page=elv-samba-helper-admin-page';

    if (window.location.href.includes(findString)) {
      // Fix admin menus (TODO fix this properly later?)
      (function () {
        var currentlySelectedTab = window['elvSambaHelpercurrentlySelectedTab'];
        var allActiveElements = $('.wp-has-current-submenu');
        allActiveElements.removeClass('wp-has-current-submenu').addClass('wp-not-current-submenu');
        var sambaMenuWrapper = $('#toplevel_page_elv-samba-helper-admin-page');
        var sambaSubmenuWrapper = sambaMenuWrapper.find('.wp-submenu-wrap');
        var sambaSubmenuItems = sambaSubmenuWrapper.find('li:not(.wp-submenu-head)');
        var allSambaMenuItems = sambaMenuWrapper.find('.wp-not-current-submenu');

        if (currentlySelectedTab === 'feeds') {
          sambaSubmenuItems[0].classList.add('current');
        }

        if (currentlySelectedTab === 'analytics') {
          sambaSubmenuItems[1].classList.add('current');
        }

        if (currentlySelectedTab === 'personalization') {
          sambaSubmenuItems[2].classList.add('current');
        }

        allSambaMenuItems.removeClass('wp-not-current-submenu').addClass('wp-has-current-submenu');
        sambaMenuWrapper.removeClass('wp-not-current-submenu').addClass('wp-has-current-submenu');
        $('.sambaToRemove').remove();
      })(); // Regenerates the password hash


      var regeneratePasswordHash = function regeneratePasswordHash() {
        var triggerButton = $('.js-regeneratePasswordHash');
        triggerButton.on('click', function (e) {
          e.preventDefault();
          triggerButton = $(this); // Kill the script if the button is disabled and user somehow still clicked it

          if (triggerButton.attr('disabled')) {
            return;
          }

          if (confirm(elvSambaHelperTranslations.sambaIDRegenerateConfirm)) {
            // Disable the button
            triggerButton.attr('disabled', 'disabled');
            triggerButton.removeClass('button-primary'); // Run first AJAX call

            regeneratePasswordHash_AJAX();
          }
        }); // Regenerate the password hash - AJAX

        var regeneratePasswordHash_AJAX = function regeneratePasswordHash_AJAX() {
          $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
              action: 'regeneratePasswordHash'
            },
            success: function success(response) {
              triggerButton.removeAttr("disabled");
              triggerButton.addClass('button-primary');
              alert(elvSambaHelperTranslations.successRegeneratedAccessPassword);
              location.reload();
            },
            error: function error(request, status, _error) {
              console.log(request.responseText);
              console.log(status);
              console.log(_error);
              triggerButton.removeAttr("disabled");
              triggerButton.addClass('button-primary');
              alert(elvSambaHelperTranslations.generalError);
            }
          });
        };
      };

      regeneratePasswordHash(); // Set the trial mode

      var setTrialMode = function setTrialMode() {
        var triggerInput = $('.js-setTrialMode');
        triggerInput.on('change', function (e) {
          triggerInput.attr('disabled', 'disabled');
          var triggerValue = triggerInput.is(":checked") ? 'checked' : 'non-checked';
          setTrialMode_AJAX(triggerValue);
        }); // Set the trial mode - AJAX

        var setTrialMode_AJAX = function setTrialMode_AJAX(triggerValue) {
          $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
              action: 'setTrialMode',
              ajax_mode: true,
              value: triggerValue
            },
            success: function success(response) {},
            error: function error(request, status, _error2) {
              console.log(request.responseText);
              console.log(status);
              console.log(_error2);
              alert(elvSambaHelperTranslations.generalError);
            },
            complete: function complete() {
              var triggerInput = $('.js-setTrialMode');
              triggerInput.removeAttr("disabled");
            }
          });
        };
      };

      setTrialMode(); // Set analytics user ID

      var setUserAnalyticsId = function setUserAnalyticsId() {
        var triggerInput = $('.js-saveUserID-trigger');
        var valueInput = $('.js-saveUserID-value');
        triggerInput.on('click', function (e) {
          triggerInput.attr('disabled', 'disabled');
          var triggerValue = String(valueInput.val());
          setUserAnalyticsId_AJAX(triggerValue);
        }); // Set the trial mode - AJAX

        var setUserAnalyticsId_AJAX = function setUserAnalyticsId_AJAX(triggerValue) {
          $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
              action: 'setSambaUserAnalyticsId',
              ajax_mode: true,
              value: triggerValue
            },
            success: function success(response) {
              alert(elvSambaHelperTranslations.successUserIdSave);
            },
            error: function error(request, status, _error3) {
              console.log(request.responseText);
              console.log(status);
              console.log(_error3);
              alert(elvSambaHelperTranslations.generalError);
            },
            complete: function complete() {
              var triggerInput = $('.js-saveUserID-trigger');
              triggerInput.removeAttr("disabled");
            }
          });
        };
      };

      setUserAnalyticsId();
      /**********************************************/

      /************* WIDGETS MANAGEMENT *************/

      /**********************************************/
      // Delete admin widget 

      var deleteAdminWidget = function deleteAdminWidget() {
        var tableWrapper = $('.js-widgetList, .js-singleWidget');
        tableWrapper.on('click', '.js-deleteWidget-trigger', function (e) {
          if (confirm(elvSambaHelperTranslations.confirmShortcodeDeletion)) {
            var triggerInput = $(this);
            triggerInput.attr('disabled', 'disabled');
            var wrapper = triggerInput.closest('.js-widgetLine, .js-singleWidget');
            var toDeleteID = wrapper.data('id');
            deleteAdminWidget_AJAX(toDeleteID, wrapper, triggerInput);
          }
        }); // Delete admin widget - AJAX

        var deleteAdminWidget_AJAX = function deleteAdminWidget_AJAX(toDeleteID, wrapper, triggerInput) {
          $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
              action: 'deleteWidget',
              ajax_mode: true,
              value: toDeleteID
            },
            success: function success(response) {
              if (wrapper.length > 0) {
                wrapper.remove();
              }

              if (window.location.href.indexOf("widgettabid") != -1) {
                window.location.href = window.location.href.replace("&widgettabid=" + toDeleteID, "");
              }
            },
            error: function error(request, status, _error4) {
              console.log(request.responseText);
              console.log(status);
              console.log(_error4);
              alert(elvSambaHelperTranslations.generalError);
            },
            complete: function complete() {
              triggerInput.removeAttr("disabled");
            }
          });
        };
      };

      deleteAdminWidget(); // Creates new admin widget 

      var createAdminWidget = function createAdminWidget() {
        var triggerInput = $('.js-createWidget-trigger');
        triggerInput.on('click', function (e) {
          triggerInput.attr('disabled', 'disabled');
          var wrapper = $('.js-widgetList');
          createAdminWidget_AJAX(wrapper, triggerInput);
        }); // Creates new admin widget  - AJAX

        var createAdminWidget_AJAX = function createAdminWidget_AJAX(wrapper, triggerInput) {
          $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
              action: 'createWidget',
              ajax_mode: true
            },
            success: function success(response) {
              wrapper.append("<tr class=\"elv_sambaHelper_widgetLine js-widgetLine\" data-id=\"".concat(response, "\">\n              <td>\n              </td>\n              <td>\n                <input type=\"text\" readonly value='[shwidget id=\"").concat(response, "\"]'>\n              </td>\n              <td class=\"elv_sambaHelper_widgetControlTd\">\n                 <a href=\"").concat(location.href, "&widgettabid=").concat(response, "\">\n                   <button class=\"button button-primary button-medium\">").concat(elvSambaHelperTranslations.shortcodeDynamicEdit, "</button>\n                 </a>\n              </td>\n              <td class=\"elv_sambaHelper_widgetControlTd\">\n                <button class=\"button button-secondary button-medium js-deleteWidget-trigger\">").concat(elvSambaHelperTranslations.shortcodeDynamicRemove, "</button>\n              </td>\n            </tr>"));
            },
            error: function error(request, status, _error5) {
              console.log(request.responseText);
              console.log(status);
              console.log(_error5);
              alert(elvSambaHelperTranslations.generalError);
            },
            complete: function complete() {
              triggerInput.removeAttr("disabled");
            }
          });
        };
      };

      createAdminWidget(); // Update current admin widget

      var updateAdminWidget = function updateAdminWidget() {
        var triggerInput = $('.js-updateWidgetSingle-trigger');
        var wrapper = $('.js-singleWidget');
        triggerInput.on('click', function (e) {
          triggerInput.attr('disabled', 'disabled');
          updateAdminWidget_AJAX(wrapper, triggerInput);
        }); // Creates new admin widget  - AJAX

        var updateAdminWidget_AJAX = function updateAdminWidget_AJAX(wrapper, triggerInput) {
          console.log(wrapper.data('id'));
          console.log(wrapper.find('.js-widgetUpdateName').val());
          console.log(wrapper.find('.js-widgetUpdateContent').val());
          $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
              action: 'updateWidget',
              ajax_mode: true,
              widget_id: wrapper.data('id'),
              name: wrapper.find('.js-widgetUpdateName').val(),
              content: wrapper.find('.js-widgetUpdateContent').val()
            },
            success: function success(response) {
              alert(elvSambaHelperTranslations.shortcodeSuccessUpdate);
            },
            error: function error(request, status, _error6) {
              console.log(request.responseText);
              console.log(status);
              console.log(_error6);
              alert(elvSambaHelperTranslations.generalError);
            },
            complete: function complete() {
              triggerInput.removeAttr("disabled");
            }
          });
        };
      };

      updateAdminWidget();
      /*****************************************/

      /************ XML GENERATION *************/

      /*****************************************/

      var exportReports = {
        products: false,
        productCategories: false,
        orders: false,
        customers: false
      };
      var reportButtonElement = $('.js-reportButtonText'); // XML generation - Products

      var XMLGeneration_products = function XMLGeneration_products() {
        reportButtonElement.text(elvSambaHelperTranslations.generatingProducts);
        $.ajax({
          type: 'POST',
          url: ajaxurl,
          data: {
            action: 'generateXML_products'
          },
          success: function success() {
            exportReports.products = true;
          },
          error: function error(request, status, _error7) {
            console.log(request.responseText);
            console.log(status);
            console.log(_error7);
          },
          complete: function complete() {
            XMLGeneration_productCategories();
          }
        });
      }; // XML generation - Products categories


      var XMLGeneration_productCategories = function XMLGeneration_productCategories() {
        XMLGeneration_customers();
        /*  reportButtonElement.text(elvSambaHelperTranslations.generatingProductCategories)
         $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
               action: 'generateXML_productsCategories',
            },
            success: function () {
               exportReports.productCategories = true
            },
            error: function (request, status, error) {
               console.log(request.responseText)
               console.log(status)
               console.log(error)
            },
            complete: function () {
               XMLGeneration_customers()
            }
         }) */
      }; // XML generation - Customers


      var XMLGeneration_customers = function XMLGeneration_customers() {
        reportButtonElement.text(elvSambaHelperTranslations.generatingCustomers);
        $.ajax({
          type: 'POST',
          url: ajaxurl,
          data: {
            action: 'generateXML_customers'
          },
          success: function success() {
            exportReports.customers = true;
          },
          error: function error(request, status, _error8) {
            console.log(request.responseText);
            console.log(status);
            console.log(_error8);
          },
          complete: function complete() {
            XMLGeneration_orders();
          }
        });
      }; // XML generation - Orders


      var XMLGeneration_orders = function XMLGeneration_orders() {
        reportButtonElement.text(elvSambaHelperTranslations.generatingOrders);
        $.ajax({
          type: 'POST',
          url: ajaxurl,
          data: {
            action: 'generateXML_orders'
          },
          success: function success() {
            exportReports.orders = true;
          },
          error: function error(request, status, _error9) {
            console.log(request.responseText);
            console.log(status);
            console.log(_error9);
          },
          complete: function complete() {
            XMLGeneration_finish();
          }
        });
      }; // XML generation - Finish report


      var XMLGeneration_finish = function XMLGeneration_finish() {
        var triggerButton = $('.js-regenerateFeedXML');
        triggerButton.removeAttr("disabled");
        triggerButton.addClass('button-primary');
        reportButtonElement.text('');
        var productMessage = "".concat(elvSambaHelperTranslations.reportProducts, " ").concat(exportReports.products ? elvSambaHelperTranslations.feedSuccess : elvSambaHelperTranslations.feedFailure); //const productCategoriesMessage = `${elvSambaHelperTranslations.reportProductCategories} ${(exportReports.productCategories) ? elvSambaHelperTranslations.feedSuccess : elvSambaHelperTranslations.feedFailure}`

        var customersMessage = "".concat(elvSambaHelperTranslations.reportCustomers, " ").concat(exportReports.customers ? elvSambaHelperTranslations.feedSuccess : elvSambaHelperTranslations.feedFailure);
        var ordersMessage = "".concat(elvSambaHelperTranslations.reportOrders, " ").concat(exportReports.orders ? elvSambaHelperTranslations.feedSuccess : elvSambaHelperTranslations.feedFailure);
        alert(elvSambaHelperTranslations.feedGenerationDone + ' \n\n' + productMessage + '\n' + customersMessage + '\n' + ordersMessage);
      };
      /**
       * Due to how jQuery processes AJAX and for support of older browsers, we need to create a series of self-calling AJAX calls that trigger at the end of each other instead of using normal "await" and "async" syntax.
       * 
       * The calls trigger in the following order:
       * 1. XMLGeneration_products
       * 2. XMLGeneration_productsCategories
       * 3. XMLGeneration_customers
       * 4. XMLGeneration_orders
       * 
       * The script then closes off with "XMLGeneration_finish" function afterwards (this one is called from "XMLGeneration_orders")
       */


      var XMLGenerationTrigger = function XMLGenerationTrigger() {
        var triggerButton = $('.js-regenerateFeedXML');
        triggerButton.on('click', function (e) {
          e.preventDefault();
          triggerButton = $(this); // Kill the script if the button is disabled and user somehow still clicked it

          if (triggerButton.attr('disabled')) {
            return;
          } // Disable the button


          triggerButton.attr('disabled', 'disabled');
          triggerButton.removeClass('button-primary'); // Reset the export report status

          exportReports.products = false;
          exportReports.productCategories = false;
          exportReports.orders = false;
          exportReports.customers = false; // Run first AJAX call

          XMLGeneration_products();
        });
      };

      XMLGenerationTrigger();
    }
  });
});