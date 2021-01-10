define(
    [
        'ko',
        'Magento_Payment/js/view/payment/cc-form',
        'mage/storage',
        'mage/translate',
        'jquery',
        'Magento_Payment/js/model/credit-card-validation/validator',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/action/redirect-on-success',
        'Magento_Checkout/js/model/quote',
        'mage/url',
    ],
    function (
        ko,
        Component,
        storage,
        $t,
        $,
        validator,
        errorProcessor,
        fullScreenLoader,
        redirectOnSuccessAction,
        quote,
        urlBuilder,
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Perspective_Kbankpayment/payment/kbankpayment-redirect-form'
            },

            redirectAfterPlaceOrder: true,

            isPlaceOrderActionAllowed: ko.observable(quote.billingAddress() != null),

            /**
             * Get payment method code
             *
             * @return {string}
             */
            getCode: function() {
                return 'kbankpayment_redirect';
            },

            /**
             * Is method available to display
             *
             * @return {boolean}
             */
            isActive: function() {
                return true;
            },

            /**
             * Start performing place order action,
             * by disable a place order button and show full screen loader component.
             */
            startPerformingPlaceOrderAction: function() {
                this.isPlaceOrderActionAllowed(false);
                fullScreenLoader.startLoader();
            },

            /**
             * Stop performing place order action,
             * by disable a place order button and show full screen loader component.
             */
            stopPerformingPlaceOrderAction: function() {
                fullScreenLoader.stopLoader();
                this.isPlaceOrderActionAllowed(true);
            },

            placeOrderAndRedirect: function(data) {
                var self = this;

                this.startPerformingPlaceOrderAction();

                self.getPlaceOrderDeferredObject()
                    .fail(
                        function(response) {
                            errorProcessor.process(response, self.messageContainer);
                            fullScreenLoader.stopLoader();
                            self.isPlaceOrderActionAllowed(true);
                        }
                    ).done(
                        function(response) {
                            var url = urlBuilder.build("kbankpayment/redirect/form?order_id="+response);
                            window.location.href = url;
                        }
                    );
            },

            /**
             * Hook the placeOrder function.
             * Original source: placeOrder(data, event); @ module-checkout/view/frontend/web/js/view/payment/default.js
             *
             * @return {boolean}
             */
            placeOrder: function(data, event) {
                if (event) {
                    event.preventDefault();
                }

                this.placeOrderAndRedirect(data);

                return true;
            },
        });
    }
);
