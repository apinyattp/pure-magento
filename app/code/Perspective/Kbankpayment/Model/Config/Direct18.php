<?php
namespace Perspective\Kbankpayment\Model\Config;

use Perspective\Kbankpayment\Model\Config\Config;

class Direct18 extends Config
{
    const CODE = 'kbankpayment_direct18';

    var $BACK_URI = 'kbankpayment/direct18/backurl';
    var $RESP_URI = 'kbankpayment/response/cc';

    var $TEST_API_BASE_URL = 'https://dev-kpaymentgateway-services.kasikornbank.com/card/v2/';
    var $LIVE_API_BASE_URL = 'https://kpaymentgateway-services.kasikornbank.com/card/v2/';

    var $TOKEN_API_URI = 'token';
    var $CHARGE_API_URI = 'charge';

    public function getServiceID() {
        return $this->SERVICE_ID;
    }

    /**
     * Retrieve KBank Public Key whether live or test key
     *
     * @return string
     */
    public function getPublic() {

        if ($this->isSandboxEnabled()) {
            return $this->getTestPublic();
        }
        return $this->getLivePublic();
    }

    /**
     * Retrieve KBank live Public Key
     *
     * @return string
     */
    protected function getLivePublic() {
        return $this->getValue('live_public');
    }

    /**
     * Retrieve KBank test Public Key
     *
     * @return string
     */
    protected function getTestPublic() {
        return $this->getValue('test_public');
    }

    /**
     * Retrieve KBank Secret Key whether live or test key
     *
     * @return string
     */
    public function getSecret() {
        echo '<br> isSandboxEnabled' . $this->isSandboxEnabled();
        echo '<br> getTestSecret' . $this->getTestSecret();
        if ($this->isSandboxEnabled()) {
            return $this->getTestSecret();
        }
        return $this->getLiveSecret();
    }

    /**
     * Retrieve KBank live Secret Key
     *
     * @return string
     */
    protected function getLiveSecret() {
        return $this->getValue('live_secret');
    }

    /**
     * Retrieve KBank test Secret Key
     *
     * @return string
     */
    protected function getTestSecret() {
        return $this->getValue('test_secret');
    }

    /**
     * Retrieve KBank Api Base URL whether live or test key
     *
     * @return string
     */
    public function getApiBaseUrl() {
        if ($this->isSandboxEnabled()) {
            return $this->getTestApiBaseUrl();
        }
        return $this->getLiveApiBaseUrl();
    }

    /**
     * Retrieve KBank live Api Base URL
     *
     * @return string
     */
    protected function getLiveApiBaseUrl() {
        return $this->LIVE_API_BASE_URL;
    }

    /**
     * Retrieve KBank test Api Base URL
     *
     * @return string
     */
    protected function getTestApiBaseUrl() {
        return $this->TEST_API_BASE_URL;
    }

    /**
     * Retrieve KBank Token Api Url whether live or test key
     *
     * @return string
     */
    public function getTokenApiUrl() {
        return $this->getApiBaseUrl() . $this->TOKEN_API_URI;
    }

    /**
     * Retrieve KBank Charge Api Url whether live or test key
     *
     * @return string
     */
    public function getChargeApiUrl() {
        return $this->getApiBaseUrl() . $this->CHARGE_API_URI;
    }

}
