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

use CloudCreativity\JsonApi\Contracts\Http\ApiInterface;
use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestFactoryInterface;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Psr\Http\Message\ServerRequestInterface;
use Neomerx\JsonApi\Contracts\Http\HttpFactoryInterface;
use CloudCreativity\JsonApi\Contracts\Validators\ValidatorFactoryInterface;
use CloudCreativity\JsonApi\Validators\ValidatorFactory;
use Neomerx\JsonApi\Factories\Factory;
use CloudCreativity\JsonApi\Object\ResourceIdentifier;
use CloudCreativity\JsonApi\Object\Document;
use CloudCreativity\JsonApi\Contracts\Object\DocumentInterface;
use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterpreterInterface;
use CloudCreativity\JsonApi\Exceptions\ValidationException;

class RequestFactory implements RequestFactoryInterface
{

    private $httpFactory;

    private $validators;

    public function __construct(
        HttpFactoryInterface $httpFactory = null,
        ValidatorFactoryInterface $validators = null
    ) {
        $this->httpFactory = $httpFactory ?: new Factory();
        $this->validators = $validators ?: new ValidatorFactory();
    }

    /**
     * @inheritdoc
     */
    public function build(ApiInterface $api, ServerRequestInterface $request)
    {
        $this->doContentNegotiation($api, $request);
        $params = $this->parseParameters($request);
        $document = $this->parseDocument($api, $request);
        $interpreter = $api->getRequestInterpreter();
        $record = $this->locateRecord($api);

        return new Request(
            $interpreter->getResourceType(),
            $params,
            $interpreter->getResourceId(),
            $interpreter->getRelationshipName(),
            $document,
            $record
        );
    }

    /**
     * @param ApiInterface $api
     * @param ServerRequestInterface $request
     * @throws JsonApiException
     */
    protected function doContentNegotiation(ApiInterface $api, ServerRequestInterface $request)
    {
        $parser = $this->httpFactory->createHeaderParametersParser();
        $checker = $this->httpFactory->createHeadersChecker($api->getCodecMatcher());

        $checker->checkHeaders($parser->parse($request));
    }

    /**
     * @param ServerRequestInterface $request
     * @return EncodingParametersInterface
     * @throws JsonApiException
     */
    protected function parseParameters(ServerRequestInterface $request)
    {
        return $this
            ->httpFactory
            ->createQueryParametersParser()
            ->parse($request);
    }

    /**
     * @param ApiInterface $api
     * @param ServerRequestInterface $request
     * @return RequestDocument|null
     * @throws JsonApiException
     */
    protected function parseDocument(ApiInterface $api, ServerRequestInterface $request)
    {
        $interpreter = $api->getRequestInterpreter();

        if (!$interpreter->isExpectingDocument()) {
            return null;
        }

        $decoder = $api->getCodecMatcher()->getDecoder();
        $document = $decoder->decode((string) $request->getBody());

        if (!is_object($document)) {
            throw new RuntimeException('A decoder that decodes to an object must be used.');
        }

        $document = ($document instanceof DocumentInterface) ? $document : new Document($document);
        $this->validateDocument($document, $interpreter);

        return $document;
    }

    protected function validateDocument(DocumentInterface $document, RequestInterpreterInterface $interpreter)
    {
        $validator = $this->documentValidator($interpreter);

        if (!$validator->isValid($document)) {
            throw new ValidationException($validator->getErrors());
        }
    }

    protected function locateRecord(ApiInterface $api)
    {
        $interpreter = $api->getRequestInterpreter();

        if (!$id = $interpreter->getResourceId()) {
            return null;
        }

        $store = $api->getStore();
        $identifier = ResourceIdentifier::create($interpreter->getResourceType(), $id);
        $record = $store->find($identifier);

        if (!$record) {
            throw new JsonApiException([], 404);
        }

        return $record;
    }

    /**
     * @param RequestInterpreterInterface $interpreter
     * @return DocumentValidatorInterface
     */
    private function documentValidator(RequestInterpreterInterface $interpreter)
    {
        if ($interpreter->isModifyRelationship()) {
            return $this->validators->relationshipDocument();
        }

        $validator = $this
            ->validators
            ->resource($interpreter->getResourceType(), $interpreter->getResourceId());

        return $this->validators->resourceDocument($validator);
    }
}
