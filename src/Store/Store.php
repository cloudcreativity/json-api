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

use CloudCreativity\JsonApi\Contracts\Object\ResourceIdentifierInterface;
use CloudCreativity\JsonApi\Contracts\Store\AdapterInterface;
use CloudCreativity\JsonApi\Contracts\Store\ContainerInterface;
use CloudCreativity\JsonApi\Contracts\Store\StoreInterface;
use CloudCreativity\JsonApi\Exceptions\RecordNotFoundException;
use CloudCreativity\JsonApi\Exceptions\RuntimeException;

/**
 * Class Store
 *
 * @package CloudCreativity\JsonApi
 */
class Store implements StoreInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var IdentityMap
     */
    private $identityMap;

    /**
     * Store constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->identityMap = new IdentityMap();
    }

    /**
     * @inheritdoc
     */
    public function isType($resourceType)
    {
        return !!$this->container->getAdapterByResourceType($resourceType);
    }

    /**
     * @inheritdoc
     */
    public function exists(ResourceIdentifierInterface $identifier)
    {
        $check = $this->identityMap->exists($identifier);

        if (is_bool($check)) {
            return $check;
        }

        $exists = $this
            ->adapterFor($identifier->getType())
            ->exists($identifier->getId());

        $this->identityMap->add($identifier, $exists);

        return $exists;
    }

    /**
     * @inheritdoc
     */
    public function find(ResourceIdentifierInterface $identifier)
    {
        $record = $this->identityMap->find($identifier);

        if (is_object($record)) {
            return $record;
        } elseif (false === $record) {
            return null;
        }

        $record = $this
            ->adapterFor($identifier->getType())
            ->find($identifier->getId());

        $this->identityMap->add($identifier, is_object($record) ? $record : false);

        return $record;
    }

    /**
     * @inheritdoc
     */
    public function findRecord(ResourceIdentifierInterface $identifier)
    {
        if (!$record = $this->find($identifier)) {
            throw new RecordNotFoundException($identifier);
        }

        return $record;
    }

    /**
     * @param $resourceType
     * @return AdapterInterface
     */
    protected function adapterFor($resourceType)
    {
        if (!$adapter = $this->container->getAdapterByResourceType($resourceType)) {
            throw new RuntimeException("No adapter for resource type: $resourceType");
        }

        return $adapter;
    }
}
