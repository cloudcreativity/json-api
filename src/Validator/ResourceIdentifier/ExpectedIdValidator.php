<?php

namespace CloudCreativity\JsonApi\Validator\ResourceIdentifier;

use CloudCreativity\JsonApi\Error\ErrorObject;
use CloudCreativity\JsonApi\Validator\AbstractValidator;

class ExpectedIdValidator extends AbstractValidator
{

    const INVALID_VALUE = 'invalid-value';
    const UNEXPECTED_ID = 'unexpected-id';

    /**
     * @var array
     */
    protected $templates = [
        self::INVALID_VALUE => [
            ErrorObject::CODE => self::INVALID_VALUE,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Invalid ID',
            ErrorObject::DETAIL => 'Expecting a non-empty string or integer id member.',
        ],
        self::UNEXPECTED_ID => [
            ErrorObject::CODE => self::UNEXPECTED_ID,
            ErrorObject::STATUS => 409,
            ErrorObject::TITLE => 'Unexpected ID',
            ErrorObject::DETAIL => 'The id received is not expected.',
        ],
    ];

    /**
     * @var int|string|null
     */
    protected $_expected;

    /**
     * @param null $expected
     */
    public function __construct($expected = null)
    {
        if (!is_null($expected)) {
            $this->setExpected($expected);
        }
    }

    /**
     * @param int|string $id
     * @return $this
     */
    public function setExpected($id)
    {
        if (!is_int($id) && !is_string($id)) {
            throw new \InvalidArgumentException('Expecting an integer or string.');
        }

        $this->_expected = $id;

        return $this;
    }

    /**
     * @return int|string
     */
    public function getExpected()
    {
        if (!is_int($this->_expected) && !is_string($this->_expected)) {
            throw new \RuntimeException('No expected id set.');
        }

        return $this->_expected;
    }

    /**
     * @param $id
     * @return bool
     */
    public function isExpected($id)
    {
        return $this->getExpected() == $id;
    }

    /**
     * @param mixed $value
     * @return void
     */
    protected function validate($value)
    {
        if (!is_string($value) && !is_int($value)) {
            $this->error(static::INVALID_VALUE);
        } elseif (!$this->isExpected($value)) {
            $this->error(static::UNEXPECTED_ID);
        }
    }
}
