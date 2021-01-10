<?php
namespace Perspective\Kbankpayment\Controller\Response;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Payment\Transaction;

class Response extends Action
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

    public function __construct(
        Context $context,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Magento\Framework\DB\Transaction $transaction
    ) {
        $this->_invoiceService = $invoiceService;
        $this->_invoiceSender = $invoiceSender;
        $this->_transaction = $transaction;
        parent::__construct($context);
    }

    /**
     * @return void
     */
    public function execute() {
        if($this->_valid_ip() === FALSE) return;

        $response = $this->_decode_response();

        $order_id = (int) $response['invoice_no'];
        if (! $order_id) {
            echo "no order id";
            return;
        }

        $currency_code = $response['currency_code'];
        if($currency_code !== '764'){
            return;
        }

        $transamount = (int) $response['transamount'];
        $amount = $transamount / 100.00;

        $order = $objectManager->create('\Magento\Sales\Model\Order')
                                   ->load($order_id);
        //$order = $this->orderRepository->get($order_id);
        if (! $order) {
            echo "no order";
            return;
        }

        if(floatval($order->getGrandTotal()) != floatval($amount)){
            $this->invalid($order, 'Payment error grandtotal '.$amount);
            return;
        }

        if ($order->getState() !== Order::STATE_PENDING_PAYMENT) {
            echo "invalid state";
            return;
        }

        if (! $payment = $order->getPayment()) {
            $this->invalid($order, 'payment not found');
            return;
        }

        if ($payment->getMethod() !== 'kbankpayment_redirect') {
            $this->invalid($order, 'invalid payment');
            return;
        }

        // if (! $order->hasInvoices()) {
        //     $this->cancel($order, __('Cannot create an invoice. Please contact our support to confirm your payment.'));
        //     return;
        // }

        $response_code = $response['response_code'];
        if($response_code !== '00'){
            $this->invalid($order, 'Payment error code '.$response_code);
            return;
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
        echo 'complete';
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
        $conditions = [
            'transcode' => 4,
            'merchant_id' => 15,
            'terminal_id' => 8,
            'shop_no' => 2,
            'currency_code' => 3,
            'invoice_no' => 12,
            'date_transaction' => 8,
            'time_transaction' => 6,
            'card_no' => 19,
            'expired_date' => 4,
            'cv_number' => 4,
            'transamount' => 12,
            'response_code' => 2,
            'approval_code' => 6,
            'card_type' => 3,
            'reference1' => 20,
            // Yellow start
            'plan_id' => 3,
            'pay_mont' => 2,
            'interest_type' => 1,
            'interest_rate' => 6,
            'amount_per' => 9,
            'total_amount' => 12,
            'management_fee' => 5,
            'interest_mode' => 2,
            // Yellow end
            'fx_rate' => 20,
            'thb_amount' => 20,
            'customer_email' => 100,
            'description' => 150,
            'payer_ip_address' => 18,
            'warning_light' => 1,
            'selected_bank' => 60,
            'issuer_bank' => 60,
            'selected_country' => 45,
            'ip_country' => 45,
            'issuer_country' => 45,
            'eci' => 4,
            'xid' => 40,
            'cavv' => 40
        ];
        $response_text = $this->getRequest()->getParam('PMGWRESP2');
        $result = array();
        foreach($conditions as $key => $value) {
            $result[$key] = mb_substr($response_text, 0, $value);
            $response_text = mb_substr($response_text, $value);
        }

        $except_list = array('plan_id','pay_mont','interest_type','interest_rate','amount_per','total_amount','management_fee','interest_mode');
        foreach($except_list as $except_key) {
            unset($result[$except_key]);
        }

        return $result;
    }

    private function valid_ip() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $config = $this->_objectManager->create('Perspective\Kbankpayment\Model\Config\Redirect');

        $ip_address = Mage::helper('core/http')->getRemoteAddr(TRUE);
        $kbank_ip = $config->getBankIP();

        return !empty($kbank_ip) && $ip_address != $kbank_ip;
    }

}
