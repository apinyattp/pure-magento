<?php
namespace Perspective\Kbankpayment\Gateway\Request;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Perspective\Kbankpayment\Observer\Direct18DataAssignObserver;

class PaymentCcTokenBuilder implements BuilderInterface
{
    /**
     * @var string
     */
    const SOURCE_TYPE = 'source_type';

    /**
     * @var string
     */
    const MODE = 'mode';

    /**
     * @var string
     */
    const TOKEN = 'token';

    /**
     * @param  array $buildSubject
     *
     * @return array
     */
    public function build(array $buildSubject) {
        $payment = SubjectReader::readPayment($buildSubject);
        $method  = $payment->getPayment();

        return [
            self::SOURCE_TYPE   => 'card',
            self::MODE          => 'token',
            self::TOKEN         => $method->getAdditionalInformation(Direct18DataAssignObserver::DIRECT18_CARD_TOKEN),
        ];
    }

}
