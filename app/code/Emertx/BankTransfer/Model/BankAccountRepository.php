<?php

namespace Emertx\BankTransfer\Model;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Description of BankAccountRepository
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
class BankAccountRepository implements \Emertx\BankTransfer\Api\BankAccountRepositoryInterface {
	
	/**
     * @var BankAccountFactory
     */
    protected $modelFactory;
	
	/**
     * @var \Emertx\BankTransfer\Model\ResourceModel\BankAccount\CollectionFactory
     */
    protected $collectionFactory;
	
	/**
     * @var \Emertx\BankTransfer\Model\ResourceModel\BankAccount
     */
    protected $resourceModel;
	
	/**
     * @param BankAccountFactory $modelFactory
     * @param \Emertx\BankTransfer\Model\ResourceModel\BankAccount\CollectionFactory $collectionFactory
     * @param \Emertx\BankTransfer\Model\ResourceModel\BankAccount $resourceModel
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        BankAccountFactory $modelFactory,
        \Emertx\BankTransfer\Model\ResourceModel\BankAccount\CollectionFactory $collectionFactory,
        \Emertx\BankTransfer\Model\ResourceModel\BankAccount $resourceModel
    ) {
        $this->modelFactory = $modelFactory;
        $this->collectionFactory = $collectionFactory;
        $this->resourceModel = $resourceModel;
    }
    
    /**
     * Save bank account data
     *
     * @param \Emertx\BankTransfer\Api\Data\BankAccountInterface $model
     * @return BankAccountInterface
     * @throws CouldNotSaveException
     */
    public function save(\Emertx\BankTransfer\Api\Data\BankAccountInterface $model)
    {
        try {
            
            $this->resourceModel->save($model);
            
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        
        return $model;
    }
    
    /**
     * Delete bank account
     *
     * @param \Emertx\BankTransfer\Api\Data\BankAccountInterface $model
     * @throws CouldNotSaveException
     */
    public function delete(\Emertx\BankTransfer\Api\Data\BankAccountInterface $model)
    {
        $model->setDeletedTime(new \DateTime());
        
        try {
            $this->resourceModel->save($model);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
    }

	/**
     * {@inheritdoc}
     */
	public function getBankAccounts($limit = 0) {
		
		/** @var \Emertx\BankTransfer\Model\ResourceModel\BankAccount\Collection $collection */
        $collection = $this->collectionFactory
			->create()
			->addOrder(\Emertx\BankTransfer\Api\Data\BankAccountInterface::SORT, 'ASC')
			->addOrder(\Emertx\BankTransfer\Api\Data\BankAccountInterface::MODEL_ID, 'DESC')
			->addFilter(\Emertx\BankTransfer\Api\Data\BankAccountInterface::IS_ACTIVE, BankAccount::STATUS_ENABLED)
			;
		
		return $collection;
		
	}

}
