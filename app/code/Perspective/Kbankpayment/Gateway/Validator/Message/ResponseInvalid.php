<?php
namespace Perspective\Kbankpayment\Gateway\Validator\Message;

use Perspective\Kbankpayment\Gateway\Validator\Message\Invalid;

class ResponseInvalid extends Invalid
{
    /**
     * @var string
     */
    protected $message = 'Couldn\'t retrieve charge transaction. Please contact administrator.';
}
