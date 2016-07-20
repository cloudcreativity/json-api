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

namespace CloudCreativity\JsonApi\Contracts\Object;

use Countable;
use Traversable;

/**
 * Interface StandardObjectInterface
 * @package CloudCreativity\JsonApi
 */
interface StandardObjectInterface extends Traversable, Countable
{

    /**
     * @param $key
     * @param $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * @param string|string[] $keys
     * @param $default
     * @return array
     */
    public function getProperties($keys, $default = null);

    /**
     * Get properties if they exist.
     *
     * @param string|string[] $keys
     * @return array
     */
    public function getMany($keys);

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
     * Set the key's value, if the key does not exist on the object.
     *
     * @param $key
     * @param $value
     * @return $this
     */
    public function add($key, $value);

    /**
     * Add many properties.
     *
     * @param array $values
     * @return $this
     */
    public function addProperties(array $values);

    /**
     * Do all the key(s) exist?
     *
     * @param string|string[] $keys
     * @return bool
     */
    public function has($keys);

    /**
     * Whether the object has all of the specified keys.
     *
     * @param array $keys
     * @return bool
     * @deprecated use `has()`
     */
    public function hasAll(array $keys);

    /**
     * Whether the object has any (at least one) of the specified keys.
     *
     * @param string|string[] $keys
     * @return bool
     */
    public function hasAny($keys);

    /**
     * @param string $key
     * @return $this
     */
    public function remove($key);

    /**
     * @param string|string[] $keys
     * @return $this
     */
    public function removeProperties($keys);

    /**
     * Reduce this object so that it only has the supplied allowed keys.
     *
     * @param string|string[] $keys
     * @return $this
     */
    public function reduce($keys);

    /**
     * Get a list of the object's keys.
     *
     * @return string[]
     */
    public function keys();

    /**
     * If the object has the current key, convert it to the new key.
     *
     * @param $currentKey
     * @param $newKey
     * @return $this
     */
    public function mapKey($currentKey, $newKey);

    /**
     * Map multiple keys to new key names.
     *
     * @param array $map
     * @return $this
     */
    public function mapKeys(array $map);

    /**
     * Apply the transform to the value for the supplied key(s), if it exists.
     *
     * @param string|string[] $keys
     * @param callable $transform
     * @return $this
     */
    public function transform($keys, callable $transform);

    /**
     * Recursively iterate through the object's keys and apply the transform to each key.
     *
     * @param callable $transform
     * @return $this
     */
    public function transformKeys(callable $transform);

    /**
     * @param $keys
     * @param callable $converter
     * @return mixed
     * @deprecated use `transform()`
     */
    public function convertValue($keys, callable $converter);

    /**
     * Apply the converter to multiple keys that exist.
     *
     * @param array $keys
     * @param callable $converter
     * @return $this
     * @deprecated use `transform()`
     */
    public function convertValues(array $keys, callable $converter);

    /**
     * Get the object's property values as an array.
     *
     * @return array
     */
    public function toArray();

    /**
     * Get the supplied key's value as a standard object.
     *
     * @param $key
     * @return StandardObjectInterface
     */
    public function asObject($key);
}
