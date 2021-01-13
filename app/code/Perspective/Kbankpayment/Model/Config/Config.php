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
