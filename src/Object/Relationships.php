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

namespace CloudCreativity\JsonApi\Object;

use CloudCreativity\JsonApi\Contracts\Object\RelationshipInterface;
use CloudCreativity\JsonApi\Contracts\Object\RelationshipsInterface;
use CloudCreativity\JsonApi\Exceptions\DocumentException;
use Generator;

/**
 * Class Relationships
 * @package CloudCreativity\JsonApi
 */
class Relationships extends StandardObject implements RelationshipsInterface
{

    /**
     * @param $method
     * @param array $args
     * @return Relationship
     * @deprecated use `rel()` or `relationship()`
     */
    public function __call($method, array $args)
    {
        $matches = [];

        if (!preg_match("/^get{1}(?<relationship>[A-Z]{1}[a-zA-Z]+)$/", $method, $matches)) {
            throw new \BadMethodCallException(sprintf('Cannot call %s::%s()', static::class, $method));
        }

        array_unshift($args, lcfirst($matches['relationship']));

        return call_user_func_array([$this, 'get'], $args);
    }

    /**
     * @param $key
     * @param $default
     * @return RelationshipInterface
     * @deprecated
     *      this will be reverted to the definition as per the StandardObjectInterface. Use `rel()` or
     *      `relationship()` instead.
     */
    public function get($key, $default = null)
    {
        return new Relationship(parent::get($key, $default));
    }

    /**
     * @return Generator
     */
    public function all()
    {
        foreach ($this->keys() as $key) {
            yield $key => $this->rel($key);
        }
    }

    /**
     * Shorthand for `relationship()`
     *
     * @param $key
     * @return RelationshipInterface
     * @throws DocumentException
     */
    public function rel($key)
    {
        return $this->relationship($key);
    }

    /**
     * @param $key
     * @return RelationshipInterface
     * @throws DocumentException
     *      if the key is not present, or is not an object.
     */
    public function relationship($key)
    {
        if (!$this->has($key)) {
            throw new DocumentException("Relationship member '$key' is not present.");
        }

        $value = parent::get($key);

        if (!is_object($value)) {
            throw new DocumentException("Relationship member '$key' is not an object.'");
        }

        return new Relationship($value);
    }

}
