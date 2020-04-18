<?php

namespace Emertx\BankTransfer\Controller\Adminhtml\Manage;

use Magento\Backend\App\Action;

/**
 * Description of Approve
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
class Approve extends \Magento\Backend\App\Action {
    
    /**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry = null;

	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;

	/**
	 * @param Action\Context $context
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 * @param \Magento\Framework\Registry $registry
	 */
	public function __construct(
		Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\Registry $registry
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->_coreRegistry = $registry;
		parent::__construct($context);
	}
	
	/**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Emertx_BankTransfer::manage');
    }
    
    /**
	 * Init actions
	 *
	 * @return \Magento\Backend\Model\View\Result\Page
	 */
	protected function _initAction() {
		// load layout, set active menu and breadcrumbs
		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->resultPageFactory->create();
		$resultPage->setActiveMenu('Emertx_BankTransfer::manage')
			->addBreadcrumb(__('Bank Transfer'), __('Bank Transfer'))
            ->addBreadcrumb(__('Approve Bank Transfer'), __('Approve Bank Transfer'));
            
		return $resultPage;
	}

	/**
	 * Edit item
	 *
	 * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function execute() {
		$id = $this->getRequest()->getParam('request_id');
        
        /* @var $model \Emertx\BankTransfer\Model\BankTransfer */
		$model = $this->_objectManager->create('Emertx\BankTransfer\Model\BankTransfer');

		if ($id) {
			$model->load($id);
            $isError = false;
			if (!$model->getId()) {
                $isError = true;
				$this->messageManager->addError(__('This item no longer exists.'));
			}
            else if ($model->getStatus() === \Emertx\BankTransfer\Model\BankTransfer::STATUS_CANCELED) {
                //$isError = true;
                //$this->messageManager->addError(__('This item has been canceled.'));
            }
            else if ($model->getStatus() === \Emertx\BankTransfer\Model\BankTransfer::STATUS_COMPLETED) {
                //$isError = true;
                //$this->messageManager->addError(__('This item has been approved.'));
            }
            if ($isError) {
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
				$resultRedirect = $this->resultRedirectFactory->create();
				return $resultRedirect->setPath('*/*/');
            }
		}

		$data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		$this->_coreRegistry->register('emertx_bank_transfer', $model);

		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->_initAction();
		$resultPage->addBreadcrumb(
			__('Approve Bank Transfer Payment'), __('Approve Bank Transfer Payment')
		);
		$resultPage->getConfig()->getTitle()->prepend(__('Bank Transfer'));
		$resultPage->getConfig()->getTitle()
			->prepend(__('Approve Bank Transfer Payment'));

		return $resultPage;
	}
    
    
}
