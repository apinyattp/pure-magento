<?php

namespace Emertx\BankTransfer\Block;

/**
 * Description of InformPayment
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
class InformPayment extends \Magento\Framework\View\Element\Template implements
    \Magento\Framework\DataObject\IdentityInterface {
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
	
	/**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    
    /**
     *
     * @var \Emertx\BankTransfer\Model\BankAccountRepository 
     */
    protected $bankAccountRepository;
    
    /**
     *
     * @var \Magento\Customer\Model\Session 
     */
    protected $customerSession;
    
    /**
     *
     * @var \Magento\Framework\View\Asset\Repository 
     */
    protected $assetRepo;
    
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_orderCollectionFactory;
    
    /**
     *
     * @var \Magento\Framework\Locale\Resolver
     */
    protected $localeResolver;
    
    /**
     *
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    public $timezone;
    
    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param \Magento\Framework\ObjectManagerInterface $objectManager
	 * @param \Magento\Framework\Registry $registry
     * @param \Emertx\BankTransfer\Model\BankAccountRepository $bankAccountRepository
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\Locale\Resolver $localeResolver
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param array $data
     */
    public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Framework\Registry $registry,
        \Emertx\BankTransfer\Model\BankAccountRepository $bankAccountRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\Locale\Resolver $localeResolver,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
		array $data = []
    ) {
		
		$this->_objectManager = $objectManager;
		$this->_coreRegistry = $registry;
        $this->bankAccountRepository = $bankAccountRepository;
        $this->customerSession = $customerSession;
        $this->assetRepo = $assetRepo;
        $this->localeResolver = $localeResolver;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->timezone = $timezone;
        
		parent::__construct($context, $data);
    }
    
    protected function _construct() {
        parent::_construct();
        $this->setTemplate('Emertx_BankTransfer::inform-payment.phtml');
    }

    public function getIdentities() {
		return [\Emertx\BankTransfer\Model\BankTransfer::CACHE_TAG . '_inform_payment_block'];
    }
    
    public function getBankName($bank) {
        return \Emertx\BankTransfer\Model\BankAccount::getBankName($bank);
    }
    
    public function getBankLogoUrl($bank) {
        return $this->assetRepo->getUrl('Emertx_BankTransfer::img/'.$bank.'.png');
    }
    
    public function getBankAccounts() {
        return $this->bankAccountRepository->getBankAccounts();
    }
    
    /**
	 * Get translated account name
     * @param \Emertx\BankTransfer\Model\BankAccount $bankAccount
     * @return string
     */
    public function getTranslatedAccountName($bankAccount) {
        $accountName = null;
        if ($this->localeResolver->getLocale() == 'th_TH') {
            $accountName = $bankAccount->getAccountNameTh();
        }
        if (empty($accountName)) {
            $accountName = $bankAccount->getAccountName();
        }
        return $accountName;
    }
    
    /**
     * 
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer() {
        return $this->isCustomerLogin() ? $this->customerSession->getCustomer() : null;
    }
    
    public function isCustomerLogin() {
        return $this->customerSession->isLoggedIn();
    }
    
    public function getCustomerName() {
        $customer = $this->getCustomer();
        if ($customer) {
            return $customer->getName();
        }
        return null;
    }
    
    public function getCustomerPhone() {
        $customer = $this->getCustomer();
        if ($customer) {
            $address = $customer->getPrimaryBillingAddress();
            return $address ? $address->getTelephone() : null;
        }
        return null;
    }
    
    public function isSlipRequired() {
        $isSlipRequired = $this->_scopeConfig->getValue(
            'emertx_banktransfer/general/slip_required',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $isSlipRequired;
    }
    
    public function getMaximumFileSize() {
        $maxSize = $this->_scopeConfig->getValue(
            'emertx_banktransfer/general/slip_max_size',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $mb = 1024 * 1024;
        if ($maxSize % $mb !== 0) {
            return number_format($maxSize/$mb, 1);
        }
        else {
            return number_format($maxSize/$mb, 0);
        }
    }
    
    public function getUnpaidBankTransferOrders() {
        $customer = $this->getCustomer();
        if ($customer) {
            $collection = $this->_orderCollectionFactory->create();
            $collection->getSelect()
                ->join(['p' => $collection->getTable('sales_order_payment')], "main_table.entity_id = p.parent_id AND p.method='banktransfer'")
                ->where('main_table.status = ?', 'Pending')
                ->where('main_table.customer_id = ?', $customer->getId())
                ->order('main_table.entity_id DESC');

            return $collection;
        }
        return false;
    }
    
    public function _prepareLayout() {
        $this->pageConfig->addPageAsset('Emertx_BankTransfer::css/bootstrap-datepicker3.min.css');
        $this->pageConfig->addPageAsset('Emertx_BankTransfer::css/bank-transfer.css');
//        if ($this->localeResolver->getLocale() === 'th_TH') {
//            $this->pageConfig->addPageAsset('Emertx_BankTransfer::js/bootstrap-datepicker.th.min.js');
//        }
        
        parent::_prepareLayout();
    }

}
