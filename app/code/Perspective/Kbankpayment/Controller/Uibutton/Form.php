<?php
namespace Perspective\Kbankpayment\Controller\Uibutton;

use Exception;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment\Transaction;

class Form extends \Magento\Framework\App\Action\Action
{
    /**
     * @var string
     */
    const PATH_CART    = 'checkout/cart';
    const PATH_SUCCESS = 'checkout/onepage/success';
    protected $resultJsonFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @return void
     */
    public function execute() {
        $order_id = $this->getRequest()->getParam('order_id');
        if (! $order_id) {
            return $this->redirect(self::PATH_CART);
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('\Magento\Sales\Model\Order')
                                   ->load($order_id);
        if (! $order) {
            return $this->redirect(self::PATH_CART);
        }

        // if ($order->getState() !== Order::STATE_NEW && $order->getState() !== Order::STATE_PENDING_PAYMENT) {
        //     return $this->redirect(self::PATH_CART);
        // }
        
        $data['order_id'] = $order_id;
        $data['order_increment_id'] = $order->getIncrementId();
        $data['amount'] = number_format($order->getGrandTotal(),2, '.', '');

        // if ($order->getState() === Order::STATE_NEW) {
        //     $order->setState(Order::STATE_PENDING_PAYMENT);
        //     $order->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_PENDING_PAYMENT));
        //     $order->save();
        // }

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($data);
    }

    protected function redirect($path) {
        return $this->_redirect($path, ['_secure' => TRUE]);
    }
}
