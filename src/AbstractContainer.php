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

namespace CloudCreativity\JsonApi;

use CloudCreativity\JsonApi\Contracts\Adapter\ResourceAdapterInterface;
use CloudCreativity\JsonApi\Contracts\ContainerInterface;
use CloudCreativity\JsonApi\Contracts\ResolverInterface;
use CloudCreativity\JsonApi\Exceptions\RuntimeException;
use Neomerx\JsonApi\Contracts\Schema\SchemaFactoryInterface;
use Neomerx\JsonApi\Contracts\Schema\SchemaProviderInterface;

/**
 * Class AbstractContainer
 *
 * @package CloudCreativity\JsonApi
 */
abstract class AbstractContainer implements ContainerInterface
{

    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @var SchemaFactoryInterface
     */
    private $factory;

    /**
     * @var array
     */
    private $createdSchemas = [];

    /**
     * @var array
     */
    private $createdAdapters = [];

    /**
     * Create an instance of the specified
     *
     * For example, a framework specific implementation may choose to delegate this method
     * to its service container.
     *
     * @param string $className
     * @return mixed
     */
    abstract protected function create($className);

    /**
     * AbstractContainer constructor.
     *
     * @param ResolverInterface $resolver
     * @param SchemaFactoryInterface $factory
     */
    public function __construct(ResolverInterface $resolver, SchemaFactoryInterface $factory)
    {
        $this->resolver = $resolver;
        $this->factory = $factory;
    }

    /**
     * @inheritDoc
     */
    public function getSchema($resourceObject)
    {
        return $this->getSchemaByType(get_class($resourceObject));
    }

    /**
     * @inheritDoc
     */
    public function getSchemaByType($type)
    {
        $resourceType = $this->resolver->getResourceType($type);

        return $this->getSchemaByResourceType($resourceType);
    }

    /**
     * @inheritDoc
     */
    public function getSchemaByResourceType($resourceType)
    {
        if ($this->hasCreatedSchema($resourceType)) {
            return $this->getCreatedSchema($resourceType);
        }

        if (!$this->resolver->isResourceType($resourceType)) {
            return $this->createdSchemas[$resourceType] = null;
        }

        $className = $this->resolver->getSchemaByResourceType($resourceType);
        $schema = $this->createSchemaFromClassName($className);
        $this->setCreatedSchema($resourceType, $schema);

        return $schema;
    }


    /**
     * @param $record
     * @return ResourceAdapterInterface|null
     */
    public function getAdapter($record)
    {
        return $this->getAdapterByType(get_class($record));
    }

    /**
     * @inheritDoc
     */
    public function getAdapterByType($type)
    {
        $resourceType = $this->resolver->getResourceType($type);

        return $this->getAdapterByResourceType($resourceType);
    }

    /**
     * @inheritDoc
     */
    public function getAdapterByResourceType($resourceType)
    {
        if ($this->hasCreatedAdapter($resourceType)) {
            return $this->getCreatedAdapter($resourceType);
        }

        if (!$this->resolver->isResourceType($resourceType)) {
            return $this->createdAdapters[$resourceType] = null;
        }

        $className = $this->resolver->getAdapterByResourceType($resourceType);
        $adapter = $this->createAdapterFromClassName($className);
        $this->setCreatedAdapter($resourceType, $adapter);

        return $adapter;
    }

    /**
     * @param string $resourceType
     * @return bool
     */
    protected function hasCreatedSchema($resourceType)
    {
        return array_key_exists($resourceType, $this->createdSchemas);
    }

    /**
     * @param string $resourceType
     * @return ResourceAdapterInterface|null
     */
    protected function getCreatedSchema($resourceType)
    {
        return $this->createdSchemas[$resourceType];
    }

    /**
     * @param string $resourceType
     * @param SchemaProviderInterface $schema
     * @return void
     */
    protected function setCreatedSchema($resourceType, SchemaProviderInterface $schema)
    {
        $this->createdSchemas[$resourceType] = $schema;
    }

    /**
     * @param string $resourceType
     * @return bool
     */
    protected function hasCreatedAdapter($resourceType)
    {
        return array_key_exists($resourceType, $this->createdAdapters);
    }

    /**
     * @param string $resourceType
     * @return ResourceAdapterInterface|null
     */
    protected function getCreatedAdapter($resourceType)
    {
        return $this->createdAdapters[$resourceType];
    }

    /**
     * @param string $resourceType
     * @param ResourceAdapterInterface $adapter
     * @return void
     */
    protected function setCreatedAdapter($resourceType, ResourceAdapterInterface $adapter)
    {
        $this->createdAdapters[$resourceType] = $adapter;
    }

    /**
     * @param string $className
     * @return SchemaProviderInterface
     */
    protected function createSchemaFromClassName($className)
    {
        $schema = $this->create($className);

        if (!$schema instanceof SchemaProviderInterface) {
            throw new RuntimeException("Class [$className] is not a schema provider.");
        }

        return $schema;
    }

    /**
     * @param $className
     * @return ResourceAdapterInterface
     */
    protected function createAdapterFromClassName($className)
    {
        $adapter = $this->create($className);

        if (!$adapter instanceof ResourceAdapterInterface) {
            throw new RuntimeException("Class [$className] is not a resource adapter.");
        }

        return $adapter;
    }

}
