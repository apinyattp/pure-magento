<?php
namespace Perspective\Kbankpayment\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Perspective\Kbankpayment\Gateway\Validator\Message\Invalid;
use Perspective\Kbankpayment\Gateway\Validator\Message\ResponseInvalid;

class CommandResponseValidator extends AbstractValidator
{

    /**
     * Performs domain-related validation for business object
     *
     * @param  array $validationSubject
     *
     * @return ResultInterface
     */
    public function validate(array $validationSubject) {
        if (! isset($validationSubject['response']) || $validationSubject['response']['object'] !== 'kpayment_direct18') {
            return $this->failed((new ResponseInvalid)->getMessage());
        }

        if ($validationSubject['response']['status'] === 'fail') {
            return $this->failed((new Invalid($validationSubject['response']['message']))->getMessage());
        }

        $result = $this->validateResponse($validationSubject['response']['data']);
        if ($result instanceof Invalid) {
            return $this->failed($result->getMessage());
        }

        return $this->createResult(TRUE, []);
    }

    /**
     * @param  \Magento\Framework\Phrase|string $message
     *
     * @return \Magento\Payment\Gateway\Validator\ResultInterface
     */
    protected function failed($message) {
        return $this->createResult(FALSE, [ $message ]);
    }

    /**
     * @param  mixed $data
     *
     * @return mixed
     */
    protected function validateResponse($data) {
        return TRUE;
    }

}
