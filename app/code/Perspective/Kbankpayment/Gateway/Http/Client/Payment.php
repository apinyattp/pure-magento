<?php
namespace Perspective\Kbankpayment\Gateway\Http\Client;

use Exception;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Perspective\Kbankpayment\Model\Config\Direct18;
use Psr\Log\LoggerInterface;

class Payment implements ClientInterface
{
    /**
     * Client request status represented to successful request step.
     *
     * @var string
     */
    const PROCESS_STATUS_SUCCESSFUL = 'successful';

    /**
     * Client request status represented to failed request step.
     *
     * @var string
     */
    const PROCESS_STATUS_FAILED = 'failed';

    /**
     * @var Perspective\Kbankpayment\Model\Config\Direct18
     */
    protected $config;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $moduleList;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;
    protected $logger;

    public function __construct(
        Direct18                 $config,
        ModuleListInterface      $moduleList,
        ProductMetadataInterface $productMetadata,
        LoggerInterface $logger
    ) {
        $this->config          = $config;
        $this->moduleList      = $moduleList;
        $this->productMetadata = $productMetadata;
        $this->logger = $logger;
    }

    /**
     * @param  \Magento\Payment\Gateway\Http\TransferInterface $transferObject
     *
     * @return array
     */
    public function placeRequest(TransferInterface $transferObject) {
        try {
            $payload = $transferObject->getBody();
            unset($payload['capture']);
            $request = $this->_makeRequest($payload);

            $response = [
                'object'  => 'kpayment_direct18',
                'status'  => self::PROCESS_STATUS_SUCCESSFUL,
                'data'    => $request,
                'message' => NULL,
            ];
        } catch (Exception $e) {
            $response = [
                'object'  => 'kpayment_direct18',
                'status'  => self::PROCESS_STATUS_FAILED,
                'data'    => NULL,
                'message' => $e->getMessage(),
            ];
        }

        // $this->logger->log("DEBUG",'Direct18.payment', $response); 
        // $this->logger->log("DEBUG",'Direct18.payment.transferObject', $payload); 
        return $response;
    }

    /**
     * @param  \Magento\Payment\Gateway\Http\TransferInterface $transferObject
     *
     * @return array
     */
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
