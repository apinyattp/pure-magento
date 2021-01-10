<?php
namespace Perspective\Kbankpayment\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class KbankHelper extends AbstractHelper
{

    /**
     * @param  string $fieldId
     *
     * @return string
     */
    public function getConfig($fieldId) {
        $path = 'payment/omise/' . $fieldId;

        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param  string  $currency
     * @param  integer $amount
     *
     * @return string
     */
    public function direct18AmountFormat($currency, $amount) {
        switch (strtoupper($currency)) {
            case 'THB':
                // Convert to a small unit
                break;
        }

        return $amount;
    }

}
