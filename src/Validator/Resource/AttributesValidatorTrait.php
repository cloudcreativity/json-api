<?php

namespace CloudCreativity\JsonApi\Validator\Resource;

use CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface;
use CloudCreativity\JsonApi\Validator\Attributes\AttributesValidator;

trait AttributesValidatorTrait
{

    /**
     * @var ValidatorInterface|null
     */
    protected $_attributesValidator;

    /**
     * @var bool
     */
    protected $_expectingAttributes = true;

    /**
     * @param ValidatorInterface $validator
     * @return $this
     */
    public function setAttributesValidator(ValidatorInterface $validator)
    {
        $this->_attributesValidator = $validator;

        return $this;
    }

    /**
     * @return ValidatorInterface|null
     */
    public function getAttributesValidator()
    {
        return $this->_attributesValidator;
    }

    /**
     * @param $bool
     * @return $this
     */
    public function setExpectingAttributes($bool)
    {
        $this->_expectingAttributes = (bool) $bool;

        return $this;
    }

    /**
     * @return bool
     */
    public function isExpectingAttributes()
    {
        return (bool) $this->_expectingAttributes;
    }

    /**
     * @return AttributesValidator
     */
    public function getAttributes()
    {
        if (is_null($this->_attributesValidator)) {
            $this->_attributesValidator = new AttributesValidator();
        }

        if (!$this->_attributesValidator instanceof AttributesValidator) {
            throw new \RuntimeException(sprintf('Attributes validator is not a %s instance.', AttributesValidator::class));
        }

        return $this->_attributesValidator;
    }
}