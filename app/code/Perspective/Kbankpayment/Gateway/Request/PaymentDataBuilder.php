<?php
namespace Perspective\Kbankpayment\Gateway\Request;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Perspective\Kbankpayment\Helper\KbankHelper;

class PaymentDataBuilder implements BuilderInterface
{
    /**
     * @var string
     */
    const AMOUNT = 'amount';

    /**
     * @var string
     */
    const CURRENCY = 'currency';

    /**
     * @var string
     */
    const DESCRIPTION = 'description';

    /**
     * @var string
     */
    const REFERENCE_ORDER = 'reference_order';

    /**
     * @param \Perspective\Kbankpayment\Helper\KbankHelper $kbankHelper
     */
    public function __construct(KbankHelper $kbankHelper) {
        $this->kbankHelper = $kbankHelper;
    }

    /**
     * @param  array $buildSubject
     *
     * @return array
     */
    public function build(array $buildSubject) {
        $payment = SubjectReader::readPayment($buildSubject);
        $order   = $payment->getOrder();

        $store_name = '';
        
        // $store_name = $this->_scopeConfig->getValue(
        //     'general/store_information/name',
        //     \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        // );

        // if(strlen($store_name) > 1){
        //     $store_name .= ' ';
        // }

        return [
            self::AMOUNT            => $this->kbankHelper->direct18AmountFormat($order->getCurrencyCode(), $order->getGrandTotalAmount()),
            self::REFERENCE_ORDER   => $order->getOrderIncrementId(),
            self::CURRENCY          => $order->getCurrencyCode(),
            self::DESCRIPTION       => $store_name.'Order id ' . $order->getOrderIncrementId(),
        ];
    }

}
