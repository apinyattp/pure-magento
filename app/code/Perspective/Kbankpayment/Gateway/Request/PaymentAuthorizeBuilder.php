<?php
namespace Perspective\Kbankpayment\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

class PaymentAuthorizeBuilder implements BuilderInterface
{
    /**
     * @var string
     */
    const CAPTURE = 'capture';

    /**
     * @param  array $buildSubject
     *
     * @return array
     */
    public function build(array $buildSubject)
    {
        return [ self::CAPTURE => false ];
    }
}
