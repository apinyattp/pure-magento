<?php
namespace Perspective\Kbankpayment\Gateway\Response;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;

class PaymentDetailsHandler implements HandlerInterface
{

    /**
     * @inheritdoc
     */
    public function handle(array $handlingSubject, array $response) {
        $payment = SubjectReader::readPayment($handlingSubject);
        $payment = $payment->getPayment();

        $payment->setAdditionalInformation('charge_id', $response['data']['id']);
        $payment->setAdditionalInformation('charge_authen_url', $response['data']['redirect_url']);
        
        $payment->setAdditionalInformation('charge_status', $response['data']['status']);
        $payment->setAdditionalInformation('transaction_state', $response['data']['transaction_state']);
        $payment->setAdditionalInformation('charge_data', $response['data']);
    }
}
