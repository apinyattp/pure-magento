<?php
namespace Perspective\Kbankpayment\Gateway\Validator;

use Perspective\Kbankpayment\Gateway\Validator\CommandResponseValidator;
use Perspective\Kbankpayment\Gateway\Validator\Message\Invalid;
use Perspective\Kbankpayment\Gateway\Validator\Message\KbankObjectInvalid;
// use Perspective\Kbankpayment\Model\Validator\Payment\AuthorizeResultValidator;
// use Perspective\Kbankpayment\Model\Validator\Payment\CaptureResultValidator;

class ThreeDSecureCommandResponseValidator extends CommandResponseValidator
{

    /**
     * @param  mixed
     *
     * @return mixed
     */
    protected function validateResponse($data) {
        if (! isset($data['object']) || $data['object'] !== 'charge') {
            return new KbankObjectInvalid();
        }

        if ($data['status'] === 'failed') {
            return new Invalid('Payment failed. ' . ucfirst($data['failure_message']) . ', please contact our support if you have any questions.');
        }

        if ($data['status'] === 'success'
            && $data['redirect_url']
        ) {
            return TRUE;
        }

        return TRUE;
        /*
        // Try validate for none 3-D Secure account case before mark as invalid
        if ($data['capture']) {
            $result = (new CaptureResultValidator)->validate($data);
        } else {
            $result = (new AuthorizeResultValidator)->validate($data);
        }

        if ($result === TRUE) {
            return TRUE;
        }
        */
        // return new Invalid('Payment failed, invalid payment status, please contact our support if you have any questions');
    }

}
