<?php

namespace CloudCreativity\JsonApi\Resolver;

use CloudCreativity\JsonApi\Contracts\ResolverInterface;
use CloudCreativity\JsonApi\Utils\Str;
use IteratorAggregate;

class NamespaceResolver implements IteratorAggregate, ResolverInterface
{

    /**
     * @var string
     */
    private $rootNamespace;

    /**
     * @var array
     */
    private $resources;

    /**
     * @var array
     */
    private $types;

    /**
     * PodResolver constructor.
     *
     * @param string $rootNamespace
     * @param array $resources
     */
    public function __construct($rootNamespace, array $resources)
    {
        $this->rootNamespace = $rootNamespace;
        $this->resources = $resources;
        $this->types = $this->flip($resources);
    }

    /**
     * @inheritDoc
     */
    public function isType($type)
    {
        return isset($this->types[$type]);
    }

    /**
     * @inheritdoc
     */
    public function getType($resourceType)
    {
        if (!isset($this->resources[$resourceType])) {
            return null;
        }

        return $this->resources[$resourceType];
    }

    /**
     * @inheritDoc
     */
    public function isResourceType($resourceType)
    {
        return isset($this->resources[$resourceType]);
    }

    /**
     * @inheritdoc
     */
    public function getResourceType($type)
    {
        if (!isset($this->types[$type])) {
            return null;
        }

        return $this->types[$type];
    }

    /**
     * @inheritdoc
     */
    public function getSchemaByType($type)
    {
        $resourceType = $this->getResourceType($type);

        return $resourceType ? $this->getSchemaByResourceType($resourceType) : null;
    }

    /**
     * @inheritdoc
     */
    public function getSchemaByResourceType($resourceType)
    {
        return $this->resolve('Schema', $resourceType);
    }

    /**
     * @inheritdoc
     */
    public function getAdapterByType($type)
    {
        $resourceType = $this->getResourceType($type);

        return $resourceType ? $this->getAdapterByResourceType($resourceType) : null;
    }

    /**
     * @inheritdoc
     */
    public function getAdapterByResourceType($resourceType)
    {
        return $this->resolve('Adapter', $resourceType);
    }

    /**
     * @inheritdoc
     */
    public function getAuthorizerByType($type)
    {
        $resourceType = $this->getResourceType($type);

        return $resourceType ? $this->getAuthorizerByResourceType($resourceType) : null;
    }

    /**
     * @inheritdoc
     */
    public function getAuthorizerByResourceType($resourceType)
    {
        return $this->resolve('Authorizer', $resourceType);
    }

    /**
     * @inheritdoc
     */
    public function getValidatorsByType($type)
    {
        $resourceType = $this->getResourceType($type);

        return $resourceType ? $this->getValidatorsByResourceType($resourceType) : null;
    }

    /**
     * @inheritdoc
     */
    public function getValidatorsByResourceType($resourceType)
    {
        return $this->resolve('Validators', $resourceType);
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        foreach ($this->types as $type => $resourceType) {
            yield $type => [
                'type' => $resourceType,
                'adapter' => $this->getAdapterByResourceType($resourceType),
                'authorizer' => $this->getAuthorizerByResourceType($resourceType),
                'schema' => $this->getSchemaByResourceType($resourceType),
                'validators' => $this->getValidatorsByResourceType($resourceType),
            ];
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return iterator_to_array($this);
    }

    /**
     * @param $unit
     * @param $resourceType
     * @return string
     */
    protected function resolve($unit, $resourceType)
    {
        $rootNamespace = rtrim($this->rootNamespace, '\\');
        $resourceType = Str::classify($resourceType);

        return sprintf('%s\%s\%s', $rootNamespace, $resourceType, $unit);
    }

    /**
     * Key the resource array by domain record type.
     *
     * @param array $resources
     * @return array
     */
    private function flip(array $resources)
    {
        $types = [];

        foreach ($resources as $resourceType => $types) {
            foreach ((array) $types as $type) {
                $types[$type] = $resourceType;
            }
        }

        return $types;
    }
}
