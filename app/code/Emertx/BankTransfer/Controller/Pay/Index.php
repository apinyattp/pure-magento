<?php

namespace Emertx\BankTransfer\Controller\Pay;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;

use Magento\Framework\File\UploaderFactory;
use Magento\Framework\Model\Exception as FrameworkException;
use Magento\Framework\File\Uploader;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Area;
use Magento\Framework\Mail\Template\TransportBuilder;

/**
 * Description of Index
 *
 * @author Komsit Rattana <komsitr@gmail.com>
 */
class Index extends \Magento\Framework\App\Action\Action {
    
    /**
	 * @var \Emertx\BankTransfer\Model\BankTransferFactory
	 */
	protected $modelFactory;
    
    /**
	 * @var \Emertx\BankTransfer\Model\BankAccountFactory
	 */
	protected $bankAccountFactory;
    
    /**
	 * @var \Magento\Sales\Model\OrderFactory
	 */
	protected $orderFactory;
    
    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;
    
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    
    /**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry = null;
    
    /**
     *
     * @var \Magento\Framework\App\Request\Http 
     */
    protected $request;
    
    /**
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    
    /**
     *
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;
    
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    
    /**
	 * uploader factory
	 *
	 * @var \Magento\Framework\File\UploaderFactory
	 */
	protected $uploaderFactory;
    
    /**
	 * @var \Magento\Framework\Mail\Template\TransportBuilder
	 */
	protected $transportBuilder;
    
