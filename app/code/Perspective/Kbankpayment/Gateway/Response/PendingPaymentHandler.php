<?php
namespace Perspective\Kbankpayment\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Payment\Gateway\Helper\SubjectReader;

class PendingPaymentHandler implements HandlerInterface
{

    /**
     * @inheritdoc
     */
    public function handle(array $handlingSubject, array $response) {
        $payment = SubjectReader::readPayment($handlingSubject)->getPayment();
        ContextHelper::assertOrderPayment($payment);
        $order = $payment->getOrder();
        $order->setState(Order::STATE_PENDING_PAYMENT);
        $order->setStatus(Order::STATE_PENDING_PAYMENT);
        $order->setIsNotified(TRUE);
    }

}
