<?php
namespace Perspective\Kbankpayment\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;

class Direct18DataAssignObserver extends AbstractDataAssignObserver
{
    /**
     * @var string
     */
    const DIRECT18_CARD_TOKEN = 'direct18_card_token';

    /**
     * @var array
     */
    protected $additionalInformationList = [
        self::DIRECT18_CARD_TOKEN
    ];

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(Observer $observer) {
        $dataObject = $this->readDataArgument($observer);

        $additionalData = $dataObject->getData(PaymentInterface::KEY_ADDITIONAL_DATA);

        if (!is_array($additionalData)) {
            return;
        }

        $paymentInfo = $this->readPaymentModelArgument($observer);

        $paymentInfo->setDirect18CardToken($additionalData[self::DIRECT18_CARD_TOKEN]);

        foreach ($this->additionalInformationList as $additionalInformationKey) {
            if (isset($additionalData[$additionalInformationKey])) {
                $paymentInfo->setAdditionalInformation(
                    $additionalInformationKey,
                    $additionalData[$additionalInformationKey]
                );
            }
        }
    }

}
