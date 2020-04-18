<?php

namespace Emertx\BankTransfer\Block\Adminhtml\Account\Edit;

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
		$this->setId('bank_account_form');
		$this->setTitle(__('Bank Account Information'));
	}

	/**
	 * Prepare form
	 *
	 * @return $this
	 */
	protected function _prepareForm() {
		/* @var $model \Emertx\BankTransfer\Model\BankAccount */
		$model = $this->_coreRegistry->registry('emertx_bank_account');

		/** @var \Magento\Framework\Data\Form $form */
		$form = $this->_formFactory->create(
			['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post', 'enctype' => 'multipart/form-data']]
		);

		$form->setHtmlIdPrefix('bankacc_');

		$fieldset = $form->addFieldset(
			'base_fieldset', ['legend' => __('General Information'), 'class' => 'fieldset-wide']
		);

		if ($model->getId()) {
			$fieldset->addField('bank_account_id', 'hidden', ['name' => 'bank_account_id']);
		}
		
		$fieldset->addField(
			'is_active', 'select', [
				'label' => __('Status'),
				'title' => __('Status'),
				'name' => 'is_active',
				'required' => true,
				'options' => \Emertx\BankTransfer\Model\BankAccount::getAvailableStatuses(),
			]
		);
		if (!$model->getId()) {
			$model->setData('is_active', '1');
            $model->setData('sort', '1000');
		}
        
        $fieldset->addField(
			'bank', 'select', [
				'label' => __('Bank'),
				'title' => __('Bank'),
				'name' => 'bank',
				'required' => true,
				'options' => \Emertx\BankTransfer\Model\BankAccount::getBanks(),
			]
		);
		
		$fieldset->addField(
			'account_number', 'text', ['name' => 'account_number', 'label' => __('Account Number'), 'title' => __('Account Number'), 'required' => true]
		);
        
        $fieldset->addField(
			'account_name', 'text', ['name' => 'account_name', 'label' => __('Account Name'), 'title' => __('Account Name'), 'required' => true]
		);
        
        $fieldset->addField(
			'account_name_th', 'text', ['name' => 'account_name_th', 'label' => __('Account Name (Thai)'), 'title' => __('Account Name (Thai)'), 'required' => false]
		);
        
        $fieldset->addField(
			'sort', 'text', ['name' => 'sort', 'label' => __('Sort'), 'title' => __('Sort'), 'required' => true, 'note' => __('Items are sorted in ascending order')]
		);
		
		$form->setValues($model->getData());
		$form->setUseContainer(true);
		$this->setForm($form);

		return parent::_prepareForm();
	}

}
