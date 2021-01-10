<?php
namespace Perspective\Kbankpayment\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Pay In Store payment method model
 */
class UiButton extends \Magento\Payment\Model\Method\AbstractMethod
{
 
    /**
     * Payment code
     *
     * @var string
     */
    
    const CODE = 'kbankpayment_uibutton';
    protected $_code = self::CODE;
 
    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = true;
}
