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

use CloudCreativity\JsonApi\Contracts\Object\StandardObjectInterface;
use CloudCreativity\JsonApi\Exceptions\DocumentException;
use IteratorAggregate;
use Traversable;

/**
 * Interface RelationshipsInterface
 * @package CloudCreativity\JsonApi
 */
interface RelationshipsInterface extends StandardObjectInterface, IteratorAggregate
{

    /**
     * @param $key
     * @param $default
     *      the default value to use for the RelationshipInterface object if the relationship does not exist.
     * @return RelationshipInterface
     * @deprecated
     *      this will be reverted to the definition as per the StandardObjectInterface. Use `rel()` or
     *      `relationship()` instead.
     */
    public function get($key, $default = null);

    /**
     * Get a traversable object of keys to relationship objects.
     *
     * This iterator will return all keys with values cast to `RelationshipInterface` objects.
     *
     * @return Traversable
     */
    public function all();

    /**
     * Shorthand for `relationship()`
     *
     * @param $key
     * @return RelationshipInterface
     * @throws DocumentException
     */
    public function rel($key);

    /**
     * @param $key
     * @return RelationshipInterface
     * @throws DocumentException
     *      if the key is not present, or is not an object.
     */
    public function relationship($key);

}
