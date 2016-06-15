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

namespace CloudCreativity\JsonApi\Object\Helpers;

use CloudCreativity\JsonApi\Contracts\Object\StandardObjectInterface;
use CloudCreativity\JsonApi\Object\StandardObject;
use InvalidArgumentException;
use RuntimeException;
use stdClass;

/**
 * Class ObjectProxyTrait
 * @package CloudCreativity\JsonApi
 */
trait ObjectProxyTrait
{

    /**
     * @var object|null
     */
    private $proxy;

    /**
     * @param object $proxy
     * @return $this
     */
    public function setProxy($proxy)
    {
        if (!is_object($proxy)) {
            throw new InvalidArgumentException('Expecting an object.');
        }

        $this->proxy = $proxy;

        return $this;
    }

    /**
     * @return object
     */
    public function getProxy()
    {
        if (!is_object($this->proxy)) {
            $this->proxy = new stdClass();
        }

        return $this->proxy;
    }

    /**
     * @param $key
     * @param null $default
     * @return null
     */
    public function get($key, $default = null)
    {
        return $this->has($key) ? $this->getProxy()->{$key} : $default;
    }

    /**
     * @param string|string[] $keys
     * @param mixed $default
     * @return array
     */
    public function getProperties($keys, $default = null)
    {
        $ret = [];

        foreach ((array) $keys as $key) {
            $ret[$key] = $this->get($key, $default);
        }

        return $ret;
    }

    /**
     * Get properties if they exist.
     *
     * @param string|string[] $keys
     * @return array
     */
    public function getMany($keys)
    {
        $ret = [];

        foreach ((array) $keys as $key) {

            if ($this->has($key)) {
                $ret[$key] = $this->get($key);
            }
        }

        return $ret;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value)
    {
        $this->getProxy()->{$key} = $value;

        return $this;
    }

    /**
     * @param array $values
     * @return $this
     */
    public function setProperties(array $values)
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * @param string|string[] $keys
     * @return bool
     */
    public function has($keys)
    {
        foreach ((array) $keys as $key) {
            if (!property_exists($this->getProxy(), $key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array $keys
     * @return bool
     * @deprecated use `has()`
     */
    public function hasAll(array $keys)
    {
        return $this->has($keys);
    }

    /**
     * @param string|string[] $keys
     * @return bool
     */
    public function hasAny($keys)
    {
        foreach ((array) $keys as $key) {

            if ($this->has($key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $key
     * @return $this
     */
    public function remove($key)
    {
        $proxy = $this->getProxy();

        unset($proxy->{$key});

        return $this;
    }

    /**
     * @param string|string $keys
     * @return $this
     */
    public function removeProperties($keys)
    {
        foreach ((array) $keys as $key) {
            $this->remove($key);
        }

        return $this;
    }

    /**
     * Reduce this object so that it only has the supplied allowed keys.
     *
     * @param string|string[] $keys
     * @return $this
     */
    public function reduce($keys)
    {
        $keys = (array) $keys;

        foreach ($this->keys() as $key) {

            if (!in_array($key, $keys)) {
                $this->remove($key);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function keys()
    {
        return array_keys($this->toArray());
    }

    /**
     * If the object has the current key, convert it to the new key name.
     *
     * @param $currentKey
     * @param $newKey
     * @return $this
     */
    public function mapKey($currentKey, $newKey)
    {
        if ($this->has($currentKey)) {
            $this->set($newKey, $this->get($currentKey))->remove($currentKey);
        }

        return $this;
    }

    /**
     * Map many current keys to new keys.
     *
     * @param array $map
     * @return $this
     */
    public function mapKeys(array $map)
    {
        foreach ($map as $currentKey => $newKey) {
            $this->mapKey($currentKey, $newKey);
        }

        return $this;
    }

    /**
     * @param string|string[] $keys
     * @param callable $transform
     * @return $this
     */
    public function transform($keys, callable $transform)
    {
        foreach ((array) $keys as $key) {

            if ($this->has($key)) {
                $this->set($key, call_user_func($transform, $this->get($key)));
            }
        }

        return $this;
    }

    /**
     * @param callable $transform
     * @return $this
     */
    public function transformKeys(callable $transform)
    {
        ObjectUtils::transformKeys($this->getProxy(), $transform);

        return $this;
    }

    /**
     * @param $key
     * @param callable $converter
     * @return $this
     * @deprecated use `transform()`
     */
    public function convertValue($key, callable $converter)
    {
        return $this->transform($key, $converter);
    }

    /**
     * @param array $keys
     * @param callable $converter
     * @return $this
     * @deprecated use `transform()`
     */
    public function convertValues(array $keys, callable $converter)
    {
        return $this->transform($keys, $converter);
    }

    /**
     * @param array $input
     * @return $this
     */
    public function exchangeArray(array $input)
    {
        $this->setProperties($input);

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return ObjectUtils::toArray($this->getProxy());
    }

    /**
     * @param $key
     * @return StandardObjectInterface
     */
    public function asObject($key)
    {
        $value = $this->get($key);

        if (!is_object($value) && !is_null($value)) {
            throw new RuntimeException("Key '$key' is not an object or null.'");
        }

        return new StandardObject($value);
    }
}
