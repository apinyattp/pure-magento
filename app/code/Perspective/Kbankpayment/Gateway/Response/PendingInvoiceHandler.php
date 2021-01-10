<?php
namespace Perspective\Kbankpayment\Gateway\Response;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;

class PendingInvoiceHandler implements HandlerInterface
{
    /**
     * @inheritdoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        /** @var \Magento\Payment\Gateway\Data\PaymentDataObjectInterface **/
        $payment = SubjectReader::readPayment($handlingSubject);
        $g_payment = $payment->getPayment();

        $transaction_state = $g_payment->getAdditionalInformation('transaction_state');
        $charge_status = $g_payment->getAdditionalInformation('charge_status');

        // if($transaction_state == 'Authorized' && $charge_status == 'success'){
        //     $invoice = $payment->getPayment()->getOrder()->prepareInvoice();
        //     $invoice->register();

        //     $payment->getPayment()->getOrder()->addRelatedObject($invoice);
        // }
    }
}
