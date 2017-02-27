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

use CloudCreativity\JsonApi\Contracts\Authorizer\AuthorizerInterface;
use CloudCreativity\JsonApi\Contracts\Http\ApiInterface;
use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestHandlerInterface;
use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterface;
use CloudCreativity\JsonApi\Contracts\Pagination\PagingStrategyInterface;
use CloudCreativity\JsonApi\Contracts\Validators\FilterValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\ValidatorProviderInterface;

/**
 * Class Request
 * @package CloudCreativity\JsonApi
 */
class RequestHandler implements RequestHandlerInterface
{

    use ChecksRelationships,
        ChecksQueryParameters,
        AuthorizesRequests,
        ChecksDocuments;

    /**
     * A list of has-one relationships that are expected as endpoints.
     *
     * @var array
     */
    protected $hasOne = [];

    /**
     * A list of has-many relationships that are exposed as endpoints.
     *
     * @var array
     */
    protected $hasMany = [];

    /**
     * @var string[]|null
     * @see ChecksQueryParameters::allowedIncludePaths()
     */
    protected $allowedIncludePaths = [];

    /**
     * @var array|null
     * @see ChecksQueryParameters::allowedFieldSetTypes()
     */
    protected $allowedFieldSetTypes = null;

    /**
     * @var string[]|null
     * @see ChecksQueryParameters::allowedSortParameters()
     */
    protected $allowedSortParameters = [];

    /**
     * @var string[]|null
     * @see ChecksQueryParameters::allowedFilteringParameters()
     */
    protected $allowedFilteringParameters = [];

    /**
     * Whether paging is allowed for this resource
     *
     * @var bool
     */
    protected $allowPaging = true;

    /**
     * @var bool
     * @see ChecksQueryParameters::allowUnrecognizedParameters()
     */
    protected $allowUnrecognizedParams = false;

    /**
     * @var ValidatorProviderInterface
     */
    private $validators;

    /**
     * @var AuthorizerInterface|null
     */
    private $authorizer;

    /**
     * AbstractRequest constructor.
     * @param AuthorizerInterface|null $authorizer
     * @param ValidatorProviderInterface|null $validators
     */
    public function __construct(AuthorizerInterface $authorizer = null, ValidatorProviderInterface $validators = null)
    {
        $this->validators = $validators;
        $this->authorizer = $authorizer;
    }

    /**
     * @inheritdoc
     */
    public function handle(ApiInterface $api, RequestInterface $request)
    {
        $interpreter = $api->getRequestInterpreter();
        $resourceType = $request->getResourceType();

        /** Check the relationship is acceptable */
        if ($request->getRelationshipName()) {
            $this->checkRelationshipName($request);
        }

        /** Check request parameters are acceptable */
        $this->checkQueryParameters($api, $request, $this->filterValidator($resourceType));

        /** Authorize the request */
        if ($this->authorizer) {
            $this->authorize($interpreter, $this->authorizer, $request);
        }

        /** Check the document content is acceptable */
        if ($this->validators) {
            $this->checkDocumentIsAcceptable($this->validators, $interpreter, $request);
        }
    }

    /**
     * @inheritDoc
     */
    protected function allowedRelationships()
    {
        return array_merge($this->hasOne, $this->hasMany);
    }

    /**
     * @inheritDoc
     */
    protected function allowUnrecognizedParameters()
    {
        return $this->allowUnrecognizedParams;
    }

    /**
     * @inheritDoc
     */
    protected function allowedIncludePaths()
    {
        return $this->allowedIncludePaths;
    }

    /**
     * @inheritDoc
     */
    protected function allowedFieldSetTypes()
    {
        return $this->allowedFieldSetTypes;
    }

    /**
     * @inheritDoc
     */
    protected function allowedSortParameters()
    {
        return $this->allowedSortParameters;
    }

    /**
     * @inheritDoc
     */
    protected function allowedFilteringParameters()
    {
        return $this->allowedFilteringParameters;
    }

    /**
     * @inheritDoc
     */
    protected function allowedPagingParameters(PagingStrategyInterface $strategy)
    {
        if (!$this->allowPaging) {
            return [];
        }

        return [$strategy->getPage(), $strategy->getPerPage()];
    }

    /**
     * @param string $resourceType
     * @return FilterValidatorInterface|null
     */
    private function filterValidator($resourceType)
    {
        return $this->validators ? $this->validators->filterResources($resourceType) : null;
    }
}
