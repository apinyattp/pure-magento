<?php

namespace Emertx\BankTransfer\Model;

use Magento\Framework\DataObject\IdentityInterface;

use Emertx\BankTransfer\Api\Data\BankAccountInterface;

/**
 * Description of BankAccount
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
class BankAccount extends \Magento\Framework\Model\AbstractModel implements BankAccountInterface, IdentityInterface {
	/*	 * #@+
	 * Statuses
	 */
	const STATUS_ENABLED = 1;
	const STATUS_DISABLED = 0;
	/*	 * #@- */
    
    /**
     * List of Banks
     */
    const BANK_BBL = 'bbl';
    const BANK_GHB = 'ghb';
    const BANK_GSB = 'gsb';
    const BANK_IBANK = 'ibank';
    const BANK_KBANK = 'kbank';
    const BANK_KRUNGSRI = 'krungsri';
    const BANK_KTB = 'ktb';
    const BANK_SCB = 'scb';
    const BANK_TBANK = 'tbank';
    const BANK_TMB = 'tmb';
    const BANK_UOB = 'uob';

	/**
	 * cache tag
	 */
	const CACHE_TAG = 'emertx_bank_account';

	/**
	 * @var string
	 */
	protected $_cacheTag = 'emertx_bank_account';

	/**
	 * Prefix of model events names
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'emertx_bank_account';

	/**
	 * Initialize resource model
	 *
	 * @return void
	 */
	protected function _construct() {
		$this->_init('Emertx\BankTransfer\Model\ResourceModel\BankAccount');
	}

	/**
	 * Prepare statuses.
	 * @return array
	 */
	public static function getAvailableStatuses() {
		return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
	}
    
    public static function getBankName($bank) {
        $banks = self::getBanks();
        if (array_key_exists($bank, $banks)) {
            return $banks[$bank];
        }
        return $bank;
    }
    
    /**
	 * Prepare statuses.
	 * @return array
	 */
	public static function getBanks() {
		return [
            self::BANK_BBL => __('Bangkok Bank'),
            self::BANK_GHB => __('Government Housing Bank'),
            self::BANK_GSB => __('Government Savings Bank'),
            self::BANK_IBANK => __('Islamic Bank of Thailand'),
            self::BANK_KBANK => __('Kasikornbank'),
            self::BANK_KRUNGSRI => __('Bank of Ayudhya'),
            self::BANK_KTB => __('Krung Thai Bank'),
            self::BANK_SCB => __('Siam Commercial Bank'),
            self::BANK_TBANK => __('Thanachart Bank'),
            self::BANK_TMB => __('TMB Bank'),
            self::BANK_UOB => __('UOB Bank'),
        ];
	}

	/**
	 * Return unique ID(s) for each object in system
	 *
	 * @return array
	 */
	public function getIdentities() {
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

    public function getAccountName() {
        return $this->getData(self::ACCOUNT_NAME);
    }
    
    public function getAccountNameTh() {
        return $this->getData(self::ACCOUNT_NAME_TH);
    }

    public function getAccountNumber() {
        return $this->getData(self::ACCOUNT_NUMBER);
    }

    public function getBank() {
        return $this->getData(self::BANK);
    }

    public function getCreationTime() {
        return $this->getData(self::CREATION_TIME);
    }

    public function getDeletedTime() {
        return $this->getData(self::DELETED_TIME);
    }

    public function getSort() {
        return $this->getData(self::SORT);
    }

    public function getUpdateTime() {
        return $this->getData(self::UPDATE_TIME);
    }

    public function isActive() {
        return $this->getData(self::IS_ACTIVE);
    }
    
    
    
    

    public function setAccountName($accountName) {
        return $this->setData(self::ACCOUNT_NAME, $accountName);
    }

    public function setAccountNameTh($accountNameTh) {
        return $this->setData(self::ACCOUNT_NAME_TH, $accountNameTh);
    }

    public function setAccountNumber($accountNumber) {
        return $this->setData(self::ACCOUNT_NUMBER, $accountNumber);
    }

    public function setBank($bank) {
        return $this->setData(self::BANK, $bank);
    }

    public function setCreationTime($creationTime) {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    public function setDeletedTime($deletedTime) {
        return $this->setData(self::DELETED_TIME, $deletedTime);
    }

    public function setIsActive($isActive) {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    public function setSort($sort) {
        return $this->setData(self::SORT, $sort);
    }

    public function setUpdateTime($updateTime) {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }

}