<?php

namespace Emertx\BankTransfer\Controller\Adminhtml\Manage;

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

		$resultPage->setActiveMenu('Emertx_BankTransfer::manage');
		$resultPage->addBreadcrumb(__('Bank Transfer'), __('Bank Transfer'));
		$resultPage->addBreadcrumb(__('Manage Bank Transfer'), __('Manage Bank Transfer'));
		$resultPage->getConfig()->getTitle()->prepend(__('Bank Transfer'));

		return $resultPage;
	}

}
