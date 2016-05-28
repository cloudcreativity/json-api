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

use CloudCreativity\JsonApi\Contracts\Store\StoreInterface;
use CloudCreativity\JsonApi\Contracts\Validators\AttributesValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\DocumentValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\RelationshipsValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\RelationshipValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\ResourceValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\ValidationMessageFactoryInterface;
use CloudCreativity\JsonApi\Contracts\Validators\ValidatorFactoryInterface;

class ValidatorFactory implements ValidatorFactoryInterface
{

    /**
     * @var ValidationMessageFactoryInterface
     */
    private $messages;

    /**
     * @var StoreInterface
     */
    private $store;

    /**
     * ValidatorFactory constructor.
     * @param ValidationMessageFactoryInterface $messages
     * @param StoreInterface $store
     */
    public function __construct(
        ValidationMessageFactoryInterface $messages,
        StoreInterface $store
    ) {
        $this->messages = $messages;
        $this->store = $store;
    }

    /**
     * Create a validator for a document containing a resource in its data member.
     *
     * @param ResourceValidatorInterface $resource
     *      the validator to use for the data member.
     * @return DocumentValidatorInterface
     */
    public function resourceDocument(ResourceValidatorInterface $resource)
    {
        return new ResourceDocumentValidator($this->messages, $resource);
    }

    /**
     * Create a validator for a document containing a relationship in its data member.
     *
     * @param RelationshipValidatorInterface $relationship
     *      the validator to use for the data member.
     * @return DocumentValidatorInterface
     */
    public function relationshipDocument(RelationshipValidatorInterface $relationship)
    {
        return new RelationshipDocumentValidator($this->messages, $relationship);
    }

    /**
     * Create a validator for a resource object.
     *
     * @param $expectedType
     *      the expected resource type.
     * @param string|int|null $expectedId
     *      the expected resource id, or null if none expected (create request).
     * @param AttributesValidatorInterface|null $attributes
     *      the validator to use for the attributes member.
     * @param RelationshipsValidatorInterface|null $relationships
     *      the validator to use for the relationships member.
     * @return ResourceValidatorInterface
     */
    public function resource(
        $expectedType,
        $expectedId = null,
        AttributesValidatorInterface $attributes = null,
        RelationshipsValidatorInterface $relationships = null
    ) {
        return new ResourceValidator(
            $this->messages,
            $expectedType,
            $expectedId,
            $attributes,
            $relationships
        );
    }

    /**
     * Create a validator for a relationships object.
     *
     * @return RelationshipsValidatorInterface
     */
    public function relationships()
    {
        return new RelationshipsValidator($this->messages, $this);
    }

    /**
     * Create a relationship validator for a has-one relationship.
     *
     * @param string|string[] $expectedType
     *      the expected type or types
     * @param bool $allowEmpty
     *      is an empty has-one relationship acceptable?
     * @param callable|null $acceptable
     *      if a non-empty relationship that exists, is it acceptable?
     * @return RelationshipValidatorInterface
     */
    public function hasOne(
        $expectedType,
        $allowEmpty = true,
        callable $acceptable = null
    ) {
        return new HasOneValidator(
            $this->messages,
            $this->store,
            $expectedType,
            $allowEmpty,
            $acceptable
        );
    }

    /**
     * Create a relationship validator for a has-many relationship.
     *
     * @param $expectedType
     *      the expected type or types.
     * @param bool $allowEmpty
     *      is an empty has-many relationship acceptable?
     * @param callable|null $acceptable
     *      if an identifier exists, is it acceptable within this relationship?
     * @return RelationshipValidatorInterface
     */
    public function hasMany(
        $expectedType,
        $allowEmpty = false,
        callable $acceptable = null
    ) {
        return new HasManyValidator(
            $this->messages,
            $this->store,
            $expectedType,
            $allowEmpty,
            $acceptable
        );
    }


}
