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

namespace CloudCreativity\JsonApi\Http\Requests;

use CloudCreativity\JsonApi\Contracts\Factories\FactoryInterface;
use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterpreterInterface;
use CloudCreativity\JsonApi\Contracts\Object\DocumentInterface;
use CloudCreativity\JsonApi\Contracts\Store\StoreInterface;
use CloudCreativity\JsonApi\Object\ResourceIdentifier;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class RequestFactory
 *
 * @package CloudCreativity\JsonApi
 */
class RequestFactory
{

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * RequestFactory constructor.
     *
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Create a JSON API request using the supplied server request, interpreter and API settings.
     *
     * Note the building the request will require a decoder to be available via the API's codec
     * matcher, if the request contains body content. This means that the API's codec matcher
     * must have been used for content negotiation prior to constructing the JSON API request.
     *
     * @param ServerRequestInterface $request
     *      the inbound HTTP request
     * @param RequestInterpreterInterface $interpreter
     *      the intepreter to analyze the request.
     * @param StoreInterface $store
     *      the store that the request relates to.
     * @return Request
     */
    public function build(
        ServerRequestInterface $request,
        RequestInterpreterInterface $interpreter,
        StoreInterface $store
    ) {
        return new Request(
            $interpreter->getResourceType(),
            $parameters = $this->parseParameters($request),
            $interpreter->getResourceId(),
            $interpreter->getRelationshipName(),
            $this->parseDocument($request, $interpreter),
            $this->locateRecord($interpreter, $store, $parameters)
        );
    }

    /**
     * @param ServerRequestInterface $request
     * @return EncodingParametersInterface
     * @throws JsonApiException
     */
    protected function parseParameters(ServerRequestInterface $request)
    {
        return $this->factory->createQueryParametersParser()->parse($request);
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestInterpreterInterface $interpreter
     * @return DocumentInterface
     */
    protected function parseDocument(
        ServerRequestInterface $request,
        RequestInterpreterInterface $interpreter
    ) {
        $document = $this->factory->createDocumentObject($request);

        if (!$document && $interpreter->isExpectingDocument()) {
            throw new JsonApiException([], 400);
        }

        return $document;
    }

    /**
     * @param RequestInterpreterInterface $interpreter
     * @param StoreInterface $store
     * @param EncodingParametersInterface $parameters
     * @return object
     */
    protected function locateRecord(
        RequestInterpreterInterface $interpreter,
        StoreInterface $store,
        EncodingParametersInterface $parameters
    ) {
        if (!$id = $interpreter->getResourceId()) {
            return null;
        }

        $identifier = ResourceIdentifier::create($interpreter->getResourceType(), $id);
        $record = $store->queryRecord($identifier, $parameters);

        if (!$record) {
            throw new JsonApiException([], 404);
        }

        return $record;
    }

}
