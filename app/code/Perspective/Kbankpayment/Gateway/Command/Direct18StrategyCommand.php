<?php
namespace Perspective\Kbankpayment\Gateway\Command;

use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Sales\Model\Order;
use Perspective\Kbankpayment\Model\Config\Direct18 as Config;

class Direct18StrategyCommand implements CommandInterface
{
    /**
     * @var string
     */
    const ACTION_AUTHORIZE         = \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE;

    /**
     * @var string
     */
    const COMMAND_AUTHORIZE         = 'authorize';
    const COMMAND_AUTHORIZE_CAPTURE = 'capture';

    /**
     * @var \Magento\Payment\Gateway\Command\CommandPoolInterface
     */
    private $_commandPool;

    /**
     * @var \Perspective\Kbankpayment\Model\Config\Direct18
     */
    private $_config;

    public function __construct(
        CommandPoolInterface $commandPool,
        Config               $config
    ) {
        $this->_commandPool = $commandPool;
        $this->_config      = $config;
    }

    /**
     * @inheritdoc
     */
    public function execute(array $commandSubject) {
        /** @var \Magento\Payment\Model\InfoInterface **/
        $payment = SubjectReader::readPayment($commandSubject)->getPayment();
        ContextHelper::assertOrderPayment($payment);

        $order        = $payment->getOrder();
        $totalDue     = $order->getTotalDue();
        $baseTotalDue = $order->getBaseTotalDue();

        switch ($this->getPaymentAction($commandSubject)) {
            // case self::ACTION_AUTHORIZE:
            //     $this->_commandPool->get(self::COMMAND_AUTHORIZE)->execute($commandSubject);
            //     break;
            case self::ACTION_AUTHORIZE:
                $payment->authorize(true, $baseTotalDue);
                $payment->setAmountAuthorized($totalDue);
                break;
            default:
                throw new CommandException(__('TODO : Rewrite error message'));
                break;
        }
    }

    /**
     * @param  array  $commandSubject
     *
     * @return string
     */
    protected function getPaymentAction(array $commandSubject) {
        return $commandSubject['paymentAction'];
    }

    /**
     * @param array  $commandSubject
     * @param string $state
     * @param string $status
     */
    protected function updateOrderState(array $commandSubject, $state, $status) {
        $stateObject = SubjectReader::readStateObject($commandSubject);
        $stateObject->setState($state);
        $stateObject->setStatus($status);
    }

}
