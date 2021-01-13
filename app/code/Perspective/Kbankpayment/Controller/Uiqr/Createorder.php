<?php
namespace Perspective\Kbankpayment\Controller\Uiqr;

use Exception;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Payment\Transaction;
use Perspective\Kbankpayment\Model\Config\Uiqr;

class Createorder extends Action
{
    /**
     * @var string
     */
    const PATH_CART    = 'checkout/cart';
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
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\DB\Transaction $transaction,
        \Psr\Log\LoggerInterface $logger,
        Uiqr $config,
        Session $session
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_invoiceService = $invoiceService;
        $this->_invoiceSender = $invoiceSender;
        $this->_transaction = $transaction;
        $this->config = $config;
        $this->session = $session;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    /**
     * @return void
     */
    public function execute() {
        $content = $this->getRequest()->getContent();
        $data = json_decode($content,TRUE);
        $order_id = $data['order_id'];

        if (! $order_id) {
            return $this->redirect(self::PATH_CART);
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('\Magento\Sales\Model\Order')
                                   ->load($order_id);
        if (! $order) {
            return $this->redirect(self::PATH_CART);
        }

        if (! $payment = $order->getPayment()) {
            $this->invalid($order, __('Cannot retrieve a payment detail from the request. Please contact our support if you have any questions.'));
            return $this->redirect(self::PATH_CART);
        }

        if ($payment->getMethod() !== 'kbankpayment_uiqr') {
            $this->invalid($order, __('Invalid payment method. Please contact our support if you have any questions.'));
            return $this->redirect(self::PATH_CART);
        }

        // $payload['amount'] = number_format($order->getGrandTotal(),2);
        $payload['amount'] = number_format($order->getGrandTotal(),2, '.', '');
        $payload['currency'] = "THB";
        $payload['description'] = "The Next Optical";
        $payload['source_type'] = "qr";
        $payload['reference_order'] = $order->getIncrementId();
        
        $response = $this->_makeRequest($payload);

        $payment->setAdditionalInformation('k_order_id', $response['id']);
        $payment->setAdditionalInformation('source_type', $response['source_type']);
        $payment->setAdditionalInformation('qr_order', $response);
        $payment->save();

        $response['sucess_url'] = self::PATH_SUCCESS;

        if($response['status'] == 'fail'){
            $response['sucess_url'] = '/kbankpayment/msg?order_id='.$order_id;
        }

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

    private function _makeRequest($payload) {
        $a_header = [
            "x-api-key:".$this->config->getSecret(),
            "Content-Type:application/json"
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->config->getOrderApiUrl());
        curl_setopt($ch, CURLOPT_HTTPHEADER, $a_header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);

        return json_decode($response, TRUE);
    }
}