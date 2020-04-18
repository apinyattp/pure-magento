<?php

namespace Emertx\BankTransfer\Api\Data;

/**
 * Description of BankAccountSearchResultsInterface
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
interface BankAccountSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface {

	/**
	 * Get pages list.
	 *
	 * @return \Emertx\BankTransfer\Api\Data\BankAccountInterface[]
	 */
	public function getItems();

	/**
	 * Set pages list.
	 *
	 * @param \Emertx\BankTransfer\Api\Data\BankAccountInterface[] $items
	 * @return $this
	 */
	public function setItems(array $items);
}
