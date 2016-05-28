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

namespace CloudCreativity\JsonApi\Contracts\Hydrator;

use CloudCreativity\JsonApi\Contracts\Object\RelationshipInterface;
use CloudCreativity\JsonApi\Contracts\Object\ResourceInterface;

interface HydratorInterface
{

    /**
     * Transfer data from a resource to a record.
     *
     * @param ResourceInterface $resource
     * @param object $record
     * @return void
     */
    public function hydrate(ResourceInterface $resource, $record);

    /**
     * Transfer data from a resource relationship to a record.
     *
     * @param $relationshipKey
     *      the key of the relationship to hydrate.
     * @param RelationshipInterface $relationship
     *      the relationship object to use for the hydration.
     * @param object $record
     *      the object to hydrate.
     * @return object
     *      the hydrated object
     */
    public function hydrateRelationship($relationshipKey, RelationshipInterface $relationship, $record);
}
