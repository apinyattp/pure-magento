<?php

namespace Emertx\BankTransfer\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Emertx\BankTransfer\Api\Data\BankTransferInterface;

/**
 * Description of BankTransfer
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
class BankTransfer extends \Magento\Framework\Model\AbstractModel implements BankTransferInterface, IdentityInterface {
    
    const STATUS_PENDING = 'Pending';
	const STATUS_COMPLETED = 'Completed';
    const STATUS_CANCELED = 'Canceled';
    
    /**
	 * cache tag
	 */
	const CACHE_TAG = 'emertx_bank_transfer';

	/**
	 * @var string
	 */
	protected $_cacheTag = 'emertx_bank_transfer';

	/**
	 * Prefix of model events names
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'emertx_bank_transfer';
    
    
    /**
	 * Prepare statuses.
	 * @return array
	 */
	public static function getAvailableStatuses() {
		return [
            self::STATUS_PENDING => __('Pending'),
            self::STATUS_COMPLETED => __('Completed'),
            self::STATUS_CANCELED => __('Canceled'),
        ];
	}
    
    
    /**
	 * Initialize resource model
	 *
	 * @return void
	 */
	protected function _construct() {
		$this->_init('Emertx\BankTransfer\Model\ResourceModel\BankTransfer');
	}
    
    
    public function getIdentities() {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
    
    
    /**
     * Get ID
     *
     * @return int
     */
    public function getId() {
        return $this->getData(self::MODEL_ID);
    }

	/**
	 * Get customer id
	 *
	 * @return int|null
	 */
	public function getCustomerId() {
        return $this->getData(self::CUSTOMER_ID);
    }
    
    /**
	 * Get customer name
	 *
	 * @return string|null
	 */
	public function getCustomerName() {
        return $this->getData(self::CUSTOMER_NAME);
    }
    
    /**
	 * Get customer phone
	 *
	 * @return string|null
	 */
	public function getCustomerPhone() {
        return $this->getData(self::CUSTOMER_PHONE);
    }
    
	/**
	 * Get order database id
	 *
	 * @return int|null
	 */
	public function getOrderId() {
        return $this->getData(self::ORDER_ID);
    }
    
    /**
	 * Get order number
	 *
	 * @return string|null
	 */
	public function getOrderNumber() {
        return $this->getData(self::ORDER_NUMBER);
    }
    
    /**
	 * Get transfer date/time
	 *
	 * @return string
	 */
	public function getTransferDateTime() {
        return $this->getData(self::TRANSFER_DATETIME);
    }
    
    /**
	 * Get transfer slip
	 *
	 * @return string|null
	 */
	public function getSlip() {
        return $this->getData(self::SLIP);
    }
    
    /**
	 * Get bank account id
	 *
	 * @return int
	 */
	public function getBankAccountId() {
        return $this->getData(self::BANK_ACCOUNT_ID);
    }
    
    /**
	 * Get request status
	 *
	 * @return string
	 */
	public function getStatus() {
        return $this->getData(self::STATUS);
    }
    
    /**
	 * Get amount
	 *
	 * @return double
	 */
	public function getAmount() {
        return $this->getData(self::AMOUNT);
    }
    
    /**
	 * Get comment
	 *
	 * @return string|null
	 */
	public function getComment() {
        return $this->getData(self::COMMENT);
    }
    
    /**
	 * Get deleted time
	 *
	 * @return string
	 */
	public function getDeletedTime() {
        return $this->getData(self::DELETED_TIME);
    }

	/**
	 * Get creation time
	 *
	 * @return string
	 */
	public function getCreationTime() {
        return $this->getData(self::CREATION_TIME);
    }

	/**
	 * Get update time
	 *
	 * @return string
	 */
	public function getUpdateTime() {
        return $this->getData(self::UPDATE_TIME);
    }
    
    
    
    
    
    
    /**
     * Set ID
     *
     * @param int $id
     * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
     */
    public function setId($id) {
        return $this->setData(self::MODEL_ID, $id);
    }
    
    /**
	 * Set customer id
	 *
	 * @param int $customerId
     * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setCustomerId($customerId) {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }
    
    /**
	 * Set customer name
	 *
	 * @param string $customerName
     * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setCustomerName($customerName) {
        return $this->setData(self::CUSTOMER_NAME, $customerName);
    }
    
    /**
	 * Set customer phone
	 *
	 * @param string $customerPhone
     * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setCustomerPhone($customerPhone) {
        return $this->setData(self::CUSTOMER_PHONE, $customerPhone);
    }
    
	/**
	 * Set order database id
	 *
	 * @param int $orderId
     * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setOrderId($orderId) {
        return $this->setData(self::ORDER_ID, $orderId);
    }
    
    /**
	 * Set order number
	 *
	 * @param string $orderNumber
     * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setOrderNumber($orderNumber) {
        return $this->setData(self::ORDER_NUMBER, $orderNumber);
    }
    
    /**
	 * Set transfer date/time
	 *
	 * @param string $transferDateTime
     * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setTransferDateTime($transferDateTime) {
        return $this->setData(self::TRANSFER_DATETIME, $transferDateTime);
    }
    
    /**
	 * Set transfer slip
	 *
	 * @param string $slip
     * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setSlip($slip) {
        return $this->setData(self::SLIP, $slip);
    }
    
    /**
	 * Set bank account id
	 *
	 * @param int $bankAccountId
     * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setBankAccountId($bankAccountId) {
        return $this->setData(self::BANK_ACCOUNT_ID, $bankAccountId);
    }
    
    /**
	 * Set request status
	 *
	 * @param string $status
     * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setStatus($status) {
        return $this->setData(self::STATUS, $status);
    }
    
    /**
	 * Set amount
	 *
	 * @param double
     * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setAmount($amount) {
        return $this->setData(self::AMOUNT, $amount);
    }
    
    /**
	 * Set comment
	 *
	 * @param string
     * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setComment($comment) {
        return $this->setData(self::COMMENT, $comment);
    }
    
    /**
	 * Set deleted time
	 *
	 * @param string $deletedTime
	 * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setDeletedTime($deletedTime) {
        return $this->setData(self::DELETED_TIME, $deletedTime);
    }

	/**
	 * Set creation time
	 *
	 * @param string $creationTime
	 * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setCreationTime($creationTime) {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

	/**
	 * Set update time
	 *
	 * @param string $updateTime
	 * @return \Emertx\BankTransfer\Api\Data\BankTransferInterface
	 */
	public function setUpdateTime($updateTime) {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }
    
    
    

}
