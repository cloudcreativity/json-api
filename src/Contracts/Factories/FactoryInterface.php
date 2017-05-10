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

namespace CloudCreativity\JsonApi\Contracts\Factories;

use CloudCreativity\JsonApi\Contracts\Http\ApiInterface;
use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterface;
use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterpreterInterface;
use CloudCreativity\JsonApi\Contracts\Pagination\PageInterface;
use CloudCreativity\JsonApi\Contracts\Repositories\ErrorRepositoryInterface;
use CloudCreativity\JsonApi\Contracts\Store\ContainerInterface as AdapterContainerInterface;
use CloudCreativity\JsonApi\Contracts\Store\StoreInterface;
use CloudCreativity\JsonApi\Contracts\Utils\ReplacerInterface;
use CloudCreativity\JsonApi\Contracts\Validators\ValidatorFactoryInterface;
use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Contracts\Document\LinkInterface;
use Neomerx\JsonApi\Contracts\Factories\FactoryInterface as BaseFactoryInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\SupportedExtensionsInterface;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface as SchemaContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface FactoryInterface
 *
 * Our extension of the `neomerx/json-api` factory, that adds in the units that
 * are created by our extended package.
 *
 * @package CloudCreativity\JsonApi
 */
interface FactoryInterface extends BaseFactoryInterface
{

    /**
     * @param $namespace
     * @param CodecMatcherInterface $codecMatcher
     * @param SchemaContainerInterface $schemaContainer
     * @param StoreInterface $store
     * @param ErrorRepositoryInterface $errorRepository
     * @param SupportedExtensionsInterface|null $supportedExtensions
     * @param string|null $urlPrefix
     * @return ApiInterface
     */
    public function createApi(
        $namespace,
        CodecMatcherInterface $codecMatcher,
        SchemaContainerInterface $schemaContainer,
        StoreInterface $store,
        ErrorRepositoryInterface $errorRepository,
        SupportedExtensionsInterface $supportedExtensions = null,
        $urlPrefix = null
    );

    /**
     * Build a JSON API request object from an API definition and an HTTP request.
     *
     * @param ServerRequestInterface $httpRequest
     *      the inbound HTTP request
     * @param RequestInterpreterInterface $interpreter
     *      the interpreter to analyze the request
     * @param ApiInterface $api
     *      the API that is receiving the request
     * @return RequestInterface
     */
    public function createRequest(
        ServerRequestInterface $httpRequest,
        RequestInterpreterInterface $interpreter,
        ApiInterface $api
    );

    /**
     * Create a codec matcher that is configured using the supplied codecs array.
     *
     * @param SchemaContainerInterface $schemas
     * @param array $codecs
     * @param string|null $urlPrefix
     * @return CodecMatcherInterface
     */
    public function createConfiguredCodecMatcher(SchemaContainerInterface $schemas, array $codecs, $urlPrefix = null);

    /**
     * @param AdapterContainerInterface $adapters
     * @return StoreInterface
     */
    public function createStore(AdapterContainerInterface $adapters);

    /**
     * @param array $adapters
     * @return AdapterContainerInterface
     */
    public function createAdapterContainer(array $adapters);

    /**
     * @param array $errors
     * @return ErrorRepositoryInterface $errors
     */
    public function createErrorRepository(array $errors);

    /**
     * @return ReplacerInterface
     */
    public function createReplacer();

    /**
     * Create a validator factory for the supplied API.
     *
     * @param ErrorRepositoryInterface $errors
     * @param StoreInterface $store
     * @return ValidatorFactoryInterface
     */
    public function createValidatorFactory(ErrorRepositoryInterface $errors, StoreInterface $store);

    /**
     * @param mixed $data
     * @param LinkInterface|null $first
     * @param LinkInterface|null $previous
     * @param LinkInterface|null $next
     * @param LinkInterface|null $last
     * @param object|array|null $meta
     * @param string|null $metaKey
     * @return PageInterface
     */
    public function createPage(
        $data,
        LinkInterface $first = null,
        LinkInterface $previous = null,
        LinkInterface $next = null,
        LinkInterface $last = null,
        $meta = null,
        $metaKey = null
    );
}
