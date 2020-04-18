<?php

namespace Emertx\BankTransfer\Controller\Adminhtml\Manage;

/**
 * Description of Save
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
class Save extends \Magento\Backend\App\Action {
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * @var \Magento\Sales\Api\OrderManagementInterface
     */
    protected $orderManagement;
    
    /**
     * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Sales\Api\OrderManagementInterface $orderManagement
     */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement
	) {
		parent::__construct($context);
		
		$this->scopeConfig = $scopeConfig;
        $this->orderManagement = $orderManagement;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _isAllowed() {
		return $this->_authorization->isAllowed('Emertx_BankTransfer::manage');
	}

	/**
	 * Save action
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute() {
		
		$data = $this->getRequest()->getPostValue();
		/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
		$resultRedirect = $this->resultRedirectFactory->create();
		if ($data) {
			/* @var $model \Emertx\BankTransfer\Model\BankTransfer  */
			$model = $this->_objectManager->create('Emertx\BankTransfer\Model\BankTransfer');
			$id = $this->getRequest()->getParam('request_id');
			if ($id) {
				$model->load($id);
			}
			
			try {
                if (!$model->getId()) {
                    throw new \RuntimeException(__('The payment is not found.'));
                }
                else if ($model->getStatus() !== \Emertx\BankTransfer\Model\BankTransfer::STATUS_PENDING) {
                    throw new \RuntimeException(__('The payment had already been processed.'));
                }
                
                //Load order
                /* @var $order \Magento\Sales\Model\Order */
                $orderNumber = $this->getRequest()->getParam('order_number');
                $approveComment = $this->getRequest()->getParam('approve_comment');
                $order = $this->_objectManager->create('\Magento\Sales\Model\Order');
                $order->loadByIncrementId($orderNumber);
                if (!$order->getId()) {
                    throw new \RuntimeException(__('The order is not found.'));
                }
                else if ($order->getStatus() !== 'pending') {
                    throw new \RuntimeException(__('The order had already been processed.'));
                }
                
                //Change order status to processing
                $newOrderStatus = $this->scopeConfig->getValue('emertx_banktransfer/general/order_status');
                $order
                    ->setState(\Magento\Sales\Model\Order::STATE_PROCESSING)
                    ->setStatus($newOrderStatus)
                    ->save();
                
                $comment = sprintf(__('Bank payment ID: %s is approved'), $model->getId());
                $order
                    ->addStatusHistoryComment($comment, $newOrderStatus)
                    ->setIsCustomerNotified(false)->save();
                
                if (!empty($approveComment)) {
                    $order
                        ->addStatusHistoryComment($approveComment, $newOrderStatus)
                        ->setIsCustomerNotified(false)->save();
                }
                
                
                $payment = $order->getPayment();
                $payment
                    ->setTransactionId($model->getId())
                    ->setParentTransactionId($model->getId())
                    ->setCurrencyCode($order->getBaseCurrencyCode())
                    ->registerCaptureNotification($model->getAmount());
                
                $order->save();
                
                //Send order email
				if (!$order->getEmailSent()) {
					$this->orderManagement->notify($order->getEntityId());
				}
                
                $model->setStatus(\Emertx\BankTransfer\Model\BankTransfer::STATUS_COMPLETED);
				$model->save();
				
				$this->messageManager->addSuccess(sprintf(__('Bank payment ID: %s has been approved.'), $model->getId()));
				$this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                
				return $resultRedirect->setPath('*/*/');
                
			} catch (\Magento\Framework\Exception\LocalizedException $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\RuntimeException $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\Exception $e) {
				$this->messageManager->addException($e, __('Something went wrong while saving the item.'));
			}

			$this->_getSession()->setFormData($data);
			return $resultRedirect->setPath('*/*/approve', ['request_id' => $this->getRequest()->getParam('request_id')]);
		}
		return $resultRedirect->setPath('*/*/');
	}

}
