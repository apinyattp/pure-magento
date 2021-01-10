<?php
namespace Perspective\Kbankpayment\Model;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\PaymentException;
use Magento\Framework\Exception\SessionException;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\UrlInterface;
use Perspective\Kbankpayment\Model\Config\Direct18 as Config;
use Perspective\Kbankpayment\Api\UrlInformationInterface;

class UrlInformation implements UrlInformationInterface
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
     * @var \Perspective\Kbankpayment\Model\Config\Direct18
     */
    protected $config;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    public function __construct(Session $session, Config $config, UrlInterface $urlBuilder) {
        $this->session  = $session;
        $this->config   = $config;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param  int $order_id
     *
     * @return string
     */
    public function getUrl($order_id) {
        if (!($payment = $this->loadOrder($order_id)->getPayment())) {
            throw new PaymentException(__('Cannot retrieve a payment detail from the request, please contact our support if you have any questions'));
        }

        $charge_status = $payment->getAdditionalInformation('charge_status');
        $transaction_state = $payment->getAdditionalInformation('transaction_state');

        if($transaction_state == 'Authorized' && $charge_status == 'success'){
            $url = $this->urlBuilder->getUrl(self::PATH_SUCCESS);
        }elseif($transaction_state == 'Pre-Authorized' && $charge_status == 'success'){
            $url = $payment->getAdditionalInformation('charge_authen_url');
        }else{
            $url = $this->urlBuilder->getUrl(self::PATH_CART);
        }

        return $url;
    }

    /**
     * @param  int $id
     *
     * @return \Magento\Sales\Model\Order
     */
    protected function loadOrder($id) {
        // Note, $order->getId(); will return a string, not int.
        $order = $this->session->getLastRealOrder();

        if (! $order->getId()) {
            throw new SessionException(__('The order session no longer exists, please make an order again or contact our support if you have any questions.'));
        }

        if ($id != $order->getId()) {
            throw new AuthorizationException(__('This request is not authorized to access the resource, please contact our support if you have any questions'));
        }

        return $order;
    }
}
