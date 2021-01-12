<?php
namespace Perspective\Kbankpayment\Controller\Uibutton;

use Exception;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Payment\Transaction;
use Perspective\Kbankpayment\Model\Config\Uibutton;

class Charge extends Action
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
     * @var Perspective\Kbankpayment\Model\Config\Uibutton
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
        Uibutton $config,
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
        print_r($data);
        $token = $data['token'];
        $order_id = $data['order_id'];

        // $token = 'tokennnnnn';
        // $order_id = '62';

        if (! $order_id) {
            return $this->redirect(self::PATH_CART);
        }

        if (! $token) {
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

        if ($payment->getMethod() !== 'kbankpayment_uibutton') {
            $this->invalid($order, __('Invalid payment method. Please contact our support if you have any questions.'));
            return $this->redirect(self::PATH_CART);
        }

        // $payload['amount'] = number_format($order->getGrandTotal(),2);
        $payload['amount'] = number_format($order->getGrandTotal(),2, '.', '');
        $payload['currency'] = "THB";
        $payload['description'] = "The Next Optical";
        $payload['source_type'] = "card";
        $payload['mode'] = "token";
        $payload['reference_order'] = $order->getIncrementId();
        $payload['token'] = $token;
        
        $response = $this->_makeRequest($payload);

        $payment->setAdditionalInformation('charge_id', $response['id']);
        $payment->setAdditionalInformation('charge_authen_url', $response['redirect_url']);
        
        $payment->setAdditionalInformation('charge_status', $response['status']);
        $payment->setAdditionalInformation('transaction_state', $response['transaction_state']);
        $payment->setAdditionalInformation('charge_data', $response);
        $payment->save();

        $response['sucess_url'] = self::PATH_SUCCESS;

        if($response['transaction_state'] == 'Authorized' && $response['status'] == 'success'){
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
        }else if($response['status'] == 'fail'){
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

        curl_setopt($ch, CURLOPT_URL, $this->config->getChargeApiUrl());
        curl_setopt($ch, CURLOPT_HTTPHEADER, $a_header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);

        return json_decode($response, TRUE);
    }
}