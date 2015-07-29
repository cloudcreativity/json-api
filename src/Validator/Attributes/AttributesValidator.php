<?php

namespace CloudCreativity\JsonApi\Validator\Attributes;

use CloudCreativity\JsonApi\Error\ErrorObject;
use CloudCreativity\JsonApi\Error\SourceObject;
use CloudCreativity\JsonApi\Validator\AbstractValidator;
use CloudCreativity\JsonApi\Validator\Type\TypeValidator;
use CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface;

class AttributesValidator extends AbstractValidator
{

    const ERROR_INVALID_VALUE = 'invalid-value';
    const ERROR_UNRECOGNISED_ATTRIBUTE = 'not-recognised';
    const ERROR_REQUIRED_ATTRIBUTE = 'required';

    /**
     * @var array
     */
    protected $templates = [
        self::ERROR_INVALID_VALUE => [
            ErrorObject::CODE => self::ERROR_INVALID_VALUE,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Invalid Value',
            ErrorObject::DETAIL => 'Attributes must be an object.',
        ],
        self::ERROR_UNRECOGNISED_ATTRIBUTE => [
            ErrorObject::CODE => self::ERROR_UNRECOGNISED_ATTRIBUTE,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Unrecognised Attribute',
            ErrorObject::DETAIL => 'Attribute key is not recognised and cannot be accepted.',
        ],
        self::ERROR_REQUIRED_ATTRIBUTE => [
            ErrorObject::CODE => self::ERROR_REQUIRED_ATTRIBUTE,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Required Attribute',
            ErrorObject::DETAIL => 'Missing required attribute "%s".',
        ],
    ];

    /**
     * @var array|null
     *      null means any allowed, array means only the supplied keys are allowed.
     */
    protected $_allowed;

    /**
     * @var array|null
     */
    protected $_required;

    /**
     * Validators for use with keys within the attributes.
     *
     * @var array
     */
    protected $_validators = [];

    /**
     * @param array $keys
     * @return $this
     */
    public function setAllowed(array $keys)
    {
        $this->_allowed = $keys;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAllowed($key)
    {
        return is_array($this->_allowed) ? in_array($key, $this->_allowed) : true;
    }

    /**
     * @param array $keys
     * @return $this
     */
    public function setRequired(array $keys)
    {
        $this->_required = $keys;

        return $this;
    }

    /**
     * @return array
     */
    public function getRequired()
    {
        return (array) $this->_required;
    }

    /**
     * @param $key
     * @param ValidatorInterface $validator
     * @return $this
     */
    public function setValidator($key, ValidatorInterface $validator)
    {
        $this->_validators[$key] = $validator;

        return $this;
    }

    /**
     * @param $key
     * @return ValidatorInterface
     */
    public function getValidator($key)
    {
        if (!isset($this->_validators[$key])) {
            $this->_validators[$key] = new TypeValidator();
        }

        return $this->_validators[$key];
    }

    /**
     * @param $value
     */
    protected function validate($value)
    {
        if (!is_object($value)) {
            $this->error(static::ERROR_INVALID_VALUE);
            return;
        }

        // Check provided keys.
        foreach (get_object_vars($value) as $key => $v) {
            $this->checkKey($key)
                ->checkValue($key, $v);
        }

        // Check that required keys exist.
        foreach ($this->getRequired() as $key) {

            if (!isset($value->{$key})) {
                $err = $this->error(static::ERROR_REQUIRED_ATTRIBUTE);
                $err->setDetail(sprintf($err->getDetail(), $key));
            }
        }
    }

    /**
     * @param $key
     * @return $this
     */
    protected function checkKey($key)
    {
        $pointer = '/' . $key;

        if (!$this->isAllowed($key)) {
            $this->error(static::ERROR_UNRECOGNISED_ATTRIBUTE)
                ->setSource([
                    SourceObject::POINTER => $pointer,
                ]);
        }

        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    protected function checkValue($key, $value)
    {
        $validator = $this->getValidator($key);

        if ($validator->isValid($value)) {
            return $this;
        }

        $errors = $validator
            ->getErrors()
            ->setSourcePointer(function ($current) use ($key) {
                return '/' . $key . $current;
            });

        $this->getErrors()->merge($errors);

        return $this;
    }

}
