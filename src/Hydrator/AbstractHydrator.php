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

namespace CloudCreativity\JsonApi\Hydrator;

use CloudCreativity\JsonApi\Contracts\Hydrator\HydratorInterface;
use CloudCreativity\JsonApi\Contracts\Object\RelationshipInterface;
use CloudCreativity\JsonApi\Contracts\Object\RelationshipsInterface;
use CloudCreativity\JsonApi\Contracts\Object\ResourceObjectInterface;
use CloudCreativity\Utils\Object\StandardObjectInterface;

/**
 * Class AbstractHydrator
 *
 * @package CloudCreativity\JsonApi
 */
abstract class AbstractHydrator implements HydratorInterface
{

    use HydratesFieldsTrait;

    /**
     * Create a new record.
     *
     * Implementing classes need only implement the logic to transfer the minimum
     * amount of data from the resource that is required to construct a new record
     * instance. The hydrate will then hydrate the object after it has been
     * created.
     *
     * @param ResourceObjectInterface $resource
     * @return object
     */
    abstract protected function createRecord(ResourceObjectInterface $resource);

    /**
     * @param StandardObjectInterface $attributes
     * @param $record
     * @return void
     */
    abstract protected function hydrateAttributes(StandardObjectInterface $attributes, $record);

    /**
     * Persist changes to the record.
     *
     * @param $record
     */
    abstract protected function persist($record);

    /**
     * @inheritdoc
     */
    public function create(ResourceObjectInterface $resource)
    {
        $record = $this->createRecord($resource);
        $this->hydrateAttributes($resource->getAttributes(), $record);
        $this->hydrateRelationships($resource->getRelationships(), $record);
        $this->persist($record);

        return $record;
    }

    /**
     * @inheritdoc
     */
    public function update(ResourceObjectInterface $resource, $record)
    {
        $this->hydrateAttributes($resource->getAttributes(), $record);
        $this->hydrateRelationships($resource->getRelationships(), $record);
        $this->persist($record);

        return $record;
    }

    /**
     * @param RelationshipsInterface $relationships
     * @param $record
     */
    protected function hydrateRelationships(RelationshipsInterface $relationships, $record)
    {
        /** @var RelationshipInterface $relationship */
        foreach ($relationships->getAll() as $key => $relationship) {
            $this->callMethodForField($key, $relationship, $record);
        }
    }
}
