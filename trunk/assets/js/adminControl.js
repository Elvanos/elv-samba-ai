jQuery(function ($) {

   $(document).ready(function () {

      // Make sure this runs on in WP admin page for the plugin... since WP is stupid and loads it everywhere
      const findString = 'page=samba-ai-admin-page'
      if (window.location.href.includes(findString)) {

         // Fix admin menus (TODO fix this properly later?)
         (function () {

            const currentlySelectedTab = window['sambaAicurrentlySelectedTab']

            const allActiveElements = $('.wp-has-current-submenu')
            allActiveElements
               .removeClass('wp-has-current-submenu')
               .addClass('wp-not-current-submenu')

            const sambaMenuWrapper = $('#toplevel_page_samba-ai-admin-page')
            const sambaSubmenuWrapper = sambaMenuWrapper.find('.wp-submenu-wrap')
            const sambaSubmenuItems = sambaSubmenuWrapper.find('li:not(.wp-submenu-head)')

            const allSambaMenuItems = sambaMenuWrapper.find('.wp-not-current-submenu')

            if (currentlySelectedTab === 'feeds') {
               sambaSubmenuItems[0].classList.add('current')
            }

            if (currentlySelectedTab === 'analytics') {
               sambaSubmenuItems[1].classList.add('current')
            }

            if (currentlySelectedTab === 'personalization') {
               sambaSubmenuItems[2].classList.add('current')
            }

            allSambaMenuItems
               .removeClass('wp-not-current-submenu')
               .addClass('wp-has-current-submenu')

            sambaMenuWrapper
               .removeClass('wp-not-current-submenu')
               .addClass('wp-has-current-submenu')

            $('.sambaToRemove').remove()

         })()

         // Regenerates the password hash
         const regeneratePasswordHash = function () {

            let triggerButton = $('.js-regeneratePasswordHash')

            triggerButton.on('click', function (e) {

               e.preventDefault()

               triggerButton = $(this)

               // Kill the script if the button is disabled and user somehow still clicked it
               if (triggerButton.attr('disabled')) {
                  return
               }

               if (confirm(sambaAiTranslations.sambaIDRegenerateConfirm)) {
                  // Disable the button
                  triggerButton.attr('disabled', 'disabled')
                  triggerButton.removeClass('button-primary')

                  // Run first AJAX call
                  regeneratePasswordHash_AJAX()
               }
            })

            // Regenerate the password hash - AJAX
            const regeneratePasswordHash_AJAX = function () {
               $.ajax({
                  type: 'POST',
                  url: ajaxurl,
                  data: {
                     action: 'regeneratePasswordHash',
                  },
                  success: function (response) {
                     triggerButton.removeAttr("disabled")
                     triggerButton.addClass('button-primary')
                     alert(sambaAiTranslations.successRegeneratedAccessPassword)
                     location.reload()
                  },
                  error: function (request, status, error) {
                     console.log(request.responseText)
                     console.log(status)
                     console.log(error)
                     triggerButton.removeAttr("disabled")
                     triggerButton.addClass('button-primary')
                     alert(sambaAiTranslations.generalError)
                  }
               })
            }
         }
         regeneratePasswordHash()

         // Set the trial mode
         const setTrialMode = function () {

            let triggerInput = $('.js-setTrialMode')

            triggerInput.on('change', function (e) {

               triggerInput.attr('disabled', 'disabled')

               const triggerValue = triggerInput.is(":checked") ? 'checked' : 'non-checked'

               setTrialMode_AJAX(triggerValue)

            })

            // Set the trial mode - AJAX
            const setTrialMode_AJAX = function (triggerValue) {
               $.ajax({
                  type: 'POST',
                  url: ajaxurl,
                  data: {
                     action: 'setTrialMode',
                     ajax_mode: true,
                     value: triggerValue,
                  },
                  success: function (response) {},
                  error: function (request, status, error) {
                     console.log(request.responseText)
                     console.log(status)
                     console.log(error)
                     alert(sambaAiTranslations.generalError)
                  },
                  complete: function () {
                     let triggerInput = $('.js-setTrialMode')
                     triggerInput.removeAttr("disabled")
                  }
               })
            }
         }
         setTrialMode()

         // Set analytics user ID
         const setUserAnalyticsId = function () {

            let triggerInput = $('.js-saveUserID-trigger')
            let valueInput = $('.js-saveUserID-value')

            triggerInput.on('click', function (e) {

               triggerInput.attr('disabled', 'disabled')

               const triggerValue = String(valueInput.val())

               setUserAnalyticsId_AJAX(triggerValue)

            })

            // Set the trial mode - AJAX
            const setUserAnalyticsId_AJAX = function (triggerValue) {
               $.ajax({
                  type: 'POST',
                  url: ajaxurl,
                  data: {
                     action: 'setSambaUserAnalyticsId',
                     ajax_mode: true,
                     value: triggerValue,
                  },
                  success: function (response) {
                     alert(sambaAiTranslations.successUserIdSave)
                  },
                  error: function (request, status, error) {
                     console.log(request.responseText)
                     console.log(status)
                     console.log(error)
                     alert(sambaAiTranslations.generalError)
                  },
                  complete: function () {
                     let triggerInput = $('.js-saveUserID-trigger')
                     triggerInput.removeAttr("disabled")
                  }
               })
            }
         }
         setUserAnalyticsId()

         /**********************************************/
         /************* WIDGETS MANAGEMENT *************/
         /**********************************************/

         // Delete admin widget 
         const deleteAdminWidget = function () {

            const tableWrapper = $('.js-widgetList, .js-singleWidget')

            tableWrapper.on('click', '.js-deleteWidget-trigger', function (e) {

               if (confirm(sambaAiTranslations.confirmShortcodeDeletion)) {

                  const triggerInput = $(this)
                  triggerInput.attr('disabled', 'disabled')

                  const wrapper = triggerInput.closest('.js-widgetLine, .js-singleWidget')
                  const toDeleteID = wrapper.data('id')

                  deleteAdminWidget_AJAX(toDeleteID, wrapper, triggerInput)
               }


            })

            // Delete admin widget - AJAX
            const deleteAdminWidget_AJAX = function (toDeleteID, wrapper, triggerInput) {

               $.ajax({
                  type: 'POST',
                  url: ajaxurl,
                  data: {
                     action: 'deleteWidget',
                     ajax_mode: true,
                     value: toDeleteID,
                  },
                  success: function (response) {

                     if (wrapper.length > 0) {
                        wrapper.remove()
                     }

                     if (window.location.href.indexOf("widgettabid") != -1) {
                        window.location.href = window.location.href.replace("&widgettabid=" + toDeleteID, "")
                     }
                  },
                  error: function (request, status, error) {
                     console.log(request.responseText)
                     console.log(status)
                     console.log(error)
                     alert(sambaAiTranslations.generalError)
                  },
                  complete: function () {
                     triggerInput.removeAttr("disabled")
                  }
               })
            }
         }
         deleteAdminWidget()

         // Creates new admin widget 
         const createAdminWidget = function () {

            let triggerInput = $('.js-createWidget-trigger')
            triggerInput.on('click', function (e) {

               triggerInput.attr('disabled', 'disabled')

               const wrapper = $('.js-widgetList')

               createAdminWidget_AJAX(wrapper, triggerInput)
            })

            // Creates new admin widget  - AJAX
            const createAdminWidget_AJAX = function (wrapper, triggerInput) {

               $.ajax({
                  type: 'POST',
                  url: ajaxurl,
                  data: {
                     action: 'createWidget',
                     ajax_mode: true
                  },
                  success: function (response) {
                     wrapper.append(`<tr class="sambaAi_widgetLine js-widgetLine" data-id="${response}">
              <td>
              </td>
              <td>
                <input type="text" readonly value='[shwidget id="${response}"]'>
              </td>
              <td class="sambaAi_widgetControlTd">
                 <a href="${location.href}&widgettabid=${response}">
                   <button class="button button-primary button-medium">${sambaAiTranslations.shortcodeDynamicEdit}</button>
                 </a>
              </td>
              <td class="sambaAi_widgetControlTd">
                <button class="button button-secondary button-medium js-deleteWidget-trigger">${sambaAiTranslations.shortcodeDynamicRemove}</button>
              </td>
            </tr>`)
                  },
                  error: function (request, status, error) {
                     console.log(request.responseText)
                     console.log(status)
                     console.log(error)
                     alert(sambaAiTranslations.generalError)
                  },
                  complete: function () {
                     triggerInput.removeAttr("disabled")
                  }
               })
            }
         }
         createAdminWidget()

         // Update current admin widget
         const updateAdminWidget = function () {

            let triggerInput = $('.js-updateWidgetSingle-trigger')
            const wrapper = $('.js-singleWidget')
            triggerInput.on('click', function (e) {

               triggerInput.attr('disabled', 'disabled')


               updateAdminWidget_AJAX(wrapper, triggerInput)
            })

            // Creates new admin widget  - AJAX
            const updateAdminWidget_AJAX = function (wrapper, triggerInput) {

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
                     content: wrapper.find('.js-widgetUpdateContent').val(),
                  },
                  success: function (response) {
                     alert(sambaAiTranslations.shortcodeSuccessUpdate)
                  },
                  error: function (request, status, error) {
                     console.log(request.responseText)
                     console.log(status)
                     console.log(error)
                     alert(sambaAiTranslations.generalError)
                  },
                  complete: function () {
                     triggerInput.removeAttr("disabled")
                  }
               })
            }
         }
         updateAdminWidget()

         /*****************************************/
         /************ XML GENERATION *************/
         /*****************************************/

         const exportReports = {
            products: false,
            productCategories: false,
            orders: false,
            customers: false
         }

         const reportButtonElement = $('.js-reportButtonText')

         // XML generation - Products
         const XMLGeneration_products = function () {
            reportButtonElement.text(sambaAiTranslations.generatingProducts)
            $.ajax({
               type: 'POST',
               url: ajaxurl,
               data: {
                  action: 'generateXML_products',
               },
               success: function () {
                  exportReports.products = true
               },
               error: function (request, status, error) {
                  console.log(request.responseText)
                  console.log(status)
                  console.log(error)
               },
               complete: function () {
                  XMLGeneration_productCategories()
               }
            })
         }

         // XML generation - Products categories
         const XMLGeneration_productCategories = function () {
            XMLGeneration_customers()
            /*  reportButtonElement.text(sambaAiTranslations.generatingProductCategories)
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
         }

         // XML generation - Customers
         const XMLGeneration_customers = function () {
            reportButtonElement.text(sambaAiTranslations.generatingCustomers)
            $.ajax({
               type: 'POST',
               url: ajaxurl,
               data: {
                  action: 'generateXML_customers',
               },
               success: function () {
                  exportReports.customers = true
               },
               error: function (request, status, error) {
                  console.log(request.responseText)
                  console.log(status)
                  console.log(error)
               },
               complete: function () {
                  XMLGeneration_orders()
               }
            })
         }

         // XML generation - Orders
         const XMLGeneration_orders = function () {
            reportButtonElement.text(sambaAiTranslations.generatingOrders)
            $.ajax({
               type: 'POST',
               url: ajaxurl,
               data: {
                  action: 'generateXML_orders',
               },
               success: function () {
                  exportReports.orders = true
               },
               error: function (request, status, error) {
                  console.log(request.responseText)
                  console.log(status)
                  console.log(error)
               },
               complete: function () {
                  XMLGeneration_finish()
               }
            })
         }

         // XML generation - Finish report
         const XMLGeneration_finish = function () {
            let triggerButton = $('.js-regenerateFeedXML')

            triggerButton.removeAttr("disabled")
            triggerButton.addClass('button-primary')

            reportButtonElement.text('')

            const productMessage = `${sambaAiTranslations.reportProducts} ${(exportReports.products) ? sambaAiTranslations.feedSuccess : sambaAiTranslations.feedFailure}`
            //const productCategoriesMessage = `${sambaAiTranslations.reportProductCategories} ${(exportReports.productCategories) ? sambaAiTranslations.feedSuccess : sambaAiTranslations.feedFailure}`
            const customersMessage = `${sambaAiTranslations.reportCustomers} ${(exportReports.customers) ? sambaAiTranslations.feedSuccess : sambaAiTranslations.feedFailure}`
            const ordersMessage = `${sambaAiTranslations.reportOrders} ${(exportReports.orders) ? sambaAiTranslations.feedSuccess : sambaAiTranslations.feedFailure}`

            alert(sambaAiTranslations.feedGenerationDone + ' \n\n' + productMessage + '\n' + customersMessage + '\n' + ordersMessage)

         }

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
         const XMLGenerationTrigger = function () {
            let triggerButton = $('.js-regenerateFeedXML')

            triggerButton.on('click', function (e) {

               e.preventDefault()

               triggerButton = $(this)

               // Kill the script if the button is disabled and user somehow still clicked it
               if (triggerButton.attr('disabled')) {
                  return
               }

               // Disable the button
               triggerButton.attr('disabled', 'disabled')
               triggerButton.removeClass('button-primary')

               // Reset the export report status
               exportReports.products = false
               exportReports.productCategories = false
               exportReports.orders = false
               exportReports.customers = false

               // Run first AJAX call
               XMLGeneration_products()
            })
         }

         XMLGenerationTrigger()
      }

   })

})