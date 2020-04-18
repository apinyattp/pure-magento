<?php

namespace Emertx\BankTransfer\Controller\Adminhtml\Account;

use Magento\Backend\App\Action;

/**
 * Description of Save
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
class Save extends \Magento\Backend\App\Action {
	
	/**
	 * @param Action\Context $context
	 */
	public function __construct(
		Action\Context $context
	) {
		parent::__construct($context);
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
			/* @var $model \Emertx\BankTransfer\Model\BankAccount  */
			$model = $this->_objectManager->create('Emertx\BankTransfer\Model\BankAccount');

			$id = $this->getRequest()->getParam('bank_account_id');
			if ($id) {
				$model->load($id);
			}
			
			$model->setData($data);

			$this->_eventManager->dispatch(
				'emertx_bank_account_prepare_save', ['bank_account' => $model, 'request' => $this->getRequest()]
			);
			
			try {
				$model->save();
				
				$this->messageManager->addSuccess(__('You saved this item.'));
				$this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					return $resultRedirect->setPath('*/*/edit', ['bank_account_id' => $model->getId(), '_current' => true]);
				}
				return $resultRedirect->setPath('*/*/');
			} catch (\Magento\Framework\Exception\LocalizedException $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\RuntimeException $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\Exception $e) {
				$this->messageManager->addException($e, __('Something went wrong while saving the item.'));
			}

			$this->_getSession()->setFormData($data);
			return $resultRedirect->setPath('*/*/edit', ['bank_account_id' => $this->getRequest()->getParam('bank_account_id')]);
		}
		return $resultRedirect->setPath('*/*/');
	}

}
