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

use CloudCreativity\JsonApi\Contracts\Object\RelationshipsInterface;
use CloudCreativity\JsonApi\Contracts\Object\ResourceInterface;
use CloudCreativity\JsonApi\Contracts\Validators\RelationshipsValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\RelationshipValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\ValidatorErrorFactoryInterface;
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
     * @param ValidatorErrorFactoryInterface $errorFactory
     * @param ValidatorFactoryInterface $factory
     */
    public function __construct(ValidatorErrorFactoryInterface $errorFactory, ValidatorFactoryInterface $factory)
    {
        parent::__construct($errorFactory);
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
     * @param callable|null $acceptable
     *      if a non-empty relationship that exists, is it acceptable?
     * @return $this
     */
    public function hasOne(
        $key,
        $expectedType = null,
        $required = false,
        $allowEmpty = true,
        callable $acceptable = null
    ) {
        $expectedType = $expectedType ?: $key;

        $this->add($key, $this->factory->hasOne(
            $expectedType,
            $allowEmpty,
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
     * @param callable|null $acceptable
     *      if an identifier exists, is it acceptable within this relationship?
     * @return $this
     */
    public function hasMany(
        $key,
        $expectedType = null,
        $required = false,
        $allowEmpty = false,
        callable $acceptable = null
    ) {
        $expectedType = $expectedType ?: $key;

        $this->add($key, $this->factory->hasMany(
            $expectedType,
            $allowEmpty,
            $acceptable
        ));

        if ($required) {
            $this->required[] = $key;
        }

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
        $relationships = $resource->relationships();
        $valid = true;

        if (!$this->validateRequired($relationships)) {
            $valid = false;
        }

        foreach ($relationships->keys() as $key) {
            if (!$this->validateRelationship($key, $relationships, $resource)) {
                $valid = false;
            }
        }

        return $valid;
    }

    /**
     * @param $key
     * @return RelationshipValidatorInterface|null
     */
    protected function get($key)
    {
        return isset($this->stack[$key]) ? $this->stack[$key] : null;
    }

    /**
     * @param RelationshipsInterface $relationships
     * @return bool
     */
    protected function validateRequired(RelationshipsInterface $relationships)
    {
        $valid = true;

        foreach ($this->required as $key) {

            if (!$relationships->has($key)) {
                $this->addError($this->errorFactory->memberRequired(
                    $key,
                    $this->getPathToRelationships()
                ));
                $valid = false;
            }
        }

        return $valid;
    }

    /**
     * @param $key
     * @param RelationshipsInterface $relationships
     * @param ResourceInterface $resource
     * @return bool
     */
    protected function validateRelationship(
        $key,
        RelationshipsInterface $relationships,
        ResourceInterface $resource
    ) {
        if (!is_object($relationships->get($key))) {
            $this->addError($this->errorFactory->memberObjectExpected(
                $key,
                $this->getPathToRelationship($key)
            ));
            return false;
        }

        $validator = $this->get($key);
        $relationship = $relationships->rel($key);

        if ($validator && !$validator->isValid($relationship, $key, $resource)) {
            $this->addErrors($validator->errors());
            return false;
        }

        return true;
    }
}
