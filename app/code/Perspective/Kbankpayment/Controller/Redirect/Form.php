<?php
namespace Perspective\Kbankpayment\Controller\Form;

use Exception;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Payment\Transaction;

class Form extends \Magento\Framework\App\Action\Action
{
    /**
     * @var string
     */
    const PATH_CART    = 'checkout/cart';
    private $_pageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        parent::__construct($context);
        $this->_pageFactory = $pageFactory;
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

        if ($order->getState() !== Order::STATE_NEW && $order->getState() !== Order::STATE_PENDING_PAYMENT) {
            return $this->redirect(self::PATH_CART);
        }

        $redirect_config = $this->_objectManager->create('Perspective\Kbankpayment\Model\Config\Redirect');
        $storeManager = $this->_objectManager->get('\Magento\Store\Model\StoreManagerInterface');

        $page = $this->_pageFactory->create();

        $firstname = $order->getCustomerFirstname();
        $middlename = $order->getCustomerMiddlename();
        $lastname = $order->getCustomerLastname();

        $billing_address = $order->getBillingAddress();
        if(!$firstname) $firstname = $billing_address->getFirstname();
        if(!$middlename) $middlename = $billing_address->getMiddlename();
        if(!$lastname) $lastname = $billing_address->getLastname();

        $customer_id = $billing_address->getCustomerID();
        if(!$customer_id) $customer_id = $billing_address->getEntityID();

        $params = $this->_transaction_parameter($order_id, $order, $redirect_config, $storeManager);

        $layout = $page->getLayout();
        $block = $layout->getBlock('kbankpayment_form_redirect');
        $block->assign(
            [
                'url' => $redirect_config->getURL(),
                'params' => $params,
            ]
        );

        if ($order->getState() === Order::STATE_NEW) {
            $order->setState(Order::STATE_PENDING_PAYMENT);
            $order->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_PENDING_PAYMENT));
            $order->save();
        }

        return $page;
    }

    private function _transaction_parameter($order_id, $order, $redirect_config, $storeManager) {
        $amount = number_format($order->getGrandTotal() * 100, 0, '', '');
        $detail = 'สั่งซื้อสินค้า/บริการออนไลน์';
        $params = [
            'MERCHANT2' => $redirect_config->getMID(),
            'TERM2' => $redirect_config->getTID(),
            'AMOUNT2' => str_pad($amount, 12, '0', STR_PAD_LEFT),
            'URL2' => $storeManager->getStore()->getBaseUrl().$redirect_config->getBackURI(),
            'RESPURL' => $storeManager->getStore()->getBaseUrl().$redirect_config->getRespURI(),
            'IPCUST2' => $order->getRemoteIp(),
            'DETAIL2' => $detail,
            'INVMERCHANT' => str_pad($order_id, 12, '0', STR_PAD_LEFT),
            'FILLSPACE' => 'Y',
        ];
        $params['CHECKSUM'] = $this->_create_checksum($params, $redirect_config);
        return $params;
    }

    private function _create_checksum($params, $config) {
        $str = implode('', $params) . $config->getSecret();
        return md5($str);
    }

    protected function redirect($path) {
        return $this->_redirect($path, ['_secure' => TRUE]);
    }

}
