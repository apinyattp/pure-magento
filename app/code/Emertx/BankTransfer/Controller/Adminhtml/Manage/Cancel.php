<?php

namespace Emertx\BankTransfer\Controller\Adminhtml\Manage;

/**
 * Description of Cancel
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
class Cancel extends \Magento\Backend\App\Action {
	
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
        $id = $this->getRequest()->getParam('request_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
			
            try {
                /* @var $model \Emertx\BankTransfer\Model\BankTransfer */
                $model = $this->_objectManager->create('Emertx\BankTransfer\Model\BankTransfer');
                $model->load($id);
                
                if (!$model->getId()) {
                    throw new \Exception(__('This item no longer exists.'));
                }
                else if ($model->getStatus() === \Emertx\BankTransfer\Model\BankTransfer::STATUS_CANCELED) {
                    throw new \Exception(__('This item had been canceled.'));
                }
                else if ($model->getStatus() === \Emertx\BankTransfer\Model\BankTransfer::STATUS_COMPLETED) {
                    throw new \Exception(__('This item had been approved.'));
                }
                
                $model->setStatus(\Emertx\BankTransfer\Model\BankTransfer::STATUS_CANCELED);
                $model->save();
                
                $this->messageManager->addSuccess(__('The item has been canceled.'));
                return $resultRedirect->setPath('*/*/');
                
            } catch (\Exception $e) {
                
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/');
                
            }
        }
        $this->messageManager->addError(__('We can\'t find a item to canceled.'));
        return $resultRedirect->setPath('*/*/');
    }
	
}
