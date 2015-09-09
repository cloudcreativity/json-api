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

namespace CloudCreativity\JsonApi\Contracts\Config;

/**
 * Interface MutableConfigInterface
 * @package CloudCreativity\JsonApi
 */
interface MutableConfigInterface extends ConfigInterface
{

    /**
     * @param string|int $key
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value);

    /**
     * @param array $values
     *      of `key` => `value` pairs.
     * @return $this
     */
    public function setMany(array $values);

    /**
     * Add a value to the config if it is not already set.
     *
     * @param $key
     * @param $value
     * @param bool $overwriteNull
     *      whether an existing null value should be overwritten.
     * @return $this
     */
    public function add($key, $value, $overwriteNull = false);

    /**
     * Add values if they are not already set.
     *
     * @param array $values
     * @param bool|false $overwriteNull
     *      whether an existing null value should be overwritten.
     * @return $this
     */
    public function addMany(array $values, $overwriteNull = false);

    /**
     * @param string|int $key
     * @return $this
     */
    public function remove($key);

    /**
     * @param array $keys
     * @return $this
     */
    public function removeMany(array $keys);

    /**
     * @param array $values
     * @param bool $recursive
     *      whether merging should be recursive.
     * @return $this
     */
    public function merge(array $values, $recursive = false);

}
