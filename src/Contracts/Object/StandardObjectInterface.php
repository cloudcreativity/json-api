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

namespace CloudCreativity\JsonApi\Contracts\Object;

/**
 * Interface StandardObjectInterface
 * @package CloudCreativity\JsonApi
 */
interface StandardObjectInterface extends \Traversable, \Countable
{

    /**
     * @param $key
     * @param $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * @param array $keys
     * @param $default
     * @return array
     */
    public function getProperties(array $keys, $default = null);

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value);

    /**
     * @param array $values
     * @return $this
     */
    public function setProperties(array $values);

    /**
     * @param $key
     * @return bool
     */
    public function has($key);

    /**
     * Whether the object has all of the specified keys.
     *
     * @param array $keys
     * @return bool
     */
    public function hasAll(array $keys);

    /**
     * Whether the object has any (at least one) of the specified keys.
     *
     * @param array $keys
     * @return bool
     */
    public function hasAny(array $keys);

    /**
     * @param $key
     * @return $this
     */
    public function remove($key);

    /**
     * @param array $keys
     * @return $this
     */
    public function removeProperties(array $keys);

    /**
     * Reduce this object so that it only has the supplied allowed keys.
     *
     * @param array $keys
     * @return $this
     */
    public function reduce(array $keys);

    /**
     * Get a list of the object's keys.
     *
     * @return string[]
     */
    public function keys();

    /**
     * Get the object's property values as an array.
     *
     * @return array
     */
    public function toArray();
}
