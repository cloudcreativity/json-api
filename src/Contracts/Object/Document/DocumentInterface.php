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

namespace CloudCreativity\JsonApi\Contracts\Object\Document;

use CloudCreativity\JsonApi\Contracts\Object\Relationships\RelationshipInterface;
use CloudCreativity\JsonApi\Contracts\Object\Resource\ResourceObjectInterface;
use CloudCreativity\JsonApi\Contracts\Object\StandardObjectInterface;

/**
 * Interface DocumentInterface
 * @package CloudCreativity\JsonApi
 */
interface DocumentInterface extends StandardObjectInterface
{

    const DATA = 'data';
    const META = 'meta';

    /**
     * @return StandardObjectInterface
     */
    public function getData();

    /**
     * Get the data member as a resource object.
     *
     * @return ResourceObjectInterface
     */
    public function getResourceObject();

    /**
     * Get the data member as a relationship.
     *
     * @return RelationshipInterface
     */
    public function getRelationship();

    /**
     * @return StandardObjectInterface
     */
    public function getMeta();
}
