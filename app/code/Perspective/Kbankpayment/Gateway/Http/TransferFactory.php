<?php
namespace Perspective\Kbankpayment\Gateway\Http;

use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;

class TransferFactory implements TransferFactoryInterface
{
    /**
     * @var \Magento\Payment\Gateway\Http\TransferBuilder $transferBuilder
     */
    private $_transferBuilder;

    /**
     * @param \Magento\Payment\Gateway\Http\TransferBuilder $transferBuilder
     */
    public function __construct(TransferBuilder $transferBuilder) {
        $this->_transferBuilder = $transferBuilder;
    }

    /**
     * Builds gateway transfer object
     *
     * @param  array $request
     *
     * @return \Magento\Payment\Gateway\Http\TransferInterface
     */
    public function create(array $request) {
        return $this->_transferBuilder->setBody($request)->build();
    }

}
