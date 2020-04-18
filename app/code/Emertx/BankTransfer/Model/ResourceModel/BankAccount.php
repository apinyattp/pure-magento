<?php

namespace Emertx\BankTransfer\Model\ResourceModel;

/**
 * BankTransfer mysql resource
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
class BankAccount extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

	/**
	 * @var \Magento\Framework\Stdlib\DateTime\DateTime
	 */
	protected $_date;

	/**
	 * Construct
	 *
	 * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
	 * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
	 * @param string|null $resourcePrefix
	 */
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context, 
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		$resourcePrefix = null
	) {
		parent::__construct($context, $resourcePrefix);
		$this->_date = $date;
	}

	/**
	 * Initialize resource model
	 *
	 * @return void
	 */
	protected function _construct() {
		$this->_init('emertx_bank_account', \Emertx\BankTransfer\Model\BankAccount::MODEL_ID);
	}
	
	/**
	 * Before deleting filter
	 * 
	 * @param \Magento\Framework\Model\AbstractModel $object
	 * @return $this
	 */
	protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object) {
		
		return parent::_beforeDelete($object);
	}

	/**
	 * Process post data before saving
	 *
	 * @param \Magento\Framework\Model\AbstractModel $object
	 * @return $this
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object) {

		if ($object->isObjectNew() && !$object->hasCreationTime()) {
			$object->setCreationTime($this->_date->gmtDate());
		}

		$object->setUpdateTime($this->_date->gmtDate());

		return parent::_beforeSave($object);
	}

	/**
	 * Retrieve select object for load object data
	 *
	 * @param string $field
	 * @param mixed $value
	 * @param \Emertx\BankTransfer\Model\BankAccount $object
	 * @return \Zend_Db_Select
	 */
	protected function _getLoadSelect($field, $value, $object) {
		$select = parent::_getLoadSelect($field, $value, $object);

		$select
            ->where(
                'deleted_time IS NULL'
            )->limit(
                1
            );

		return $select;
	}

}
