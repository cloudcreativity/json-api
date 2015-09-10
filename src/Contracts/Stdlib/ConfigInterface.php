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

namespace CloudCreativity\JsonApi\Contracts\Stdlib;

/**
 * Interface MutableConfigInterface
 * @package CloudCreativity\JsonApi
 */
interface ConfigInterface extends \IteratorAggregate, \Countable
{

    /**
     * @param string|int $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * @param array $keys
     * @param mixed $default
     * @return array
     *      of `key` => `value` pairs
     */
    public function getMany(array $keys, $default = null);

    /**
     * Whether there is a value for the provided key, including null values.
     *
     * @param $key
     * @return bool
     */
    public function exists($key);

    /**
     * Whether a value exists and is not null.
     *
     * @param string|int $key
     * @return bool
     */
    public function has($key);

    /**
     * Whether all the specified keys exist and are not null.
     *
     * @param array $keys
     * @return bool
     */
    public function hasAll(array $keys);

    /**
     * @return array
     */
    public function toArray();

    /**
     * @return bool
     */
    public function isEmpty();
}
