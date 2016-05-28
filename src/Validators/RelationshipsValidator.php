<?php

/**
 * Copyright 2016 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Validators;

use CloudCreativity\JsonApi\Contracts\Object\ResourceInterface;
use CloudCreativity\JsonApi\Contracts\Validators\RelationshipsValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\RelationshipValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\ValidationMessageFactoryInterface;
use CloudCreativity\JsonApi\Contracts\Validators\ValidatorFactoryInterface;

class RelationshipsValidator extends AbstractValidator implements RelationshipsValidatorInterface
{

    /**
     * @var ValidatorFactoryInterface
     */
    private $factory;

    /**
     * @var array
     */
    private $stack = [];

    /**
     * @var string[]
     */
    private $required = [];

    /**
     * RelationshipsValidator constructor.
     * @param ValidationMessageFactoryInterface $messages
     * @param ValidatorFactoryInterface $factory
     */
    public function __construct(ValidationMessageFactoryInterface $messages, ValidatorFactoryInterface $factory)
    {
        parent::__construct($messages);
        $this->factory = $factory;
    }

    /**
     * @param $key
     * @param RelationshipValidatorInterface $validator
     * @return $this
     */
    public function add($key, RelationshipValidatorInterface $validator)
    {
        $this->stack[$key] = $validator;

        return $this;
    }

    /**
     * Add a has-one relationship validator for the specified relationship key.
     *
     * @param string $key
     *      the key of the relationship.
     * @param string|string[]|null $expectedType
     *      the expected type or types. If null, defaults to the key name.
     * @param bool $required
     *      must the relationship exist as a member on the relationship object?
     * @param bool $allowEmpty
     *      is an empty has-one relationship acceptable?
     * @param callable|null $exists
     *      if a non-empty relationship, does the type/id exist?
     * @param callable|null $acceptable
     *      if a non-empty relationship that exists, is it acceptable?
     * @return $this
     */
    public function hasOne(
        $key,
        $expectedType = null,
        $required = false,
        $allowEmpty = true,
        callable $exists = null,
        callable $acceptable = null
    ) {
        $expectedType = $expectedType ?: $key;

        $this->add($key, $this->factory->hasOne(
            $expectedType,
            $allowEmpty,
            $exists,
            $acceptable
        ));

        if ($required) {
            $this->required[] = $key;
        }

        return $this;
    }

    /**
     * Add a has-many relationship validator for the specified relationship key.
     *
     * @param string $key
     *      the key of the relationship.
     * @param string|string[]|null $expectedType
     *      the expected type or types. If null, defaults to the key name.
     * @param bool $required
     *      must the relationship exist as a member on the relationship object?
     * @param bool $allowEmpty
     *      is an empty has-many relationship acceptable?
     * @param callable|null $exists
     *      does the type/id of an identifier within the relationship exist?
     * @param callable|null $acceptable
     *      if an identifier exists, is it acceptable within this relationship?
     * @return $this
     */
    public function hasMany(
        $key,
        $expectedType = null,
        $required = false,
        $allowEmpty = false,
        callable $exists = null,
        callable $acceptable = null
    ) {
        $expectedType = $expectedType ?: $key;

        $this->add($key, $this->factory->hasMany(
            $expectedType,
            $required,
            $allowEmpty,
            $exists,
            $acceptable
        ));

        return $this;
    }

    /**
     * Are the relationships on the supplied resource valid?
     *
     * @param ResourceInterface $resource
     * @return bool
     */
    public function isValid(ResourceInterface $resource)
    {
        // TODO: Implement isValid() method.
    }


}
