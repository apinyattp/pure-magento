<?php
namespace Perspective\Kbankpayment\Controller\Direct18;

use Exception;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Payment\Transaction;

class Back extends Action
{
    /**
     * @var string
     */
    const PATH_CART    = 'checkout/cart';
    const PATH_SUCCESS = 'checkout/onepage/success';

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory resultPageFactory
     */

    public function __construct(
        Context $context,
        Session $session
    ) {
        parent::__construct($context);

        $this->session = $session;
    }

    /**
     * @return void
     */
    public function execute() {
        $response = $this->getRequest()->getParam('response');
        if (! $response) {
            $this->messageManager->addErrorMessage(__('There is no response from payment gateway.'));

            return $this->redirect(self::PATH_CART);
        }

        if ($response !== 'approved'){
            $this->messageManager->addErrorMessage(__('Creditcard authorization failed.'));
            $this->session->restoreQuote();

            return $this->redirect(self::PATH_CART);
        }

        return $this->redirect(self::PATH_SUCCESS);
    }

    /**
     * @param  string $path
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function redirect($path) {
        return $this->_redirect($path, ['_secure' => TRUE]);
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
    protected function cancel(Order $order, $message) {
        $invoice = $this->invoice($order);
        $invoice->cancel();
        $order->addRelatedObject($invoice);

        $order->registerCancellation($message)->save();
        $this->messageManager->addErrorMessage($message);
    }

}
?>
