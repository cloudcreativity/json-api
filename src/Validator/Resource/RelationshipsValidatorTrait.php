<?php

namespace CloudCreativity\JsonApi\Validator\Resource;

use CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface;
use CloudCreativity\JsonApi\Validator\Relationships\RelationshipsValidator;

trait RelationshipsValidatorTrait
{

    /**
     * @var ValidatorInterface|null
     */
    protected $_relationshipsValidator;

    /**
     * @var bool
     */
    protected $_expectingRelationships = false;

    /**
     * @param ValidatorInterface $validator
     * @return $this
     */
    public function setRelationshipsValidator(ValidatorInterface $validator)
    {
        $this->_relationshipsValidator = $validator;

        return $this;
    }

    /**
     * @return ValidatorInterface|null
     */
    public function getRelationshipsValidator()
    {
        return $this->_relationshipsValidator;
    }

    /**
     * @param $bool
     * @return $this
     */
    public function setExpectingRelationships($bool)
    {
        $this->_expectingRelationships = (bool) $bool;

        return $this;
    }

    /**
     * @return bool
     */
    public function isExpectingRelationships()
    {
        return (bool) $this->_expectingRelationships;
    }

    /**
     * @return RelationshipsValidator
     */
    public function getRelationships()
    {
        if (is_null($this->_relationshipsValidator)) {
            $this->_relationshipsValidator = new RelationshipsValidator();
        }

        if (!$this->_relationshipsValidator instanceof RelationshipsValidator) {
            throw new \RuntimeException(sprintf('Relationships validator is not a %s instance.', RelationshipsValidator::class));
        }

        return $this->_relationshipsValidator;
    }
}