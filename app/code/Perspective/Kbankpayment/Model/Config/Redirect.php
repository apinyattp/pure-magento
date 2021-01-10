<?php
namespace Perspective\Kbankpayment\Model\Config;

use Perspective\Kbankpayment\Model\Config\Config;

class Redirect extends Config
{
    const CODE = 'kbankpayment_redirect';

    var $BACK_URI = 'kbankpayment/redirect/back';
    var $RESP_URI = 'kbankpayment/redirect/response';
    var $TEST_URL = 'https://uatkpgw.kasikornbank.com/mobilepay/payment.aspx';
    var $LIVE_URL = 'https://rt05.kasikornbank.com/pgpayment/payment.aspx';

    public function getServiceID() {
        return $this->SERVICE_ID;
    }

    /**
     * Retrieve KBank merchant ID whether live or test key
     *
     * @return string
     */
    public function getSecret() {

        if ($this->isSandboxEnabled()) {
            return $this->getTestSecret();
        }
        return $this->getLiveSecret();
    }

    /**
     * Retrieve KBank live merchant ID
     *
     * @return string
     */
    protected function getLiveSecret() {
        return $this->getValue('live_secret');
    }

    /**
     * Retrieve KBank test merchant ID
     *
     * @return string
     */
    protected function getTestSecret() {
        return $this->getValue('test_secret');
    }

}
