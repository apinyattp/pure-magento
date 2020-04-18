<?php

namespace Emertx\BankTransfer\Model\ResourceModel\BankAccount;

/**
 * Description of Collection
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

	/**
     * @var string
     */
    protected $_idFieldName = 'bank_account_id';
	
	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct() {
		$this->_init('Emertx\BankTransfer\Model\BankAccount', 'Emertx\BankTransfer\Model\ResourceModel\BankAccount');
	}

}
