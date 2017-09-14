<?php

/**
 * Copyright 2017 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Contracts\Hydrator;

use CloudCreativity\JsonApi\Contracts\Object\RelationshipInterface;
use CloudCreativity\JsonApi\Contracts\Object\ResourceObjectInterface;
use CloudCreativity\JsonApi\Exceptions\RuntimeException;

/**
 * Interface HydratorInterface
 *
 * Hydrators are responsible for transferring data from JSON API resource and relationship objects
 * into the domain records that they represent, and persisting the changes.
 *
 * @package CloudCreativity\JsonApi
 */
interface HydratorInterface
{

    /**
     * Create a domain record using data from the supplied resource object.
     *
     * @param ResourceObjectInterface $resource
     * @return object
     *      the created domain record.
     */
    public function create(ResourceObjectInterface $resource);

    /**
     * Update a domain record with data from the supplied resource object.
     *
     * @param ResourceObjectInterface $resource
     * @param object $record
     *      the domain record to update.
     * @return object
     *      the updated domain record.
     */
    public function update(ResourceObjectInterface $resource, $record);

    /**
     * Update a domain record's relationship with data from the supplied relationship object.
     *
     * For a has-one relationship, this changes the relationship to match the supplied relationship
     * object.
     *
     * For a has-many relationship, this completely replaces every member of the relationship, changing
     * it to match the supplied relationship object.
     *
     * @param $relationshipKey
     *      the key of the relationship to hydrate.
     * @param RelationshipInterface $relationship
     *      the relationship object to use for the hydration.
     * @param object $record
     *      the object to hydrate.
     * @return object
     *      the updated domain record.
     */
    public function updateRelationship($relationshipKey, RelationshipInterface $relationship, $record);

    /**
     * Add data to a domain record's relationship using data from the supplied relationship object.
     *
     * For a has-many relationship, this adds the resource identifiers in the relationship to the domain
     * record's relationship. It is not valid for a has-one relationship.
     *
     * @param $relationshipKey
     * @param RelationshipInterface $relationship
     * @param object $record
     *      the domain record to update.
     * @return object
     *      the updated domain record.
     * @throws RuntimeException
     *      if the relationship object is a has-one relationship.
     */
    public function addToRelationship($relationshipKey, RelationshipInterface $relationship, $record);

    /**
     * Remove data from a domain record's relationship using data from the supplied relationship object.
     *
     * For a has-many relationship, this removes the resource identifiers in the relationship from the
     * domain record's relationship. It is not valid for a has-one relationship.
     *
     * @param $relationshipKey
     * @param RelationshipInterface $relationship
     * @param object $record
     *      the domain record to update.
     * @return object
     *      the updated domain record.
     * @throws RuntimeException
     *      if the relationship object is a has-one relationship.
     */
    public function removeFromRelationship($relationshipKey, RelationshipInterface $relationship, $record);

}
