<?php
namespace Perspective\Kbankpayment\Api;

interface UrlInformationInterface
{

    /**
     * @param  int $order_id
     *
     * @return string
     */
    public function getUrl($order_id);

}
