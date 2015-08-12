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

namespace CloudCreativity\JsonApi\Object\ResourceIdentifier;

/**
 * Class ResourceIdentifierCollection
 * @package CloudCreativity\JsonApi
 */
class ResourceIdentifierCollection implements \IteratorAggregate, \Countable
{

    /**
     * @var array
     */
    protected $_stack = [];

    /**
     * @param array $identifiers
     */
    public function __construct(array $identifiers = [])
    {
        $this->addMany($identifiers);
    }

    /**
     * @param ResourceIdentifier $identifier
     * @return $this
     */
    public function add(ResourceIdentifier $identifier)
    {
        if (!$this->has($identifier)) {
            $this->_stack[] = $identifier;
        }

        return $this;
    }

    /**
     * @param ResourceIdentifier $identifier
     * @return bool
     */
    public function has(ResourceIdentifier $identifier)
    {
        return in_array($identifier, $this->_stack);
    }

    /**
     * @param array $identifiers
     * @return $this
     */
    public function addMany(array $identifiers)
    {
        foreach ($identifiers as $identifier) {

            if (!$identifier instanceof ResourceIdentifier) {
                throw new \InvalidArgumentException('Expecting only Identifier objects.');
            }

            $this->add($identifier);
        }

        return $this;
    }

    /**
     * @param array $identifiers
     * @return $this
     */
    public function setAll(array $identifiers)
    {
        $this->clear()->addMany($identifiers);

        return $this;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->_stack;
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->_stack = [];

        return $this;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getAll());
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->_stack);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->_stack);
    }

    /**
     * @return bool
     */
    public function isComplete()
    {
        /** @var ResourceIdentifier $identifier */
        foreach ($this as $identifier) {

            if (!$identifier->isComplete()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $typeOrTypes
     * @return bool
     */
    public function isOnly($typeOrTypes)
    {
        /** @var ResourceIdentifier $identifier */
        foreach ($this as $identifier) {

            if (!$identifier->isType($typeOrTypes)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function getIds()
    {
        $ret = [];

        /** @var ResourceIdentifier $identifier */
        foreach ($this as $identifier) {
            $ret[] = $identifier->getId();
        }

        return $ret;
    }

    /**
     * @param array|null $typeMap
     * @return array
     */
    public function map(array $typeMap = null)
    {
        $ret = [];

        /** @var ResourceIdentifier $identifier */
        foreach ($this as $identifier) {

            $key = is_array($typeMap) ? $identifier->mapType($typeMap) : $identifier->getType();

            if (!isset($ret[$key])) {
                $ret[$key] = [];
            }

            $ret[$key][] = $identifier->getId();
        }

        return $ret;
    }

    /**
     * @param array $input
     * @return ResourceIdentifierCollection
     */
    public static function create(array $input)
    {
        $collection = new static();

        foreach ($input as $value) {
            $collection->add(new ResourceIdentifier($value));
        }

        return $collection;
    }
}
