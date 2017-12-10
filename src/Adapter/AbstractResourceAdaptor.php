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

namespace CloudCreativity\JsonApi\Adapter;

use CloudCreativity\JsonApi\Contracts\Adapter\ResourceAdapterInterface;
use CloudCreativity\JsonApi\Contracts\Object\RelationshipsInterface;
use CloudCreativity\JsonApi\Contracts\Object\ResourceObjectInterface;
use CloudCreativity\JsonApi\Contracts\Store\StoreAwareInterface;
use CloudCreativity\JsonApi\Store\StoreAwareTrait;
use CloudCreativity\Utils\Object\StandardObjectInterface;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;

/**
 * Class AbstractResourceAdaptor
 *
 * @package CloudCreativity\JsonApi
 */
abstract class AbstractResourceAdaptor implements ResourceAdapterInterface, StoreAwareInterface
{

    use StoreAwareTrait;

    /**
     * Create a new record.
     *
     * Implementing classes need only implement the logic to transfer the minimum
     * amount of data from the resource that is required to construct a new record
     * instance. The adapter will then hydrate the object after it has been
     * created.
     *
     * @param ResourceObjectInterface $resource
     * @return object
     */
    abstract protected function createRecord(ResourceObjectInterface $resource);

    /**
     * @param $record
     * @param StandardObjectInterface $attributes
     * @return void
     */
    abstract protected function hydrateAttributes($record, StandardObjectInterface $attributes);

    /**
     * @param $record
     * @param RelationshipsInterface $relationships
     * @return void
     */
    abstract protected function hydrateRelationships($record, RelationshipsInterface $relationships);

    /**
     * Persist changes to the record.
     *
     * @param $record
     * @return object
     */
    abstract protected function persist($record);

    /**
     * @inheritdoc
     */
    public function create(ResourceObjectInterface $resource, EncodingParametersInterface $parameters)
    {
        $record = $this->createRecord($resource);
        $this->hydrateAttributes($record, $resource->getAttributes());
        $this->hydrateRelationships($record, $resource->getRelationships());
        $record = $this->persist($record);

        return $this->hydrateRelated($record, $resource);
    }

    /**
     * @inheritdoc
     */
    public function update($record, ResourceObjectInterface $resource, EncodingParametersInterface $parameters)
    {
        $this->hydrateAttributes($record, $resource->getAttributes());
        $this->hydrateRelationships($record, $resource->getRelationships());
        $record = $this->persist($record);

        return $this->hydrateRelated($record, $resource);
    }

    /**
     * Hydrate any related domain records after the supplied record has been persisted.
     *
     * Child classes can overload this method if they need to do any hydration work after the
     * supplied record has been persisted. This is particularly useful for database structures
     * where relationship data is stored in separate tables, and so the record must have a
     * database id before its relationships can be created.
     *
     * @param $record
     * @param ResourceObjectInterface $resource
     * @return object
     */
    protected function hydrateRelated($record, ResourceObjectInterface $resource)
    {
        return $record;
    }
}
