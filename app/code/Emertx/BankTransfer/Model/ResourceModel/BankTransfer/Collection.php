<?php

namespace Emertx\BankTransfer\Model\ResourceModel\BankTransfer;

/**
 * Description of Collection
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

	/**
     * @var string
     */
    protected $_idFieldName = 'request_id';
	
	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct() {
		$this->_init('Emertx\BankTransfer\Model\BankTransfer', 'Emertx\BankTransfer\Model\ResourceModel\BankTransfer');
	}

}
