<?php

namespace Emertx\BankTransfer\Controller\Adminhtml\Account;

/**
 * Description of Delete
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
class Delete extends \Magento\Backend\App\Action {
	
	/**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Emertx_BankTransfer::manage');
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('bank_account_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
			
            try {
                /* @var $model \Emertx\BankTransfer\Model\BankAccount */
                $model = $this->_objectManager->create('Emertx\BankTransfer\Model\BankAccount');
                $model->load($id);
                
                $date = new \DateTime();
                $model->setDeletedTime($date->format('Y-m-d H:i:s'));
                
                $model->save();
                
                $this->messageManager->addSuccess(__('The item has been deleted.'));
                return $resultRedirect->setPath('*/*/');
                
            } catch (\Exception $e) {
                
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/');
                
            }
        }
        $this->messageManager->addError(__('We can\'t find a item to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
	
}
