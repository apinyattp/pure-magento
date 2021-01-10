<?php
namespace Perspective\Kbankpayment\Gateway\Validator\Message;

use Perspective\Kbankpayment\Gateway\Validator\Message\Invalid;

class KbankObjectInvalid extends Invalid
{
    /**
     * @var string
     */
    protected $message = 'Transaction has been declined. Please contact administrator';
}
