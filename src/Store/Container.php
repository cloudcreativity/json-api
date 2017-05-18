<?php

/**
 * Copyright 2017 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Store;

use Closure;
use CloudCreativity\JsonApi\Contracts\Store\AdapterInterface;
use CloudCreativity\JsonApi\Contracts\Store\ContainerInterface;
use CloudCreativity\JsonApi\Exceptions\RuntimeException;

/**
 * Class Container
 *
 * @package CloudCreativity\JsonApi
 */
class Container implements ContainerInterface
{

    /**
     * @var array
     */
    private $adapters = [];

    /**
     * @param string $resourceType
     * @param mixed $adapter
     * @return $this
     */
    public function register($resourceType, $adapter)
    {
        $this->adapters[$resourceType] = $adapter;

        return $this;
    }

    /**
     * @param array $adapters
     * @return $this
     */
    public function registerMany(array $adapters)
    {
        foreach ($adapters as $resourceType => $adapter) {
            $this->register($resourceType, $adapter);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAdapterByResourceType($resourceType)
    {
        if (!isset($this->adapters[$resourceType])) {
            return null;
        }

        $adapter = $this->adapters[$resourceType];

        if (!$adapter instanceof AdapterInterface) {
            $adapter = $this->adapters[$resourceType] = $this->create($resourceType, $adapter);
        }

        return $adapter;
    }

    /**
     * @param $resourceType
     * @param $adapter
     * @return AdapterInterface
     */
    protected function create($resourceType, $adapter)
    {
        if ($adapter instanceof Closure) {
            return $this->createFromClosure($adapter, $resourceType);
        }

        if (is_string($adapter)) {
            return $this->createFromString($adapter);
        }

        throw new RuntimeException("Cannot create an adapter for $resourceType.");
    }

    /**
     * @param $string
     * @return AdapterInterface
     */
    protected function createFromString($string)
    {
        if (!class_exists($string)) {
            throw new RuntimeException("Adapter $string is not a class.");
        }

        $adapter = new $string();

        if (!$adapter instanceof AdapterInterface) {
            throw new RuntimeException("Adapter $string does not resolve to an adapter instance.");
        }

        return $adapter;
    }

    /**
     * @param Closure $fn
     * @param $resourceType
     * @return AdapterInterface
     */
    protected function createFromClosure(Closure $fn, $resourceType)
    {
        $adapter = $fn($resourceType);

        if (!$adapter instanceof AdapterInterface) {
            throw new RuntimeException("Adapter closure for resource type $resourceType does not create an adapter instance.");
        }

        return $adapter;
    }

}
