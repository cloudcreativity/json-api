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

namespace CloudCreativity\JsonApi\Http\Requests;

use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterface;
use CloudCreativity\JsonApi\Contracts\Object\DocumentInterface;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;

/**
 * Class JsonApiRequest
 * @package CloudCreativity\JsonApi
 */
class Request implements RequestInterface
{

    /**
     * @var string
     */
    private $resourceType;

    /**
     * @var EncodingParametersInterface
     */
    private $parameters;

    /**
     * @var string|null
     */
    private $resourceId;

    /**
     * @var string|null
     */
    private $relationshipName;

    /**
     * @var DocumentInterface|null
     */
    private $document;

    /**
     * @var object|null
     */
    private $record;

    /**
     * ValidatedRequest constructor.
     * @param string $resourceType
     * @param EncodingParametersInterface $parameters
     * @param string|null $resourceId
     * @param string|null $relationshipName
     * @param DocumentInterface|null $document
     * @param object|null $record
     */
    public function __construct(
        $resourceType,
        EncodingParametersInterface $parameters,
        $resourceId = null,
        $relationshipName = null,
        DocumentInterface $document = null,
        $record = null
    ) {
        $this->resourceType = $resourceType;
        $this->parameters = $parameters;
        $this->resourceId = $resourceId;
        $this->relationshipName = $relationshipName;
        $this->document = $document;
        $this->record = $record;
    }

    /**
     * @inheritdoc
     */
    public function getResourceType()
    {
        return $this->resourceType;
    }

    /**
     * @inheritdoc
     */
    public function getResourceId()
    {
        return $this->resourceId;
    }

    /**
     * @inheritdoc
     */
    public function getRelationshipName()
    {
        return $this->relationshipName;
    }

    /**
     * @inheritdoc
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @inheritdoc
     */
    public function getDocument()
    {
        return $this->document ? clone $this->document : null;
    }

    /**
     * @inheritdoc
     */
    public function getRecord()
    {
        return $this->record;
    }

}
