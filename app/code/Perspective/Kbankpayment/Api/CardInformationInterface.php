<?php
namespace Perspective\Kbankpayment\Api;
use Perspective\Kbankpayment\Api\Data\CardInterface;

interface CardInformationInterface
{

    /**
     * @param Perspective\Kbankpayment\Api\Data\CardInterface $card
     * @return string
     */
    public function getToken(CardInterface $card);

}
