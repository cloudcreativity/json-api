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

namespace CloudCreativity\JsonApi\Config;

use CloudCreativity\JsonApi\Contracts\Config\ConfigInterface;

/**
 * Class ImmutableConfig
 * @package CloudCreativity\JsonApi
 */
class ImmutableConfig implements ConfigInterface
{

    /**
     * @var array
     */
    protected $_config = [];

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->_config = $config;
    }

    /**
     * @param $key
     * @param null $default
     * @return null
     */
    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->_config) ? $this->_config[$key] : $default;
    }

    /**
     * @param array $keys
     * @param null $default
     * @return array
     */
    public function getMany(array $keys, $default = null)
    {
        $ret = [];

        foreach ($keys as $key) {
            $ret[$key] = $this->get($key, $default);
        }

        return $ret;
    }

    /**
     * Whether there is a value for the provided key, including null values.
     *
     * @param $key
     * @return bool
     */
    public function exists($key)
    {
        return array_key_exists($key, $this->_config);
    }

    /**
     * Whether a value exists and is not null.
     *
     * @param string|int $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->_config[$key]);
    }

    /**
     * Whether all the specified keys exist and are not null.
     *
     * @param array $keys
     * @return bool
     */
    public function hasAll(array $keys)
    {
        foreach ($keys as $key) {

            if (!$this->has($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->_config;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->_config);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->_config);
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->toArray());
    }
}
