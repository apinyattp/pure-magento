define([
    'jquery',
    'mage/translate',
    'bootbox',
    'bootstrap-datepicker',
], function($, $t, bootbox) {
    "use strict";
	
    var $form = null;
    
    function InformPayment(config, element) {
        $form = $('#frmInformPayment');
		$form.on('submit', onSubmit);
        
        $('#ebt_date').datepicker({
            format: 'yyyy-mm-dd',
            endDate: '0d',
            autoclose: true
        });
        
        $('.submit', $form).attr('type', 'submit');
	}
    
    function onSubmit(e) {
        
        e.preventDefault();
        
        if ($form.hasClass('saving')) {
            return;
        }
        
        //Validate field
        //Bank account
        if ($('input[name="bank_account_id"]:checked', $form).length != 1) {
            alert($t('Please choose the bank account'));
            $($('input[name="bank_account_id"]', $form)[0]).focus();
            return;
        }
        
        //Order ID or number
        if ($('input[name="order_id"]:checked', $form).length != 1 && !$('input[name="order_number"]', $form).val()) {
            alert($t('Please choose the order to be paid'));
            $('input[name="order_id"]:first-child', $form).focus();
            return;
        }
        
        //Ajax submit
        $form.addClass('saving');
        jQuery('button[type="submit"]', $form).prop('disabled', true);
        
        var formData = new FormData(this);
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json'
        }).done(onSubmitDone).fail(onSubmitFail);
    }
    
    function onSubmitDone(resp) {
        if (!resp.success) {
            alert(resp.message || $t('The payment details could not be saved.'));
            $form.removeClass('saving');
            jQuery('button[type="submit"]', $form).prop('disabled', false);
            return;
        }
        var orderConfirmText = `Your payment order: ${resp.data.orderNumber} has been confirmed.`;
        //$form.parent().prepend('<div class="alert alert-success" role="alert">' + $t('We have received your payment details. Thank you.') + '</div>');
        $form.remove();
        $('#payment-title').remove();
        $('#btnMobileCategory').remove();

        $('#content').prepend(`
        <div class="col-xs-12 text-center">
            <i class="far fa-check-circle"
               style="color: #d4d4d4;font-size: 48px;">
            </i>
        </div>
        <div class="col-xs-12 text-center" style="margin: 30px 0;">
            <span class="font-good-vibes secondary-color-1 text-size-42">
                ${$t('Confirm Payment Success')}
            </span>
        </div>
        <div class="col-xs-12 text-center">
            <p style="margin: 0;">
                ${$t(orderConfirmText)}
            </p>
            <p>
                ${$t('Thank you for shopping with us')}
            </p>
        </div>
        
        <div class="col-xs-12 text-center" style="margin-top: 20px;">
            <button class="btn btn-primary font-secondary w-xs-100 continue-shopping">
            ${$t('Continue Shopping')}
            </button>
        </div>
        `);
    }
    
    function onSubmitFail() {
        $form.removeClass('saving');
        jQuery('button[type="submit"]', $form).prop('disabled', false);
        
    }
	
	return InformPayment;
	
});