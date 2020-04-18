<?php

namespace Emertx\BankTransfer\Controller\Adminhtml\Account;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Description of Index
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
class Index extends \Magento\Backend\App\Action {

	/**
	 * @var PageFactory
	 */
	protected $resultPageFactory;

	/**
	 * @param Context $context
	 * @param PageFactory $resultPageFactory
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
	) {
		parent::__construct($context);
		$this->resultPageFactory = $resultPageFactory;
	}

	/**
	 * Is the user allowed to view the post grid.
	 *
	 * @return bool
	 */
	protected function _isAllowed() {
		return $this->_authorization->isAllowed('Emertx_BankTransfer::manage');
	}

	public function execute() {
		
		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->resultPageFactory->create();

		$resultPage->setActiveMenu('Emertx_BankTransfer::bank_account');
		$resultPage->addBreadcrumb(__('Bank Account'), __('Bank Account'));
		$resultPage->addBreadcrumb(__('Manage Bank Account'), __('Manage Bank Account'));
		$resultPage->getConfig()->getTitle()->prepend(__('Bank Account'));

		return $resultPage;
	}

}
