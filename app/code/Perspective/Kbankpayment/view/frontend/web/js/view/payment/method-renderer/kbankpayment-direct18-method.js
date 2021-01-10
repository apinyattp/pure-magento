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
                template: 'Perspective_Kbankpayment/payment/kbankpayment-direct18-form'
            },

            redirectAfterPlaceOrder: true,

            isPlaceOrderActionAllowed: ko.observable(quote.billingAddress() != null),

            /**
             * Get payment method code
             *
             * @return {string}
             */
            getCode: function() {
                return 'kbankpayment_direct18';
            },

            /**
             * Get a checkout form data
             *
             * @return {Object}
             */
            getData: function() {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'direct18_card_token': this.direct18CardToken()
                    }
                };
            },

            /**
             * Get Direct18 public key
             *
             * @return {string}
             */
            getPublicKey: function() {
                return window.checkoutConfig.payment.kbankpayment_direct18.public;
            },

            /**
             * Get Direct18 public key
             *
             * @return {string}
             */
            getURL: function() {
                return window.checkoutConfig.payment.kbankpayment_direct18.url;
            },

            /**
             * Initiate observable fields
             *
             * @return this
             */
            initObservable: function() {
                this._super()
                    .observe([
                        'direct18CardNumber',
                        'direct18CardHolderName',
                        'direct18CardExpirationMonth',
                        'direct18CardExpirationYear',
                        'direct18CardSecurityCode',
                        'direct18CardToken'
                    ]);

                return this;
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

            /**
             * Generate token before proceed the placeOrder process.
             *
             * @return {void}
             */
            generateTokenAndPerformPlaceOrderAction: function(data) {
                this.startPerformingPlaceOrderAction();
                var self = this;

                var serviceUrl = urlBuilder.build('rest/V1/direct18/token');

                var card = {
                    "card" : {
                        "name"      : this.direct18CardHolderName(),
                        "number"    : this.direct18CardNumber(),
                        "month"     : this.direct18CardExpirationMonth(),
                        "year"      : this.direct18CardExpirationYear(),
                        "cvv"       : this.direct18CardSecurityCode()
                    }
                };
                storage.post(serviceUrl, JSON.stringify(card))
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
                            self.direct18CardToken(response);
                            self.beginPlaceOrder();
                        }
                    );
            },

            beginPlaceOrder: function() {
                var self = this;
                this.getPlaceOrderDeferredObject()
                    .fail(
                        function(response) {
                            errorProcessor.process(response, self.messageContainer);
                            fullScreenLoader.stopLoader();
                            self.isPlaceOrderActionAllowed(true);
                        }
                    ).done(self.getRedirectURL);
            },

            getRedirectURL: function (order_id){
                var serviceUrl = urlBuilder.build('rest/V1/direct18/url/'+order_id);

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
                            if (response) {
                                // console.log(response);
                                $.mage.redirect(response);
                            } else {
                                errorProcessor.process(response, self.messageContainer);
                                fullScreenLoader.stopLoader();
                                self.isPlaceOrderActionAllowed(true);
                            }
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

                if (! this.validate()) {
                    return false;
                }

                this.generateTokenAndPerformPlaceOrderAction(data);

                return true;
            },

            /**
             * Hook the validate function.
             * Original source: validate(); @ module-checkout/view/frontend/web/js/view/payment/default.js
             *
             * @return {boolean}
             */
            validate: function () {
                $('#' + this.getCode() + 'Form').validation();

                var isCardNumberValid          = $('#' + this.getCode() + 'CardNumber').valid();
                var isCardHolderNameValid      = $('#' + this.getCode() + 'CardHolderName').valid();
                var isCardExpirationMonthValid = $('#' + this.getCode() + 'CardExpirationMonth').valid();
                var isCardExpirationYearValid  = $('#' + this.getCode() + 'CardExpirationYear').valid();
                var isCardSecurityCodeValid    = $('#' + this.getCode() + 'CardSecurityCode').valid();

                if (isCardNumberValid
                    && isCardHolderNameValid
                    && isCardExpirationMonthValid
                    && isCardExpirationYearValid
                    && isCardSecurityCodeValid) {
                    return true;
                }

                return false;
            },

            getCcMonthsValues () {
                return [
                    {"value":1,"month":"01"},
                    {"value":2,"month":"02"},
                    {"value":3,"month":"03"},
                    {"value":4,"month":"04"},
                    {"value":5,"month":"05"},
                    {"value":6,"month":"06"},
                    {"value":7,"month":"07"},
                    {"value":8,"month":"08"},
                    {"value":9,"month":"09"},
                    {"value":10,"month":"10"},
                    {"value":11,"month":"11"},
                    {"value":12,"month":"12"}
                ];
            },

            getCcYearsValues () {
                var years = [];
                var cur = (new Date()).getFullYear();

                for(var year=cur; year <= cur+50; year++){
                    years.push({"value":year,"year":year});
                }
                return years;
            }
        });
    }
);
