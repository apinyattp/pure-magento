var curURL = window.location.href;
// var kbankURL = 'https://dev-kpaymentgateway.kasikornbank.com';
// if(curURL.search("carelicious.shop") > 0){
//     kbankURL = 'https://kpaymentgateway.kasikornbank.com';
// }
var kbankURL = 'https://kpaymentgateway.kasikornbank.com'

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
                template: 'Perspective_Kbankpayment/payment/kbankpayment-uiqr-form'
            },

            redirectAfterPlaceOrder: true,
            order_id: 0,
            k_order_id: '',

            isPlaceOrderActionAllowed: ko.observable(quote.billingAddress() != null),

            /**
             * Get payment method code
             *
             * @return {string}
             */
            getCode: function() {
                return 'kbankpayment_uiqr';
            },


            getPublicKey: function() {
                return window.checkoutConfig.payment.kbankpayment_uiqr.public;
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
                document.getElementById('Kbank_buttonqr').appendChild(s);
                KPayment.create();

                KPayment.setPublickey(this.getPublicKey());
                KPayment.setAmount(orderData.amount);
                KPayment.setRefNumber(orderData.order_increment_id);
                KPayment.setOrderId(orderData.k_order_id);

                KPayment.setCurrency("THB");
                KPayment.setName("The Next Optical");
                KPayment.setPaymentMethods("qr");

                KPayment.onClose(this.kpaymentClose);
                KPayment.show();

                jQuery('body').on('DOMNodeInserted', 'button', function () {
                    if(document.querySelector('input[name="chargeId"]')){
                        fullScreenLoader.startLoader();
                        var chargeID = document.querySelector('input[name="chargeId"]').value;
                        self.kpaymentInquiry(chargeID);
                    }
                });
            },
            KpaymentOrder: function (orderData){
                var self = this;
                var chargeUrl = urlBuilder.build("kbankpayment/uiqr/Createorder");
                var postData = {order_id:this.order_id};

                storage.post(chargeUrl, JSON.stringify(postData))
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
                        // console.log(response);
                        orderData.k_order_id = response.id;
                        self.buttonInitial(orderData);
                        
                    }
                );
            },
            kpaymentInquiry: function (chargeID){
                var self = this;
                var chargeUrl = urlBuilder.build("kbankpayment/response/qrCallback");
                var postData = {order_id:this.order_id,charge_id:chargeID};

                storage.post(chargeUrl, JSON.stringify(postData))
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
                        console.log(response);

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
                            var serviceUrl = urlBuilder.build("kbankpayment/uiqr/form?order_id="+response);
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
                                    self.KpaymentOrder(response);
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
