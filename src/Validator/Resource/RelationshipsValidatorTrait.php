<?php

/**
 * Copyright 2015 Cloud Creativity Limited
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace CloudCreativity\JsonApi\Validator\Resource;

use CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface;
use CloudCreativity\JsonApi\Validator\Relationships\RelationshipsValidator;

/**
 * Class RelationshipsValidatorTrait
 * @package CloudCreativity\JsonApi
 */
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