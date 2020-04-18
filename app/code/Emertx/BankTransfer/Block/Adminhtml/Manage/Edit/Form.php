<?php

namespace Emertx\BankTransfer\Block\Adminhtml\Manage\Edit;

/**
 * Description of Form
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic {
	
	/**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

	/**
	 * @var \Magento\Store\Model\System\Store
	 */
	protected $_systemStore;

	/**
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Framework\Data\FormFactory $formFactory
	 * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
	 * @param \Magento\Store\Model\System\Store $systemStore
	 * @param array $data
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Data\FormFactory $formFactory,
		\Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
		\Magento\Store\Model\System\Store $systemStore,
		array $data = []
	) {
		$this->_systemStore = $systemStore;
		$this->_wysiwygConfig = $wysiwygConfig;
		
		parent::__construct($context, $registry, $formFactory, $data);
	}

	/**
	 * Init form
	 *
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setId('bank_transfer_form');
		$this->setTitle(__('Bank Transfer Approval Form'));
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
	 * Prepare form
	 *
	 * @return $this
	 */
	protected function _prepareForm() {
		/* @var $model \Emertx\BankTransfer\Model\BankTransfer */
		$model = $this->_coreRegistry->registry('emertx_bank_transfer');

		/** @var \Magento\Framework\Data\Form $form */
		$form = $this->_formFactory->create(
			['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post', 'enctype' => 'multipart/form-data']]
		);

		$form->setHtmlIdPrefix('bankreq_');
        
        $fieldset = $form->addFieldset(
			'base_fieldset', ['legend' => __('Order Information'), 'class' => 'fieldset-wide']
		);
        
		if ($model->getId()) {
			$fieldset->addField('request_id', 'hidden', ['name' => 'request_id']);
		}
        
        $fieldset->addField(
			'order_number', 'text', ['name' => 'order_number', 'label' => __('Order Number'), 'title' => __('Order Number'), 'required' => true]
		);
        
        $fieldset->addField(
			'approve_comment', 'textarea', ['name' => 'approve_comment', 'label' => __('Note'), 'title' => __('Note'), 'required' => false]
		);
		
		$form->setValues($model->getData());
		$form->setUseContainer(true);
		$this->setForm($form);

		return parent::_prepareForm();
	}

}
