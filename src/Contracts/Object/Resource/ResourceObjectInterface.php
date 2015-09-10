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

namespace CloudCreativity\JsonApi\Contracts\Object\Resource;

use CloudCreativity\JsonApi\Contracts\Object\Relationships\RelationshipsInterface;
use CloudCreativity\JsonApi\Contracts\Object\ResourceIdentifier\ResourceIdentifierInterface;
use CloudCreativity\JsonApi\Contracts\Object\StandardObjectInterface;
use RuntimeException;

/**
 * Interface ResourceObjectInterface
 * @package CloudCreativity\JsonApi
 */
interface ResourceObjectInterface extends StandardObjectInterface
{

    /**
     * @return string
     * @throws RuntimeException
     *      if no type is set.
     */
    public function getType();

    /**
     * @return string|int
     * @throws
     *      if no id is set.
     */
    public function getId();

    /**
     * @return bool
     */
    public function hasId();

    /**
     * @return ResourceIdentifierInterface
     */
    public function getIdentifier();

    /**
     * @return StandardObjectInterface
     */
    public function getAttributes();

    /**
     * @return bool
     */
    public function hasAttributes();

    /**
     * @return RelationshipsInterface
     */
    public function getRelationships();

    /**
     * @return bool
     */
    public function hasRelationships();

    /**
     * @return StandardObjectInterface
     */
    public function getMeta();

    /**
     * @return bool
     */
    public function hasMeta();

}
