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

namespace CloudCreativity\JsonApi\Validator\Helper;

use CloudCreativity\JsonApi\Contracts\Validator\KeyedValidatorInterface;
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
     * @param ValidatorInterface $validator
     * @return $this
     */
    public function setRelationshipsValidator(ValidatorInterface $validator)
    {
        $this->_relationshipsValidator = $validator;

        return $this;
    }

    /**
     * @return ValidatorInterface
     */
    public function getRelationshipsValidator()
    {
        if (!$this->_relationshipsValidator instanceof ValidatorInterface) {
            $this->_relationshipsValidator = new RelationshipsValidator();
        }

        return $this->_relationshipsValidator;
    }

    /**
     * @return KeyedValidatorInterface
     */
    public function getKeyedRelationships()
    {
        $relationships = $this->getRelationshipsValidator();

        if (!$relationships instanceof KeyedValidatorInterface) {
            throw new \RuntimeException('%s expects relationships validator to be a %s instance.', static::class, KeyedValidatorInterface::class);
        }

        return $relationships;
    }
}