<?php
namespace Perspective\Kbankpayment\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class Direct18InvoiceObserver implements ObserverInterface
{
    /**
     * @inheritdoc
     */
    /**
     * @var \Magento\Sales\Model\Service\InvoiceService
     */
    protected $_invoiceService;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\InvoiceSender
     */
    protected $_invoiceSender;
    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $_transaction;
    protected $_orderFactory;
    protected $logger;

    public function __construct(
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        LoggerInterface $logger
    ) {
        $this->_invoiceService = $invoiceService;
        $this->_invoiceSender = $invoiceSender;
        $this->_transaction = $transaction;
        $this->_orderFactory = $orderFactory;
        $this->logger = $logger;
    }

    public function execute(Observer $observer) {
        // $this->logger->log("DEBUG",'Direct18InvoicenObserver', array("msg"=>"hello")); 

        $orderIds = $observer->getEvent()->getOrderIds();
        
        if (count($orderIds)) {
            $orderId = $orderIds[0];            
            $order = $this->_orderFactory->create()->load($orderId);
            $payment = $order->getPayment();

            if ($payment->getMethod() !== 'kbankpayment_direct18') {
                $this->logger->log("DEBUG",'Direct18InvoicenObserver', array("msg"=>"error_getMethod")); 
                return;
            }

            if(!$payment->getAdditionalInformation('transaction_state')){
                $this->logger->log("DEBUG",'Direct18InvoicenObserver', array("msg"=>"error_transaction_state"));
                return;
            }

            if($payment->getAdditionalInformation('transaction_state') != 'Authorized'){
                $this->logger->log("DEBUG",'Direct18InvoicenObserver', array("msg"=>"error_transaction_state_unauthorized"));
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
        }
    }
}
