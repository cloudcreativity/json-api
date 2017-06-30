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

use CloudCreativity\JsonApi\Contracts\Encoder\SerializerInterface;
use CloudCreativity\JsonApi\Contracts\Http\ApiInterface;
use CloudCreativity\JsonApi\Contracts\Http\Client\ClientInterface;
use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterface;
use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterpreterInterface;
use CloudCreativity\JsonApi\Contracts\Http\Responses\ResponseInterface;
use CloudCreativity\JsonApi\Contracts\Object\DocumentInterface;
use CloudCreativity\JsonApi\Contracts\Pagination\PageInterface;
use CloudCreativity\JsonApi\Contracts\Repositories\ErrorRepositoryInterface;
use CloudCreativity\JsonApi\Contracts\Store\ContainerInterface as AdapterContainerInterface;
use CloudCreativity\JsonApi\Contracts\Store\StoreInterface;
use CloudCreativity\JsonApi\Contracts\Utils\ReplacerInterface;
use CloudCreativity\JsonApi\Contracts\Validators\QueryValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\ValidatorFactoryInterface;
use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Contracts\Document\LinkInterface;
use Neomerx\JsonApi\Contracts\Factories\FactoryInterface as BaseFactoryInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\SupportedExtensionsInterface;
use Neomerx\JsonApi\Contracts\Http\Query\QueryCheckerInterface;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface as SchemaContainerInterface;
use Neomerx\JsonApi\Encoder\EncoderOptions;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponse;

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
     * @param SchemaContainerInterface $container
     * @param EncoderOptions|null $encoderOptions
     * @return SerializerInterface
     */
    public function createSerializer(SchemaContainerInterface $container, EncoderOptions $encoderOptions = null);

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
     * Create a JSON API request object from a PSR server request.
     *
     * @param ServerRequestInterface $httpRequest
     *      the inbound HTTP request
     * @param RequestInterpreterInterface $interpreter
     *      the interpreter to analyze the request
     * @param StoreInterface $store
     *      the store that the request relates to.
     * @return RequestInterface
     */
    public function createRequest(
        ServerRequestInterface $httpRequest,
        RequestInterpreterInterface $interpreter,
        StoreInterface $store
    );

    /**
     * Create a JSON API response object from a PSR response.
     *
     * @param PsrResponse $response
     * @return ResponseInterface
     */
    public function createResponse(PsrResponse $response);

    /**
     * Create a JSON API document from a HTTP message.
     *
     * @param MessageInterface $message
     * @return DocumentInterface|null
     *      the document, or null if the message does not contain body content.
     */
    public function createDocumentObject(MessageInterface $message);

    /**
     * @param mixed $httpClient
     *      the HTTP client that will send requests.
     * @param SchemaContainerInterface $container
     * @param SerializerInterface $encoder
     * @return ClientInterface
     */
    public function createClient($httpClient, SchemaContainerInterface $container, SerializerInterface $encoder);

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
     * Create an extended query parameters checker.
     *
     * @param bool|false $allowUnrecognized
     * @param array|null $includePaths
     * @param array|null $fieldSetTypes
     * @param array|null $sortParameters
     * @param array|null $pagingParameters
     * @param array|null $filteringParameters
     * @param QueryValidatorInterface|null $validator
     * @return QueryCheckerInterface
     */
    public function createExtendedQueryChecker(
        $allowUnrecognized = false,
        array $includePaths = null,
        array $fieldSetTypes = null,
        array $sortParameters = null,
        array $pagingParameters = null,
        array $filteringParameters = null,
        QueryValidatorInterface $validator = null
    );
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
