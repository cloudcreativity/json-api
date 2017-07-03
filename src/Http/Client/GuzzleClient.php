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

namespace CloudCreativity\JsonApi\Http\Client;

use CloudCreativity\JsonApi\Contracts\Encoder\SerializerInterface;
use CloudCreativity\JsonApi\Contracts\Http\Client\ClientInterface;
use CloudCreativity\JsonApi\Contracts\Http\Responses\ResponseInterface;
use CloudCreativity\JsonApi\Factories\Factory;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface;
use Neomerx\JsonApi\Exceptions\JsonApiException;

/**
 * Class GuzzleClient
 *
 * @package CloudCreativity\JsonApi
 */
class GuzzleClient implements ClientInterface
{

    use SendsRequestsTrait;

    /**
     * @var Client
     */
    private $http;

    /**
     * GuzzleClient constructor.
     *
     * @param Factory $factory
     * @param Client $http
     * @param ContainerInterface $schemas
     * @param SerializerInterface $serializer
     */
    public function __construct(
        Factory $factory,
        Client $http,
        ContainerInterface $schemas,
        SerializerInterface $serializer
    ) {
        $this->factory = $factory;
        $this->http = $http;
        $this->schemas = $schemas;
        $this->serializer = $serializer;
    }

    /**
     * @inheritdoc
     */
    public function index($resourceType, EncodingParametersInterface $parameters = null)
    {
        return $this->request('GET', $this->resourceUri($resourceType), [
            'headers' => $this->normalizeHeaders(),
            'query' => $parameters ? $this->parseSearchQuery($parameters) : null,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function create($record, EncodingParametersInterface $parameters = null)
    {
        return $this->sendRecord('POST', $this->serializeRecord($record), $parameters);
    }

    /**
     * @inheritdoc
     */
    public function read($resourceType, $resourceId, EncodingParametersInterface $parameters = null)
    {
        $uri = $this->resourceUri($resourceType, $resourceId);

        return $this->request('GET', $uri, [
            'headers' => $this->normalizeHeaders(),
            'query' => $parameters ? $this->parseQuery($parameters) : null,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function update($record, array $fields = [], EncodingParametersInterface $parameters = null)
    {
        return $this->sendRecord('PATCH', $this->serializeRecord($record, $fields), $parameters);
    }

    /**
     * @inheritdoc
     */
    public function delete($record)
    {
        return $this->request('DELETE', $this->recordUri($record));
    }

    /**
     * @param $method
     * @param array $serializedRecord
     *      the encoded record
     * @param EncodingParametersInterface|null $parameters
     * @return ResponseInterface
     */
    protected function sendRecord($method, array $serializedRecord, EncodingParametersInterface $parameters = null)
    {
        $resourceType = $serializedRecord['data']['type'];

        if ('POST' === $method) {
            $uri = $this->resourceUri($resourceType);
        } else {
            $resourceId = isset($serializedRecord['data']['id']) ? $serializedRecord['data']['id'] : null;
            $uri = $this->resourceUri($resourceType, $resourceId);
        }

        return $this->request($method, $uri, [
            'json' => $serializedRecord,
            'query' => $parameters ? $this->parseQuery($parameters) : null,
            'headers' => $this->normalizeHeaders(true),
        ]);
    }

    /**
     * @param $method
     * @param $uri
     * @param array $options
     * @return ResponseInterface
     * @throws JsonApiException
     */
    protected function request($method, $uri, array $options = [])
    {
        try {
            $response = $this->http->request($method, $uri, $options);
        } catch (BadResponseException $ex) {
            throw $this->parseErrorResponse($ex);
        }

        return $this->factory->createResponse($response);
    }

    /**
     * Safely parse an error response.
     *
     * This method wraps decoding the body content of the provided exception, so that
     * another exception is not thrown while trying to parse an existing exception.
     *
     * @param BadResponseException $ex
     * @return JsonApiException
     */
    private function parseErrorResponse(BadResponseException $ex)
    {
        try {
            $response = $ex->getResponse();
            $document = $response ? $this->factory->createDocumentObject($response) : null;
            $errors = $document ? $document->getErrors() : [];
            $statusCode = $response ? $response->getStatusCode() : 0;
        } catch (Exception $e) {
            $errors = [];
            $statusCode = 0;
        }

        return new JsonApiException($errors, $statusCode, $ex);
    }

}
