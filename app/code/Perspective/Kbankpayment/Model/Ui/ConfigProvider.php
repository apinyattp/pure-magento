<?php
namespace Perspective\Kbankpayment\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Perspective\Kbankpayment\Model\Config\Redirect;
use Perspective\Kbankpayment\Model\Config\Direct18;
use Perspective\Kbankpayment\Model\Config\Uibutton;
use Perspective\Kbankpayment\Model\Config\Uibuttonterm;
use Perspective\Kbankpayment\Model\Config\Uiqr;

class ConfigProvider implements ConfigProviderInterface
{

    /**
     * @var \Perspective\Kbankpayment\Model\Config\Redirect
     */
    protected $kbankpaymentRedirectConfig;

    /**
     * @var \Perspective\Kbankpayment\Model\Config\Direct18
     */
    protected $KbankpaymentDirect18Config;

    /**
     * @var \Perspective\Kbankpayment\Model\Config\Uibutton
     */
    protected $KbankpaymentUibuttonConfig;

    /**
     * @var \Perspective\Kbankpayment\Model\Config\Uibuttonterm
     */
    protected $KbankpaymentUibuttontermConfig;

    /**
     * @var \Perspective\Kbankpayment\Model\Config\Uiqr
     */
    protected $KbankpaymentUiqrConfig;

    public function __construct(Redirect $kbankpaymentRedirectConfig, Direct18 $kbankpaymentDirect18Config, Uibutton $KbankpaymentUibuttonConfig, Uibuttonterm $KbankpaymentUibuttontermConfig, Uiqr $KbankpaymentUiqrConfig) {
        $this->kbankpaymentRedirectConfig   = $kbankpaymentRedirectConfig;
        $this->kbankpaymentDirect18Config   = $kbankpaymentDirect18Config;
        $this->KbankpaymentUibuttonConfig   = $KbankpaymentUibuttonConfig;
        $this->KbankpaymentUibuttontermConfig = $KbankpaymentUibuttontermConfig;
        $this->KbankpaymentUiqrConfig   = $KbankpaymentUiqrConfig;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig() {
        return [
            'payment' => [
                Redirect::CODE => [
                    'url' => $this->kbankpaymentRedirectConfig->getURL(),
                    'mid' => $this->kbankpaymentRedirectConfig->getMID(),
                    'tid' => $this->kbankpaymentRedirectConfig->getTID(),
                    'back_uri' => $this->kbankpaymentRedirectConfig->getBackURI(),
                ],
                Direct18::CODE => [
                    'url' => $this->kbankpaymentDirect18Config->getURL(),
                    'public' => $this->kbankpaymentDirect18Config->getPublic(),
                ],
                Uibutton::CODE => [
                    'public' => $this->KbankpaymentUibuttonConfig->getPublic(),
                    'secret' => $this->KbankpaymentUibuttonConfig->getSecret(),
                ],
                Uibuttonterm::CODE => [
                    'public' => $this->KbankpaymentUibuttontermConfig->getPublic(),
                    'secret' => $this->KbankpaymentUibuttontermConfig->getSecret(),
                    'mid' => $this->KbankpaymentUibuttontermConfig->getMID(),
                    'tid' => $this->KbankpaymentUibuttontermConfig->getTid(),
                ],
                Uiqr::CODE => [
                    'public' => $this->KbankpaymentUiqrConfig->getPublic(),
                    'secret' => $this->KbankpaymentUiqrConfig->getSecret(),
                ],
            ]
        ];
    }

}
