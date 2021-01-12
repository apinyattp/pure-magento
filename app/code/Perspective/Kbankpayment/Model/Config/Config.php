<?php
namespace Perspective\Kbankpayment\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface as MagentoScopeConfigInterface;
use Magento\Store\Model\ScopeInterface as MagentoScopeInterface;

class Config
{
    const CODE = 'kbankpayment';
    const MODULE_NAME = 'Persective_Kbankpayment';

    var $BACK_URI = '';
    var $RESP_URI = '';
    var $COMMAND = '';
    var $TEST_URL = '';
    var $LIVE_URL = '';
    var $BANK_IP = '203.146.18.94';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(MagentoScopeConfigInterface $scopeConfig) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param  string $field
     * @param  string $code
     *
     * @return mixed
     */
    public function getValue($field, $code = FALSE) {
        if($code === FALSE) $code = static::CODE;
        return $this->scopeConfig->getValue(
            'payment/' . $code . '/' . $field,
            MagentoScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if KBank's sandbox mode enable or not
     *
     * @return bool
     */
    public function isSandboxEnabled() {
        if ($this->getValue('sandbox_status', self::CODE)) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Retrieve KBank merchant ID whether live or test key
     *
     * @return string
     */
    public function getMID() {

        if ($this->isSandboxEnabled()) {
            return $this->getTestMID();
        }
        return $this->getLiveMID();
    }

    /**
     * Retrieve KBank live merchant ID
     *
     * @return string
     */
    protected function getLiveMID() {
        return $this->getValue('live_mid', self::CODE);
    }

    /**
     * Retrieve KBank test merchant ID
     *
     * @return string
     */
    protected function getTestMID() {
        return $this->getValue('test_mid', self::CODE);
    }

    /**
     * Retrieve KBank terminal id whether live or test key
     *
     * @return string
     */
    public function getTID() {
        if ($this->isSandboxEnabled()) {
            return $this->getTestTID();
        }

        return $this->getLiveTID();
    }

    /**
     * Retrieve KBank live terminal id
     *
     * @return string
     */
    protected function getLiveTID() {
        return $this->getValue('live_tid', self::CODE);
    }

    /**
     * Retrieve KBank test terminal id
     *
     * @return string
     */
    protected function getTestTID() {
        return $this->getValue('test_tid', self::CODE);
    }

    /**
     * Retrieve KBank merchant ID whether live or test key
     *
     * @return string
     */
    public function getMIDTerm() {

        if ($this->isSandboxEnabled()) {
            return $this->getTestMIDTerm();
        }
        return $this->getLiveMIDTerm();
    }

    /**
     * Retrieve KBank live merchant ID
     *
     * @return string
     */
    protected function getLiveMIDTerm() {
        return $this->getValue('live_mid_term', self::CODE);
    }

    /**
     * Retrieve KBank test merchant ID
     *
     * @return string
     */
    protected function getTestMIDTerm() {
        return $this->getValue('test_mid_term', self::CODE);
    }

    /**
     * Retrieve KBank terminal id whether live or test key
     *
     * @return string
     */
    public function getTIDTerm() {
        if ($this->isSandboxEnabled()) {
            return $this->getTestTIDTerm();
        }

        return $this->getLiveTIDTerm();
    }

    /**
     * Retrieve KBank live terminal id
     *
     * @return string
     */
    protected function getLiveTIDTerm() {
        return $this->getValue('live_tid_term', self::CODE);
    }

    /**
     * Retrieve KBank test terminal id
     *
     * @return string
     */
    protected function getTestTIDTerm() {
        return $this->getValue('test_tid_term', self::CODE);
    }

    public function getBackURI() {
        return $this->BACK_URI;
    }

    public function getRespURI() {
        return $this->RESP_URI;
    }

    public function getURL() {
        if ($this->isSandboxEnabled()) {
            return $this->getTestURL();
        }

        return $this->getLiveURL();
    }

    protected function getLiveURL() {
        return $this->LIVE_URL;
    }

    protected function getTestURL() {
        return $this->TEST_URL;
    }

    public function getBankIP() {
        if ($this->isSandboxEnabled()) {
            return FALSE;
        }
        return $this->BANK_IP;
    }

}
