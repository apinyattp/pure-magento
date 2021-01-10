<?php
namespace Perspective\Kbankpayment\Model\Data;

use Perspective\Kbankpayment\Api\Data\CardInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

class Card extends AbstractExtensibleObject implements CardInterface
{

    /**
     * @api
     *
     * @param  string $value
     *
     * @return self
     */
    public function setName($value) {
        return $this->setData(self::NAME, $value);
    }

    /**
     * @api
     *
     * @param  string $value
     *
     * @return self
     */
    public function setNumber($value) {
        return $this->setData(self::NUMBER, $value);
    }

    /**
     * @api
     *
     * @param  int $value
     *
     * @return self
     */
    public function setMonth($value) {
        return $this->setData(self::MONTH, $value);
    }

    /**
     * @api
     *
     * @param  int $value
     *
     * @return self
     */
    public function setYear($value) {
        return $this->setData(self::YEAR, $value);
    }

    /**
     * @api
     *
     * @param  string $value
     *
     * @return self
     */
    public function setCvv($value) {
        return $this->setData(self::CVV, $value);
    }

    /**
     * Always return a "token" string.
     *
     * @api
     *
     * @return string
     */
    public function getObject() {
        return 'token';
    }

    /**
     * @api
     *
     * @return string|null
     */
    public function getName() {
        return $this->_get(self::NAME);
    }

    /**
     * @api
     *
     * @return string|null
     */
    public function getNumber() {
        return $this->_get(self::NUMBER);
    }

    /**
     * @api
     *
     * @return int|null
     */
    public function getMonth() {
        return $this->_get(self::MONTH);
    }

    /**
     * @api
     *
     * @return int|null
     */
    public function getYear() {
        return $this->_get(self::YEAR);
    }

    /**
     * @api
     *
     * @return string|null
     */
    public function getCvv() {
        return $this->_get(self::CVV);
    }

}
