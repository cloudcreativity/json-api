<?php

namespace CloudCreativity\JsonApi\Validator\ResourceIdentifier;

use CloudCreativity\JsonApi\Error\ErrorObject;
use CloudCreativity\JsonApi\Validator\AbstractValidator;

class ExpectedTypeValidator extends AbstractValidator
{

    const INVALID_VALUE = 'invalid-value';
    const UNSUPPORTED_TYPE = 'unsupported-type';

    /**
     * @var array
     */
    protected $templates = [
        self::INVALID_VALUE => [
            ErrorObject::CODE => self::INVALID_VALUE,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Invalid Type',
            ErrorObject::DETAIL => 'Expecting a non-empty string type member.',
        ],
        self::UNSUPPORTED_TYPE => [
            ErrorObject::CODE => self::UNSUPPORTED_TYPE,
            ErrorObject::STATUS => 409,
            ErrorObject::TITLE => 'Unsupported Type',
            ErrorObject::DETAIL => 'Received type is not supported: expecting only "%s" resource objects.',
        ],
    ];

    /**
     * @var string|null
     */
    protected $_expected;

    /**
     * @param string $type
     * @return $this
     */
    public function setExpected($type)
    {
        if (!is_string($type) || empty($type)) {
            throw new \InvalidArgumentException('Expecting a non-empty string expected type.');
        }

        $this->_expected = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getExpected()
    {
        if (!is_string($this->_expected)) {
            throw new \RuntimeException('No expected type set.');
        }

        return $this->_expected;
    }

    /**
     * @param $type
     * @return bool
     */
    public function isExpected($type)
    {
        return $this->getExpected() === $type;
    }

    /**
     * @param mixed $value
     * @return void
     */
    protected function validate($value)
    {
        if (!is_string($value) || empty($value)) {
            $this->error(static::INVALID_VALUE);
        } elseif (!$this->isExpected($value)) {
            $err = $this->error(static::UNSUPPORTED_TYPE);
            $err->setDetail(sprintf($err->getDetail(), $this->getExpected()));
        }
    }
}
