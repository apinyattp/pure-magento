<?php

namespace Emertx\BankTransfer\Block\Adminhtml\Manage;

/**
 * Description of Approve
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
class Approve extends \Magento\Backend\Block\Widget\Form\Container {

	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry = null;

	/**
	 * @param \Magento\Backend\Block\Widget\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param array $data
	 */
	public function __construct(
		\Magento\Backend\Block\Widget\Context $context,
		\Magento\Framework\Registry $registry,
		array $data = []
	) {
		$this->_coreRegistry = $registry;
		parent::__construct($context, $data);
	}
    
    /**
	 * Initialize blog post edit block
	 *
	 * @return void
	 */
	protected function _construct() {
		$this->_objectId = 'request_id';
		$this->_blockGroup = 'Emertx_BankTransfer';
		$this->_controller = 'adminhtml_manage';

		parent::_construct();
        
        /* @var $transfer \Emertx\BankTransfer\Model\BankTransfer */
        $transfer = $this->getBankTransfer();

		if ($transfer->getStatus() === \Emertx\BankTransfer\Model\BankTransfer::STATUS_PENDING && $this->_isAllowedAction('Emertx_BankTransfer::manage')) {
            $this->buttonList->update('save', 'label', __('Approve'));
        }
        else {
            $this->buttonList->remove('save');
        }

        $this->buttonList->remove('reset');
		$this->buttonList->remove('delete');
        
	}
    
    /**
     * 
     * @return \Emertx\BankTransfer\Model\BankTransfer
     */
    public function getBankTransfer() {
        /* @var $transfer \Emertx\BankTransfer\Model\BankTransfer */
        $transfer = $this->_coreRegistry->registry('emertx_bank_transfer');
        return $transfer;
    }

	/**
	 * Retrieve text for header element depending on loaded post
	 *
	 * @return \Magento\Framework\Phrase
	 */
	public function getHeaderText() {
        return __('Approve Bank Transfer Payment');
	}

	/**
	 * Check permission for passed action
	 *
	 * @param string $resourceId
	 * @return bool
	 */
	protected function _isAllowedAction($resourceId) {
		return $this->_authorization->isAllowed($resourceId);
	}
    
    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    /**
     * Prepare form Html. Add block for configurable product modification interface.
     *
     * @return string
     */
    public function getFormHtml()
    {
        $html = parent::getFormHtml();
        
        /* @var $transfer \Emertx\BankTransfer\Model\BankTransfer */
        $transfer = $this->getBankTransfer();

		if ($transfer->getStatus() === \Emertx\BankTransfer\Model\BankTransfer::STATUS_PENDING) {
            return $html;
        }
        
        return '';
    }
}