    /**
	 * @var \Magento\Framework\Filesystem
	 */
	protected $fileSystem;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    
    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;
    
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
	 * @param \Emertx\BankTransfer\Model\BankTransferFactory $modelFactory
     * @param \Emertx\BankTransfer\Model\BankAccountFactory $bankAccountFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
	 * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param TransportBuilder $transportBuilder
     * @param UploaderFactory $uploaderFactory
     * @param Filesystem $fileSystem
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Emertx\BankTransfer\Model\BankTransferFactory $modelFactory,
        \Emertx\BankTransfer\Model\BankAccountFactory $bankAccountFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
		\Magento\Framework\Registry $registry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        TransportBuilder $transportBuilder,
        UploaderFactory $uploaderFactory,
        Filesystem $fileSystem
    ) {
        $this->modelFactory = $modelFactory;
        $this->bankAccountFactory = $bankAccountFactory;
        $this->orderFactory = $orderFactory;
        
        $this->resultPageFactory = $resultPageFactory;
		$this->resultForwardFactory = $resultForwardFactory;
		$this->_coreRegistry = $registry;
        $this->request = $request;
        $this->customerSession = $customerSession;
        $this->timezone = $timezone;
        $this->_scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_urlBuilder = $urlBuilder;
        
        $this->transportBuilder = $transportBuilder;
        $this->uploaderFactory = $uploaderFactory;
        $this->fileSystem = $fileSystem;
		
        parent::__construct($context);
    }
    
    
    public function execute() {
        
        try {
            
            //Check POST request
            $post = $this->request->getPost();
            if (!$post) {
                throw new \Exception(__('Method is now allowed'));
            }
            
            $amount = $this->request->getPost('amount');
            if (!is_numeric($amount)) {
                throw new \Exception(__('Please correct the transfer amount'));
            }
            
            if (!\Zend_Validate::is(trim($post['bank_account_id']), 'NotEmpty')) {
                throw new \Exception(__('Please choose the bank account'));
            }
            if (!\Zend_Validate::is(trim($post['date']), 'NotEmpty')) {
                throw new \Exception(__('Please enter transfer date'));
            }
            if (!\Zend_Validate::is(trim($post['hour']), 'NotEmpty')) {
                throw new \Exception(__('Please enter transfer time'));
            }
            if (!\Zend_Validate::is(trim($post['minute']), 'NotEmpty')) {
                throw new \Exception(__('Please enter transfer time'));
            }
            if (!\Zend_Validate::is(trim($post['amount']), 'NotEmpty')) {
                throw new \Exception(__('Please enter the amount of transfer'));
            }
            
            //Create record
            /* @var $payment \Emertx\BankTransfer\Model\BankTransfer */
            $payment = $this->modelFactory->create();
            
            /* @var $bankAccount \Emertx\BankTransfer\Model\BankAccount */
            $bankAccount = $this->bankAccountFactory->create();
            $bankAccount->load($post['bank_account_id']);
            if (!$bankAccount->getId() || !$bankAccount->isActive()) {
                throw new \Exception(__('Please correct the bank account'));
            }
            $payment->setBankAccountId($bankAccount->getId());
            
            $payment->setAmount($amount);
            
            $inDate = $this->request->getPost('date');
            if (!preg_match('/^2[0-9]{3}-[0-1][0-9]-[0-3][0-9]$/i', $inDate)) {
                throw new \Exception(__('Please correct the transfer date'));
            }
            $inDateSplit = explode('-', $inDate);
            $inHour = $this->request->getPost('hour');
            $inMinute = $this->request->getPost('minute');
            $transferDateTime = $this->timezone->date();
            $transferDateTime->setDate($inDateSplit[0], $inDateSplit[1], $inDateSplit[2]);
            $transferDateTime->setTime($inHour, $inMinute, 0);
            $localTransferDateTime = $transferDateTime->format('Y-m-d H:i:s');
            $transferDateTime->setTimezone(new \DateTimeZone('UTC'));
                
            $payment->setTransferDateTime($transferDateTime->format('Y-m-d H:i:s'));
            
            $payment->setCustomerName($this->request->getPost('name'));
            $payment->setCustomerPhone($this->request->getPost('phone'));
            $payment->setComment($this->request->getPost('comment'));
            
            if ($this->customerSession->isLoggedIn()) {
                $customer = $this->customerSession->getCustomer();
                
                //Load order info
                $order = $this->orderFactory->create();
                
                $orderId = $this->request->getPost('order_id');
                $orderNumber = $this->request->getPost('order_number');
                if ($orderId) {
                    $order->load($orderId);
                    
                    if (!$order->getId()) {
                        throw new \Exception(__('Please correct the order you want to pay'));
                    }
                    else if ($order->getCustomerId() != $customer->getId()) {
                        throw new \Exception(__('Please choose only your own orders to confirm the payment'));
                    }
                    
                    $payment->setCustomerId($customer->getId());
                    $payment->setOrderId($order->getId());
                    $payment->setOrderNumber($order->getRealOrderId());
                }
                else if ($orderNumber) {
                    $payment->setOrderNumber($orderNumber);
                }
            }
            else {
                $orderNumber = $this->request->getPost('order_number');
                if (!$orderNumber) {
                    throw new \Exception(__('Please enter the order number'));
                }
                
                $payment->setOrderNumber($orderNumber);
            }
            
            /**************************************************************/
            $fileName = null;
            $originalFilename = null;
            $attachmentDir = $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath('emertx/bank-transfer');
            if (isset($_FILES['slip']['name']) && !empty($_FILES['slip']['name'])) {
                try {
                    
                    $maxSize = $this->_scopeConfig->getValue(
                        'emertx_banktransfer/general/slip_max_size',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );
                    $mb = 1024 * 1024;
                    if ($_FILES['slip']['size'] > $maxSize) {
                        throw new \Exception(sprintf(__('Transfer slip is too large (limit up to %sMB)'), number_format($maxSize/$mb, $maxSize % $mb ? 1 : 0)));
                    }
                    
                    $originalFilename = $_FILES['slip']['name'];
                    
                    $fileName = $originalFilename;
                    $fileExt = strtolower(substr(strrchr($fileName, ".") ,1));
                    $fileNamewoe = rtrim($fileName, $fileExt);
                    $fileName = date('Ymd') . '-' . md5($fileNamewoe) . '.' . $fileExt;
                    
                    $uploader = $this->uploaderFactory->create(['fileId' => 'slip']);
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                    $uploader->setAllowCreateFolders(true);
                    $uploader->setAllowedExtensions(array('pdf', 'jpg', 'jpeg', 'gif', 'png'));
                    
                    $result = $uploader->save($attachmentDir, $fileName);
                    if ($result) {
                        $payment->setSlip($fileName);
                    }

                } catch (\Exception $e) {
                    $fileName = null;
                    $error = true;
                }
            }
            else {
                $isSlipRequired = $this->_scopeConfig->getValue(
                    'emertx_banktransfer/general/slip_required',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
                if ($isSlipRequired) {
                    throw new \Exception(__('Please attach the transfer slip'));
                }
            }
            /**************************************************************/
            
            $payment->setStatus(\Emertx\BankTransfer\Model\BankTransfer::STATUS_PENDING);
            $payment->save();
            
            //Send email to admin
            $adminEmail = $this->_scopeConfig->getValue(
                'emertx_banktransfer/general/admin_email',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            
            $senderEmail = $this->_scopeConfig->getValue(
                'emertx_banktransfer/general/sender_email',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            
            if (!empty($adminEmail) && !empty($senderEmail)) {
                                
                try {
                    $storeid = 1;

                    $senderData['name'] = 'Diamondgrain Store';
                    $senderData['email'] = $senderEmail;
                
                    $this->transportBuilder
                        ->setTemplateIdentifier('emertx_banktransfer_admin_notification')
                        ->setTemplateOptions([
                            'area' => Area::AREA_FRONTEND, 
                            'store' => $storeid
                        ])
                        ->setTemplateVars([
                            'bankName' => \Emertx\BankTransfer\Model\BankAccount::getBankName($bankAccount->getBank()),
                            'accountNo' => $bankAccount->getAccountNumber(),
                            'accountName' => $bankAccount->getAccountName(),
                            'orderNumber' => $payment->getOrderNumber(),
                            'transferDateTime' => $localTransferDateTime,
                            'hasSlip' => !!$payment->getSlip(),
                            'slipUrl' => $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]) . 'emertx/bank-transfer/' . $payment->getSlip(),
                            'customerName' => $payment->getCustomerName(),
                            'phone' => $payment->getCustomerPhone(),
                            'comment' => $payment->getComment(),
                            'amount' => $payment->getAmount(),
                        ])
                        ->setFrom($senderData);
                        
                    $adminEmails = explode(',', $adminEmail);
                    if ($adminEmails) {
                        foreach($adminEmails as $adminEmailItem) {
                            $adminEmailItem = trim($adminEmailItem);
                            if ($adminEmailItem) {
                                $this->transportBuilder->addTo($adminEmailItem);
                            }
                        }
                    }
                        
                    $transport = $this->transportBuilder->getTransport();
                    $transport->sendMessage();
                    
                } catch (\Exception $ex) {
                    //Ignore
                }
            }
            
//            $this->messageManager->addSuccess(__('We have received your payment details. Thank you.'));
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'orderNumber' => $payment->getOrderNumber()
                ],
            ]);
            
        } catch (\Exception $ex) {
            
            echo json_encode([
                'success' => false,
                'message' => $ex->getMessage(),
            ]);
            
        }
        exit;
    }

}
