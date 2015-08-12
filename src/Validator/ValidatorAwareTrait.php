<?php

namespace CloudCreativity\JsonApi\Validator;

use CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface;

trait ValidatorAwareTrait
{

    /**
     * @var ValidatorInterface|null
     */
    protected $_validator;

    /**
     * @param ValidatorInterface $validator
     * @return $this
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->_validator = $validator;

        return $this;
    }

    /**
     * @return ValidatorInterface
     */
    public function getValidator()
    {
        if (!$this->_validator instanceof ValidatorInterface) {
            throw new \RuntimeException(sprintf('%s expects to be injected with a %s instance.', static::class, ValidatorInterface::class));
        }

        return $this->_validator;
    }
}