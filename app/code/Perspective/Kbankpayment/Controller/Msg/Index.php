<?php
/**
 * Copyright © 2015 Clounce. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Perspective\Kbankpayment\Controller\Msg;
 
/**
 * Display Hello on screen using a template
 */
class Index extends \Magento\Framework\App\Action\Action
{
/**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    protected $orderFactory;

    /**
     * @var string
     */
    const PATH_ERROR    = '404';
 
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->orderFactory = $orderFactory;
    }
 
    /**
     * Default hello page with layout
     *
     * @return void
     */
    public function execute()
    {
        $msg = "";
        $status = "";
        $order_id = $this->getRequest()->getParam('order_id');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('\Magento\Sales\Model\Order')->load($order_id);

        if($order->getIncrementId()){
            $orderIncrementId = $order->getIncrementId();
            $payment = $order->getPayment();
            $charge_data = $payment->getAdditionalInformation('charge_data');

            $status = $charge_data['status'];
            $msg = $charge_data['failure_message'] ? $charge_data['failure_message'] : '-';

            /** @var \Magento\Framework\View\Result\Page $resultPage */
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getLayout()->initMessages();

            $resultPage->getLayout()->getBlock('msg_display')->setOrderid($orderIncrementId);
            $resultPage->getLayout()->getBlock('msg_display')->setMsg($msg);
            $resultPage->getLayout()->getBlock('msg_display')->setStatus($status);
            $resultPage->getConfig()->getTitle()->set('');
            return $resultPage;
        }
        return $this->redirect(self::PATH_ERROR);
    }

    /**
     * @param  string $path
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function redirect($path)
    {
        return $this->_redirect($path, ['_secure' => true]);
    }
}