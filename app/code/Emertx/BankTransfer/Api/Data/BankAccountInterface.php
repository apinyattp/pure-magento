<?php

namespace Emertx\BankTransfer\Api\Data;

/**
 * Description of BankAccountInterface
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
interface BankAccountInterface {
    
    const MODEL_ID = 'bank_account_id';
	const BANK = 'bank';
    const ACCOUNT_NUMBER = 'account_number';
	const ACCOUNT_NAME = 'account_name';
    const ACCOUNT_NAME_TH = 'account_name_th';
	const SORT = 'sort';
	const IS_ACTIVE = 'is_active';
    const DELETED_TIME = 'deleted_time';
    const CREATION_TIME = 'creation_time';
	const UPDATE_TIME = 'update_time';
	
    /**
     * Get ID
     *
     * @return int
     */
    public function getId();

	/**
	 * Get bank
	 *
	 * @return string
	 */
	public function getBank();
    
    /**
	 * Get account number
	 *
	 * @return string
	 */
	public function getAccountNumber();
    
    /**
	 * Get account name
	 *
	 * @return string
	 */
	public function getAccountName();
    
    /**
	 * Get account name (thai)
	 *
	 * @return string
	 */
	public function getAccountNameTh();
    
	/**
	 * Get sort
	 *
	 * @return int
	 */
	public function getSort();
    
    /**
	 * Is active
	 *
	 * @return bool|null
	 */
	public function isActive();
    
    /**
	 * Get deleted time
	 *
	 * @return string
	 */
	public function getDeletedTime();

	/**
	 * Get creation time
	 *
	 * @return string
	 */
	public function getCreationTime();

	/**
	 * Get update time
	 *
	 * @return string
	 */
	public function getUpdateTime();
	
    
    
    
    
	
	/**
     * Set ID
     *
     * @param int $id
     * @return \Emertx\BankTransfer\Api\Data\BankAccountInterface
     */
    public function setId($id);

	/**
	 * Set bank
	 *
	 * @param string $bank
	 * @return \Emertx\BankTransfer\Api\Data\BankAccountInterface
	 */
	public function setBank($bank);
    
    /**
	 * Set account number
	 *
	 * @param string $accountNumber
	 * @return \Emertx\BankTransfer\Api\Data\BankAccountInterface
	 */
	public function setAccountNumber($accountNumber);
    
    /**
	 * Set account name
	 *
	 * @param string $accountName
	 * @return \Emertx\BankTransfer\Api\Data\BankAccountInterface
	 */
	public function setAccountName($accountName);
    
    /**
	 * Set account name (thai)
	 *
	 * @param string $accountNameTh
	 * @return \Emertx\BankTransfer\Api\Data\BankAccountInterface
	 */
	public function setAccountNameTh($accountNameTh);
    
	/**
	 * Set sort
	 *
	 * @param string $sort
	 * @return \Emertx\BankTransfer\Api\Data\BankAccountInterface
	 */
	public function setSort($sort);
    
    /**
	 * Set is active
	 *
	 * @param int|bool $isActive
	 * @return \Emertx\BankTransfer\Api\Data\BankAccountInterface
	 */
	public function setIsActive($isActive);
    
    /**
	 * Set deleted time
	 *
	 * @param string $deletedTime
	 * @return \Emertx\BankTransfer\Api\Data\BankAccountInterface
	 */
	public function setDeletedTime($deletedTime);

	/**
	 * Set creation time
	 *
	 * @param string $creationTime
	 * @return \Emertx\BankTransfer\Api\Data\BankAccountInterface
	 */
	public function setCreationTime($creationTime);

	/**
	 * Set update time
	 *
	 * @param string $updateTime
	 * @return \Emertx\BankTransfer\Api\Data\BankAccountInterface
	 */
	public function setUpdateTime($updateTime);
    
}
