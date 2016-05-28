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

namespace CloudCreativity\JsonApi\Contracts\Validators;

interface ValidatorFactoryInterface
{

    /**
     * Create a validator for a document containing a resource in its data member.
     *
     * @param ResourceValidatorInterface $resource
     *      the validator to use for the data member.
     * @return DocumentValidatorInterface
     */
    public function resourceDocument(ResourceValidatorInterface $resource);

    /**
     * Create a validator for a document containing a relationship in its data member.
     *
     * @param RelationshipValidatorInterface $relationship
     *      the validator to use for the data member.
     * @return DocumentValidatorInterface
     */
    public function relationshipDocument(RelationshipValidatorInterface $relationship);

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
    );

    /**
     * Create a validator for a relationships object.
     *
     * @return RelationshipsValidatorInterface
     */
    public function relationships();

    /**
     * Create a relationship validator for a has-one relationship.
     *
     * @param string|string[] $expectedType
     *      the expected type or types
     * @param bool $allowEmpty
     *      is an empty has-one relationship acceptable?
     * @param AcceptRelatedResourceInterface|callable|null $acceptable
     *      if a non-empty relationship that exists, is it acceptable?
     * @return RelationshipValidatorInterface
     */
    public function hasOne(
        $expectedType,
        $allowEmpty = true,
        $acceptable = null
    );

    /**
     * Create a relationship validator for a has-many relationship.
     *
     * @param string|string[] $expectedType
     *      the expected type or types.
     * @param bool $allowEmpty
     *      is an empty has-many relationship acceptable?
     * @param AcceptRelatedResourceInterface|callable|null $acceptable
     *      if an identifier exists, is it acceptable within this relationship?
     * @return RelationshipValidatorInterface
     */
    public function hasMany(
        $expectedType,
        $allowEmpty = false,
        $acceptable = null
    );
}
