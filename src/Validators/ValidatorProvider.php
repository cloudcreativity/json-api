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

namespace CloudCreativity\JsonApi\Validators;

use CloudCreativity\JsonApi\Contracts\Validators\ValidatorFactoryInterface;
use CloudCreativity\JsonApi\Contracts\Validators\ValidatorProviderInterface;

/**
 * Class ValidatorProvider
 * @package CloudCreativity\JsonApi
 */
class ValidatorProvider implements ValidatorProviderInterface
{

    /**
     * @var ValidatorFactoryInterface
     */
    protected $factory;

    /**
     * ValidatorProvider constructor.
     * @param ValidatorFactoryInterface $factory
     */
    public function __construct(ValidatorFactoryInterface $factory = null)
    {
        $this->factory = $factory ?: new ValidatorFactory();
    }

    /**
     * @inheritDoc
     */
    public function createResource($resourceType)
    {
        return $this->resource($resourceType);
    }

    /**
     * @inheritDoc
     */
    public function updateResource($resourceType, $resourceId, $record)
    {
        return $this->resource($resourceType, $resourceId);
    }

    /**
     * @inheritDoc
     */
    public function modifyRelationship($resourceType, $resourceId, $relationshipName, $record)
    {
        return $this->factory->relationship();
    }

    /**
     * @inheritDoc
     */
    public function filterResources($resourceType)
    {
        return new FilterValidator();
    }

    /**
     * @inheritDoc
     */
    protected function resource($resourceType, $resourceId = null)
    {
        $resourceValidator = $this->factory->resource($resourceType, $resourceId);

        return $this->factory->resourceDocument($resourceValidator);
    }

}
