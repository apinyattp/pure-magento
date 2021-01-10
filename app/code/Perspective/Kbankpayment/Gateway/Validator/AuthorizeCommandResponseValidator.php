<?php
namespace Perspective\Kbankpayment\Gateway\Validator;

use Perspective\Kbankpayment\Gateway\Validator\CommandResponseValidator;
use Perspective\Kbankpayment\Gateway\Validator\Message\Invalid;
use Perspective\Kbankpayment\Gateway\Validator\Message\KbankObjectInvalid;

class AuthorizeCommandResponseValidator extends CommandResponseValidator
{
    /**
     * @param  mixed
     *
     * @return mixed
     */
    protected function validateResponse($data)
    {
        if (! isset($data['object']) || $data['object'] !== 'charge') {
            return new KbankObjectInvalid();
        }
        
        if ($data['status'] === 'fail') {
            return new Invalid('Payment failed. ' . ucfirst($data['failure_message']) . ', please contact our support if you have any questions.');
        }
        // return true;
        if ($data['status'] === 'success' && $data['transaction_state'] == 'Authorized'){
            return true;
        }
        if ($data['status'] === 'success' && $data['transaction_state'] == 'Pre-Authorized'){
            return true;
        }

        return new Invalid('Payment failed, invalid payment status, please contact our support if you have any questions');
    }
}
