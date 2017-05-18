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

use CloudCreativity\JsonApi\Contracts\Store\ContainerInterface;
use CloudCreativity\JsonApi\Exceptions\InvalidArgumentException;

/**
 * Class ContainerIterator
 *
 * @package CloudCreativity\JsonApi
 */
class ContainerIterator implements ContainerInterface
{

    /**
     * @var array
     */
    private $containers = [];

    /**
     * @param ContainerInterface $container
     * @return $this
     */
    public function attach(ContainerInterface $container)
    {
        if ($this === $container) {
            throw new InvalidArgumentException('Cannot attach a container iterator to itself.');
        }

        $this->containers[] = $container;

        return $this;
    }

    /**
     * @param array $containers
     * @return $this
     */
    public function attachMany(array $containers)
    {
        foreach ($containers as $container) {
            $this->attach($container);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAdapterByResourceType($resourceType)
    {
        /** @var ContainerInterface $container */
        foreach ($this->containers as $container) {
            if ($adapter = $container->getAdapterByResourceType($resourceType)) {
                return $adapter;
            }
        }

        return null;
    }

}
