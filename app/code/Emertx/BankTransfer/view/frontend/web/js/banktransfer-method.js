/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'ko',
        'Magento_Checkout/js/view/payment/default'
    ],
    function (ko, Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Emertx_BankTransfer/banktransfer'
            },
            /**
             * Get value of instruction field.
             * @returns {String}
             */
            getInstructions: function () {
                return window.checkoutConfig.payment.instructions[this.item.method];
            },
            
            
            hasBankAccounts: function() {
                return ('bankAcc' in window.checkoutConfig && window.checkoutConfig.bankAcc && window.checkoutConfig.bankAcc.length > 0);
            },
            /**
             * Get list of bank accounts
             * @returns {String}
             */
            getBankAccounts: function() {
                if (this.hasBankAccounts()) {
                    return window.checkoutConfig.bankAcc;
                }
                return [];
            }
        });
    }
);
