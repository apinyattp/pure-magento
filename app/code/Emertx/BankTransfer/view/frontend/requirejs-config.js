var config = {
    paths: {
        'bootstrap': 'Emertx_BankTransfer/js/bootstrap.min',
        'bootstrap-datepicker': 'Emertx_BankTransfer/js/bootstrap-datepicker.min',
        'bootbox': 'Emertx_BankTransfer/js/bootbox.min'
    },
    shim: {
        'bootstrap-datepicker': {
            deps: [
                'bootstrap'
            ]
        },
        'bootbox': {
            deps: [
                'bootstrap'
            ]
        }
    },
    map: {
        '*': {
            'Magento_OfflinePayments/js/view/payment/method-renderer/banktransfer-method': 'Emertx_BankTransfer/js/banktransfer-method'
        }
    }
};