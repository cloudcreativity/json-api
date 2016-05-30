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

namespace CloudCreativity\JsonApi\Store;

use CloudCreativity\JsonApi\Contracts\Object\ResourceIdentifierInterface;
use CloudCreativity\JsonApi\Contracts\Store\AdapterInterface;
use CloudCreativity\JsonApi\Contracts\Store\StoreInterface;
use CloudCreativity\JsonApi\Exceptions\RecordNotFoundException;
use CloudCreativity\JsonApi\Exceptions\StoreException;
use InvalidArgumentException;

class Store implements StoreInterface
{

    /**
     * @var AdapterInterface[]
     */
    private $adapters = [];

    /**
     * Does the record this resource identifier refers to exist?
     *
     * @param ResourceIdentifierInterface $identifier
     * @return bool
     */
    public function exists(ResourceIdentifierInterface $identifier)
    {
        return $this
            ->adapterFor($identifier->type())
            ->exists($identifier);
    }

    /**
     * @param ResourceIdentifierInterface $identifier
     * @return object|null
     *      the record, or null if it does not exist.
     */
    public function find(ResourceIdentifierInterface $identifier)
    {
        return $this
            ->adapterFor($identifier->type())
            ->find($identifier);
    }

    /**
     * @param ResourceIdentifierInterface $identifier
     * @return object
     *      the record
     * @throws RecordNotFoundException
     *      if the record does not exist.
     */
    public function findRecord(ResourceIdentifierInterface $identifier)
    {
        $record = $this->find($identifier);

        if (!$record) {
            throw RecordNotFoundException::create($identifier);
        }

        return $record;
    }

    /**
     * @param AdapterInterface $adapter
     */
    public function register(AdapterInterface $adapter)
    {
        $this->adapters[] = $adapter;
    }

    /**
     * @param $resourceType
     * @return AdapterInterface
     */
    protected function adapterFor($resourceType)
    {
        /** @var AdapterInterface $adapter */
        foreach ($this->adapters as $adapter) {
            if ($adapter->recognises($resourceType)) {
                return $adapter;
            }
        }

        throw new StoreException("No adapter for resource type: $resourceType");
    }

    /**
     * @param array $adapters
     */
    protected function registerMany(array $adapters)
    {
        foreach ($adapters as $adapter) {
            if (!$adapter instanceof AdapterInterface) {
                throw new InvalidArgumentException('Expecting an array of adapter instances.');
            }

            $this->register($adapter);
        }
    }
}
