<?php

namespace Emertx\BankTransfer\Model\BankAccount\Source;

/**
 * Description of Bank
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
class Bank implements \Magento\Framework\Data\OptionSourceInterface {

	/**
	 * Constructor
	 *
	 */
	public function __construct() {
        
	}

	/**
	 * Get options
	 *
	 * @return array
	 */
	public function toOptionArray() {
		$options[] = ['label' => '', 'value' => ''];
		$availableOptions = \Emertx\BankTransfer\Model\BankAccount::getBanks();
		foreach ($availableOptions as $key => $value) {
			$options[] = [
				'label' => $value,
				'value' => $key,
			];
		}
		return $options;
	}

}