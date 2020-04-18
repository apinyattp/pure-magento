<?php

namespace Emertx\BankTransfer\Block\Adminhtml\Manage;

use Magento\Framework\UrlInterface;

/**
 * Description of View
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
class View extends \Magento\Backend\Block\Widget {
    
    /**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry = null;
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;
    
    /**
     * Factory for payment method models
     *
     * @var \Magento\Payment\Model\Method\Factory
     */
    protected $_methodFactory;

	/**
	 * @param \Magento\Backend\Block\Widget\Context $context
	 * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Payment\Model\Method\Factory $paymentMethodFactory
	 * @param array $data
	 */
	public function __construct(
		\Magento\Backend\Block\Widget\Context $context,
		\Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Payment\Model\Method\Factory $paymentMethodFactory,
		array $data = []
	) {
        
		$this->_coreRegistry = $registry;
        $this->_objectManager = $objectManager;
        $this->timezone = $timezone;
        $this->_methodFactory = $paymentMethodFactory;
        
		parent::__construct($context, $data);
	}
    
    /**
     * 
     * @return \Emertx\BankTransfer\Model\BankTransfer
     */
    public function getBankTransfer() {
        /* @var $transfer \Emertx\BankTransfer\Model\BankTransfer */
        $transfer = $this->_coreRegistry->registry('emertx_bank_transfer');
        return $transfer;
    }
    
    /**
     * 
     * @return \Emertx\BankTransfer\Model\BankAccount
     */
    public function getBankAccount() {
        
        /* @var $transfer \Emertx\BankTransfer\Model\BankTransfer */
        $transfer = $this->getBankTransfer();
        
        /* @var $bankAccount \Emertx\BankTransfer\Model\BankAccount */
		$bankAccount = $this->_objectManager->create('Emertx\BankTransfer\Model\BankAccount');
        $bankAccount->load($transfer->getBankAccountId());
        
        if ($bankAccount->getId()) {
            return $bankAccount;
        }
        return null;
    }
    
    /**
     * Empty ID to get member who inform the payment
     * 
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer($id = null) {
        
        $transfer = $this->getBankTransfer();
        $customerId = empty($id) ? $transfer->getCustomerId() : $id;
        if (!$customerId) {
            return null;
        }
        
        /* @var $customer \Magento\Customer\Model\Customer */
		$customer = $this->_objectManager->create('\Magento\Customer\Model\Customer');
        $customer->load($customerId);
        
        return $customer;
    }
    
    /**
     * 
     * @return \DateTime
     */
    public function getTransferDate() {
        $transfer = $this->getBankTransfer();
        return $this->timezone->date($transfer->getTransferDateTime());
    }
    
    /**
     * 
     * @return string
     */
    public function getSlipUrl() {
        $transfer = $this->getBankTransfer();
        $url = $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]) . 'emertx/bank-transfer/' . $transfer->getSlip();
        return $url;
    }
    
    /**
     * 
     * @return \Magento\Sales\Model\Order
     */
    public function getRelatedOrder() {
        $transfer = $this->getBankTransfer();
        $orderNumber = $transfer->getOrderNumber();
        if (!$orderNumber) {
            return null;
        }
        
        /* @var $order \Magento\Sales\Model\Order */
		$order = $this->_objectManager->create('\Magento\Sales\Model\Order');
        $order->loadByIncrementId($orderNumber);
        if ($order->getId()) {
            return $order;
        }
        
        return null;
    }
    
    public function getPaymentMethodName($code) {
        $class = $this->_scopeConfig->getValue(
            sprintf('payment/%s/model', $code),
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $paymentModel = $this->_methodFactory->create($class);
        return $paymentModel->getConfigData('title');
    }
    
}
