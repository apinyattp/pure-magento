<?php
namespace Perspective\Kbankpayment\Model;

use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\PaymentException;
use Magento\Framework\Exception\SessionException;
use Magento\Framework\HTTP\Client\Curl;
use Perspective\Kbankpayment\Model\Config\Direct18 as Config;
use Perspective\Kbankpayment\Api\CardInformationInterface;
use Perspective\Kbankpayment\Api\Data\CardInterface;

class CardInformation implements CardInformationInterface
{
    /**
     * @var \Perspective\Kbankpayment\Model\Config\Direct18
     */
    protected $config;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $curl;

    public function __construct(Config $config, Curl $curl) {
        $this->config = $config;
        $this->curl = $curl;
    }

    /**
     * @param Perspective\Kbankpayment\Api\Data\CardInterface $card
     * @return string
     */
    public function getToken(CardInterface $card) {

        $a_header = [
            "x-api-key:".$this->config->getPublic(),
            "Content-Type:application/json"
        ];

        $payload = [
            'mode' => 'fullpan',
            'card' => [
                'name' => $card->getName(),
                'number' => $card->getNumber(),
                'expmonth' => $card->getMonth(),
                'expyear' => $card->getYear(),
                'cvv' => $card->getCvv(),
            ],
        ];
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->config->getTokenApiUrl());
        curl_setopt($ch, CURLOPT_HTTPHEADER, $a_header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);

        $a_resp = json_decode($response, TRUE);

        if($a_resp['object'] == 'error') throw new PaymentException($a_resp['message']);
        if($a_resp['object'] !== 'token') throw new PaymentException(__('Cannot retrieve a token detail from the request, please contact our support if you have any questions'));

        return $a_resp['id'];
    }

}
