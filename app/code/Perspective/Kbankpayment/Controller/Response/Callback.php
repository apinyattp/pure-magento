<?php
namespace Perspective\Kbankpayment\Controller\Response;

use Exception;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Payment\Transaction;
use Perspective\Kbankpayment\Model\Config\Direct18;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;

class Callback extends Action implements CsrfAwareActionInterface
{
    /**
     * @var string
     */
    const PATH_CART    = 'checkout/cart';
    const PATH_SUCCESS = 'checkout/onepage/success';

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
     * @var Perspective\Kbankpayment\Model\Config\Direct18
     */
    protected $config;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $session;
    protected $_logger;

    public function __construct(
        Context $context,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Magento\Framework\DB\Transaction $transaction,
        \Psr\Log\LoggerInterface $logger,
        Direct18 $config,
        Session $session
    ) {
        $this->_invoiceService = $invoiceService;
        $this->_invoiceSender = $invoiceSender;
        $this->_transaction = $transaction;
        $this->config = $config;
        $this->session = $session;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    public function createCsrfValidationException(RequestInterface $request): ? InvalidRequestException
    {
        return null;
    }
        
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * @return void
     */
    public function execute() {
        $response = $this->_decode_response();
        $charge_id = $response['objectId'];

        $inquiry = $this->_makeRequest($charge_id);
        echo $inquiry['reference_order'];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('\Magento\Sales\Model\Order')->load($inquiry['reference_order']);
var_dump($order->getPayment());die();
        if (! $payment = $order->getPayment()) {
            $this->invalid($order, __('Cannot retrieve a payment detail from the request. Please contact our support if you have any questions.'));
            return $this->redirect(self::PATH_CART);
        }

        $payment->setAdditionalInformation('cc_charge', $inquiry);

        if($inquiry['transaction_state'] !== 'Authorized'){
            $this->messageManager->addErrorMessage(__('The transaction state is not authorized, please make an order again or contact our support if you have any questions.'));
            return $this->redirect(self::PATH_CART);
        }
        $payment->setAdditionalInformation('transaction_state', $inquiry['transaction_state']);
       
        if (! $order->getId()) {
            $this->messageManager->addErrorMessage(__('The order session no longer exists, please make an order again or contact our support if you have any questions.'));
            return $this->redirect(self::PATH_CART);
        }
        
        if (!in_array($payment->getMethod(), array('kbankpayment_direct18','kbankpayment_uibutton','kbankpayment_uibuttonterm'))) {
            $this->invalid($order, __('Invalid payment method. Please contact our support if you have any questions.'));
            return $this->redirect(self::PATH_CART);
        }
       
        if (! $charge_id = $payment->getAdditionalInformation('charge_id')) {
            $this->cancel($order, __('Cannot retrieve a charge reference id. Please contact our support to confirm your payment.'));
            $this->session->restoreQuote();
            return $this->redirect(self::PATH_CART);
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

        return $this->redirect(self::PATH_SUCCESS);
    }
    public function execute22222() {

        $transamount = (int) $response['transamount'];
        $amount = $transamount / 100.00;
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

    private function _decode_response() {
        $result = $this->getRequest()->getPost();
        return $result;
    }

    private function _makeRequest($charge_id) {
        $a_header = [
            "x-api-key:".$this->config->getSecret()
        ];


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->config->getChargeApiUrl().'/'.$charge_id);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $a_header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);

        return json_decode($response, TRUE);
    }
}