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

namespace CloudCreativity\JsonApi\Contracts\Object\Relationships;

use CloudCreativity\JsonApi\Contracts\Object\ResourceIdentifier\ResourceIdentifierCollectionInterface;
use CloudCreativity\JsonApi\Contracts\Object\ResourceIdentifier\ResourceIdentifierInterface;
use CloudCreativity\JsonApi\Contracts\Object\StandardObjectInterface;
use RuntimeException;

/**
 * Interface RelationshipInterface
 * @package CloudCreativity\JsonApi
 */
interface RelationshipInterface
{

    /**
     * Get the data as a correctly casted object.
     *
     * If this is a belongs to relationship, a ResourceIdentifierInterface object or null will be returned. If it is
     * a has many relationship, a ResourceIdentifierCollectionInterface will be returned.
     *
     * @return ResourceIdentifierInterface|ResourceIdentifierCollectionInterface|null
     * @throws RuntimeException
     *      if the value for the data key is not a valid relationship value.
     */
    public function getData();

    /**
     * @return bool
     * @deprecated use `isHasOne` instead
     */
    public function isBelongsTo();

    /**
     * @return bool
     */
    public function isHasOne();

    /**
     * @return bool
     */
    public function isHasMany();

    /**
     * @return StandardObjectInterface
     */
    public function getMeta();

    /**
     * @return bool
     */
    public function hasMeta();
}
