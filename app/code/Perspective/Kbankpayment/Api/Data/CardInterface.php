<?php
namespace Perspective\Kbankpayment\Api\Data;

interface CardInterface
{
    // Fields
    const TYPE     = 'object';
    const NAME     = 'name';
    const NUMBER   = 'number';
    const MONTH    = 'month';
    const YEAR     = 'year';
    const CVV      = 'cvv';

    /**
     * @api
     *
     * @param  string $value
     *
     * @return self
     */
    public function setName($value);

    /**
     * @api
     *
     * @param  string $value
     *
     * @return self
     */
    public function setNumber($value);

    /**
     * @api
     *
     * @param  int $value
     *
     * @return self
     */
    public function setMonth($value);

    /**
     * @api
     *
     * @param  int $value
     *
     * @return self
     */
    public function setYear($value);

    /**
     * @api
     *
     * @param  string $value
     *
     * @return self
     */
    public function setCvv($value);

    /**
     * Always return a "card" string.
     *
     * @api
     *
     * @return string
     */
    public function getObject();

    /**
     * @api
     *
     * @return string|null
     */
    public function getName();

    /**
     * @api
     *
     * @return string|null
     */
    public function getNumber();

    /**
     * @api
     *
     * @return int|null
     */
    public function getMonth();

    /**
     * @api
     *
     * @return int|null
     */
    public function getYear();

    /**
     * @api
     *
     * @return string|null
     */
    public function getCvv();

}
