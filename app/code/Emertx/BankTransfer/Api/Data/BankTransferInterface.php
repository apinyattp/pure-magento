<?php

namespace Emertx\BankTransfer\Api\Data;

/**
 * Description of BankTransferInterface
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
interface BankTransferInterface {
    
    const MODEL_ID = 'request_id';
	const CUSTOMER_ID = 'customer_id';
    const CUSTOMER_NAME = 'name';
	const CUSTOMER_PHONE = 'phone';
	const ORDER_ID = 'order_id';
	const ORDER_NUMBER = 'order_number';
    const TRANSFER_DATETIME = 'transfer_datetime';
    const SLIP = 'slip';
    const BANK_ACCOUNT_ID = 'bank_acc_id';
	const STATUS = 'status';
    const DELETED_TIME = 'deleted_time';
    const CREATION_TIME = 'creation_time';
    const UPDATE_TIME = 'update_time';
    const AMOUNT = 'amount';
    const COMMENT = 'comment';
    
    /**
     * Get ID
     *
     * @return int
     */
    public function getId();

	/**
	 * Get customer id
	 *
	 * @return int|null
	 */
	public function getCustomerId();
    
    /**
	 * Get customer name
	 *
	 * @return string|null
	 */
	public function getCustomerName();
    
    /**
	 * Get customer phone
	 *
	 * @return string|null
	 */
	public function getCustomerPhone();
    
	/**
	 * Get order database id
	 *
	 * @return int|null
	 */
	public function getOrderId();
    
    /**
	 * Get order number
	 *
	 * @return string|null
	 */
	public function getOrderNumber();
    
    /**
	 * Get transfer date/time
	 *
	 * @return string
	 */
	public function getTransferDateTime();
    
    /**
	 * Get transfer slip
	 *
	 * @return string|null
	 */
	public function getSlip();
    
    /**
	 * Get bank account id
	 *
	 * @return int
	 */
	public function getBankAccountId();
    
    /**
	 * Get request status
	 *
	 * @return string
	 */
	public function getStatus();
    
    /**
	 * Get amount
	 *
	 * @return double
	 */
	public function getAmount();
    
    /**
	 * Get comment
	 *
	 * @return string|null
	 */
	public function getComment();
    
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
     * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
     */
    public function setId($id);
    
    /**
	 * Set customer id
	 *
	 * @param int $customerId
     * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setCustomerId($customerId);
    
    /**
	 * Set customer name
	 *
	 * @param string $customerName
     * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setCustomerName($customerName);
    
    /**
	 * Set customer phone
	 *
	 * @param string $customerPhone
     * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setCustomerPhone($customerPhone);
    
	/**
	 * Set order database id
	 *
	 * @param int $orderId
     * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setOrderId($orderId);
    
    /**
	 * Set order number
	 *
	 * @param string $orderNumber
     * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setOrderNumber($orderNumber);
    
    /**
	 * Set transfer date/time
	 *
	 * @param string $transferDateTime
     * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setTransferDateTime($transferDateTime);
    
    /**
	 * Set transfer slip
	 *
	 * @param string $slip
     * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setSlip($slip);
    
    /**
	 * Set bank account id
	 *
	 * @param int $bankAccountId
     * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setBankAccountId($bankAccountId);
    
    /**
	 * Set request status
	 *
	 * @param string $status
     * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setStatus($status);
    
    /**
	 * Set amount
	 *
	 * @param double
     * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setAmount($amount);
    
    /**
	 * Set comment
	 *
	 * @param string
     * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setComment($comment);
    
    /**
	 * Set deleted time
	 *
	 * @param string $deletedTime
	 * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setDeletedTime($deletedTime);

	/**
	 * Set creation time
	 *
	 * @param string $creationTime
	 * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setCreationTime($creationTime);

	/**
	 * Set update time
	 *
	 * @param string $updateTime
	 * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setUpdateTime($updateTime);
    
}
