var curURL = window.location.href;
var kbankURL = 'https://dev-kpaymentgateway.kasikornbank.com';
if(curURL.search("carelicious.shop") > 0){
    kbankURL = 'https://kpaymentgateway.kasikornbank.com';
}

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
        kbankURL+'/ui/v2/kpayment.min.js'
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
        kbankURL1
    ) {
        'use strict';
        
        

        return Component.extend({
            defaults: {
                template: 'Perspective_Kbankpayment/payment/kbankpayment-uibuttonterm-form'
            },

            redirectAfterPlaceOrder: true,
            order_id: 0,
            token: "",

            isPlaceOrderActionAllowed: ko.observable(quote.billingAddress() != null),

            /**
             * Get payment method code
             *
             * @return {string}
             */
            getCode: function() {
                return 'kbankpayment_uibuttonterm';
            },


            getPublicKey: function() {
                return window.checkoutConfig.payment.kbankpayment_uibuttonterm.public;
            },

            /**
             * Is method available to display
             *
             * @return {boolean}
             */
            isActive: function() {
                return true;
            },

            getKbankURL: function(){
                return kbankURL;
            },

            buttonInitial: function (orderData) {
                var self = this;
                var d = kbankURL,
                    s = document.querySelector('script[src^="' + d + '"]');
                document.getElementById('Kbank_button').appendChild(s);
                KPayment.create();
// console.log(KPayment)
console.log(window.checkoutConfig.payment.kbankpayment_uibuttonterm)
                KPayment.setPublickey(this.getPublicKey());
                KPayment.setAmount(orderData.amount);
                KPayment.setRefNumber(orderData.order_increment_id);
                KPayment.setOrderId(orderData.order_increment_id);
                KPayment.setMid(orderData.mid);
                KPayment.setSmartpayId("0002");
                KPayment.setTerm(10);
                
                KPayment.setCurrency("THB");
                KPayment.setName("The Next Optical");
                KPayment.setPaymentMethods("card");
                KPayment.onClose(this.kpaymentClose);
                KPayment.show();

                jQuery('body').on('DOMNodeInserted', 'button', function () {
                    if(document.getElementsByName("token").length){
                        self.chargeKbank();
                    }
                });
            },
            chargeKbank: function (event) {
                if(this.token.length > 1){
                    return false;
                }

                var tokenData = document.getElementsByName("token")[0].value;
                this.token = tokenData;

                var chargeUrl = urlBuilder.build("kbankpayment/uibuttonterm/charge");
                var chargeData = {token:this.token, order_id:this.order_id};

                storage.post(chargeUrl, JSON.stringify(chargeData))
                .fail(
                    function (response) {
                        errorProcessor.process(response, self.messageContainer);
                        fullScreenLoader.stopLoader();
                        self.isPlaceOrderActionAllowed(true);
                    }
                )
                .done(
                    function (response) {
                        if (!response) {
                            errorProcessor.process(response, self.messageContainer);
                            fullScreenLoader.stopLoader();
                            self.isPlaceOrderActionAllowed(true);
                            return;
                        }
                        if(response.redirect_url){
                            window.location.href = response.redirect_url;
                        }else{
                            $.mage.redirect(response.sucess_url);
                        }
                    }
                );

            },

            kpaymentClose: function (){
                fullScreenLoader.stopLoader();
                location.reload(true);
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
                            var serviceUrl = urlBuilder.build("kbankpayment/uibuttonterm/form?order_id="+response);
                            self.order_id = response;

                            storage.get(serviceUrl, false)
                            .fail(
                                function (response) {
                                    errorProcessor.process(response, self.messageContainer);
                                    fullScreenLoader.stopLoader();
                                    self.isPlaceOrderActionAllowed(true);
                                }
                            )
                            .done(
                                function (response) {
                                    if (!response) {
                                        errorProcessor.process(response, self.messageContainer);
                                        fullScreenLoader.stopLoader();
                                        self.isPlaceOrderActionAllowed(true);
                                        return;
                                    }
                                    self.buttonInitial(response);
                                }
                            );
                            
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
