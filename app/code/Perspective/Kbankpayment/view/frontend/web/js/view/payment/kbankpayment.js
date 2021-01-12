define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';

        rendererList.push(
            {
                type: 'kbankpayment_redirect',
                component: 'Perspective_Kbankpayment/js/view/payment/method-renderer/kbankpayment-redirect-method'
            }
        );

        rendererList.push(
            {
                type: 'kbankpayment_direct18',
                component: 'Perspective_Kbankpayment/js/view/payment/method-renderer/kbankpayment-direct18-method'
            }
        );

        rendererList.push(
            {
                type: 'kbankpayment_uibutton',
                component: 'Perspective_Kbankpayment/js/view/payment/method-renderer/kbankpayment-uibutton-method'
            }
        );

        rendererList.push(
            {
                type: 'kbankpayment_uibuttonterm',
                component: 'Perspective_Kbankpayment/js/view/payment/method-renderer/kbankpayment-uibutton-term-method'
            }
        );

        rendererList.push(
            {
                type: 'kbankpayment_uiqr',
                component: 'Perspective_Kbankpayment/js/view/payment/method-renderer/kbankpayment-uiqr-method'
            }
        );

        return Component.extend({});
    }
);
