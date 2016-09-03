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

use CloudCreativity\JsonApi\Contracts\Object\ResourceInterface;

/**
 * Interface HydratesRelatedInterface
 * @package CloudCreativity\JsonApi
 */
interface HydratesRelatedInterface
{

    /**
     * Hydrate any related domain records.
     *
     * Hydrators that need to transfer data from the supplied resource to records other than the
     * primary domain record that the resource represents should implement this interface.
     *
     * This is particularly useful where a two-step hydration process is required. For example, in
     * relational databases, if the resource contains data that needs to be transferred on to related
     * domain records, the primary record will need to exist *before* this data is transferred. This is
     * common if the resource contains representations of has-many relationships that need to be
     * populated into a database. Or if the resource's attributes contain attribute values that are
     * actually read from related domain records rather than the primary record that the resource belongs to.
     *
     * @param ResourceInterface $resource
     * @param object $record
     *      the domain record that the resource represents
     * @return object|object[]|null
     *      the related record(s) that were hydrated, or none.
     */
    public function hydrateRelated(ResourceInterface $resource, $record);
}
