<?php
namespace Perspective\Kbankpayment\Controller\Response;

use Exception;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Payment\Transaction;
use Perspective\Kbankpayment\Model\Config\Uiqr;

class Qr extends Action
{
    /**
     * @var string
     */
    const PATH_CART    = 'cart';
    const PATH_SUCCESS = 'onepage/success';

    /**
     * @var \Magento\Sales\Model\Service\InvoiceService
     */
    private $_invoiceService;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\InvoiceSender
     */
    private $_invoiceSender;

    /**
     * @var \Magento\Framework\DB\Transaction
     */
    private $_transaction;

    /**
     * @var Perspective\Kbankpayment\Model\Config\Uiqr
     */
    protected $config;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $session;
    protected $_logger;
    protected $resultJsonFactory;

    public function __construct(
        Context $context,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Magento\Framework\DB\Transaction $transaction,
        \Psr\Log\LoggerInterface $logger,
        Uiqr $config,
        Session $session,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->_invoiceService = $invoiceService;
        $this->_invoiceSender = $invoiceSender;
        $this->_transaction = $transaction;
        $this->config = $config;
        $this->session = $session;
        $this->_logger = $logger;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * @return void
     */
    public function execute() {
        $response = file_get_contents('php://input');

        $filesystem = $objectManager->get('Magento\Framework\Filesystem');
        $directoryList = $objectManager->get('Magento\Framework\App\Filesystem\DirectoryList');
        $media = $filesystem->getDirectoryWrite($directoryList::MEDIA);
        $contents = json_encode($response);
        $media->writeFile("sample_card1.json",$response);

        $data = json_decode($content,TRUE);
        
        $order_id = $data['order_id'];
        $charge_id = $data['id'];

        if (! $order_id) {
            return $this->redirect(self::PATH_CART);
        }
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('\Magento\Sales\Model\Order')->load($order_id);
        
        $inquiry = $this->_makeRequest($charge_id);

        if (! $payment = $order->getPayment()) {
            $this->invalid($order, __('Cannot retrieve a payment detail from the request. Please contact our support if you have any questions.'));
            $response['sucess_url'] = '/kbankpayment/msg?order_id='.$order_id;
        }

        $payment->setAdditionalInformation('qr_charge', $inquiry);
        
        if($inquiry['transaction_state'] !== 'Authorized' && $inquiry['status'] !== 'success'){
            $this->messageManager->addErrorMessage(__('The transaction state is not authorized, please make an order again or contact our support if you have any questions.'));
            $response['sucess_url'] = '/kbankpayment/msg?order_id='.$order_id;
        }

        if (! $order->getId()) {
            $this->messageManager->addErrorMessage(__('The order session no longer exists, please make an order again or contact our support if you have any questions.'));
            $response['sucess_url'] = '/kbankpayment/msg?order_id='.$order_id;
        }

        if (!in_array($payment->getMethod(), array('kbankpayment_uiqr'))) {
            $this->invalid($order, __('Invalid payment method. Please contact our support if you have any questions.'));
            $response['sucess_url'] = '/kbankpayment/msg?order_id='.$order_id;
        }

        if (! $inquiry['order_id'] = $payment->getAdditionalInformation('k_order_id')) {
            $this->cancel($order, __('Cannot retrieve a charge reference id. Please contact our support to confirm your payment.'));
            $this->session->restoreQuote();
            $response['sucess_url'] = '/kbankpayment/msg?order_id='.$order_id;
        }

        if($order->canInvoice()) {
            $invoice = $this->_invoiceService->prepareInvoice($order);
            $invoice->register();
            $invoice->save();
            $transactionSave = $this->_transaction->addObject(
                $invoice
            )->addObject(
                $invoice->getOrder()
            );
            $transactionSave->save();
            $this->_invoiceSender->send($invoice);
            //send notification code
            $order->addStatusHistoryComment(
                __('Notified customer about invoice #%1.', $invoice->getId())
            )
            ->setIsCustomerNotified(TRUE)
            ->save();
        }

        $order->setState(Order::STATE_PROCESSING);
        $order->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING));

        $order->save();

        $response['sucess_url'] = self::PATH_SUCCESS;

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }

    /**
     * @param  string $path
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function redirect($path)
    {
        return $this->_redirect($path, ['_secure' => true]);
    }

    /**
     * @param  \Magento\Sales\Model\Order $order
     *
     * @return \Magento\Sales\Api\Data\InvoiceInterface
     */
    protected function invoice(Order $order) {
        return $order->getInvoiceCollection()->getLastItem();
    }

    /**
     * @param \Magento\Sales\Model\Order       $order
     * @param \Magento\Framework\Phrase|string $message
     */
    protected function invalid(Order $order, $message) {
        $order->addStatusHistoryComment($message);
        $order->save();

    }

    /**
     * @param \Magento\Sales\Model\Order       $order
     * @param \Magento\Framework\Phrase|string $message
     */
    protected function cancel(Order $order, $message) {
        $invoice = $this->invoice($order);
        $invoice->cancel();
        $order->addRelatedObject($invoice);

        $order->registerCancellation($message)->save();
    }

    private function _makeRequest($charge_id) {
        $a_header = [
            "x-api-key:".$this->config->getSecret()
        ];


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->config->getInquiryApiUrl().'/'.$charge_id);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $a_header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);

        return json_decode($response, TRUE);
    }
}