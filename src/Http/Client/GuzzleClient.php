<?php

namespace CloudCreativity\JsonApi\Http\Client;

use CloudCreativity\JsonApi\Contracts\Object\ResourceIdentifierInterface;
use CloudCreativity\JsonApi\Encoder\Encoder;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Psr\Http\Message\ResponseInterface;

class GuzzleClient
{

    use SendsRequestsTrait;

    /**
     * @var Client
     */
    private $http;

    /**
     * GuzzleClient constructor.
     *
     * @param Client $http
     * @param ContainerInterface $schemas
     * @param Encoder $encoder
     */
    public function __construct(Client $http, ContainerInterface $schemas, Encoder $encoder)
    {
        $this->http = $http;
        $this->schemas = $schemas;
        $this->encoder = $encoder;
    }

    /**
     * @param $resourceType
     * @param EncodingParametersInterface|null $parameters
     * @return Response
     * @throws JsonApiException
     *      if the remote server replies with an error.
     */
    public function index($resourceType, EncodingParametersInterface $parameters = null)
    {
        return new Response($this->request('GET', $this->resourceUri($resourceType), [
            'headers' => $this->normalizeHeaders(),
            'query' => $parameters ? $this->parseSearchQuery($parameters) : null,
        ]));
    }

    /**
     * Send the domain record to the remote JSON API.
     *
     * @param object $record
     * @param EncodingParametersInterface|null $parameters
     * @return Response
     * @throws JsonApiException
     *      if the remote server replies with an error.
     */
    public function create($record, EncodingParametersInterface $parameters = null)
    {
        return new Response($this->sendRecord(
            'POST',
            $this->serializeRecord($record),
            $parameters
        ));
    }

    /**
     * Read the domain record from the remote JSON API.
     *
     * @param ResourceIdentifierInterface $identifier
     * @param EncodingParametersInterface|null $parameters
     * @return Response
     * @throws JsonApiException
     *      if the remote server replies with an error.
     */
    public function read(ResourceIdentifierInterface $identifier, EncodingParametersInterface $parameters = null)
    {
        $uri = $this->resourceUri($identifier);

        $response = $this->request('GET', $uri, [
            'headers' => $this->normalizeHeaders(),
            'query' => $parameters ? $this->parseQuery($parameters) : null,
        ]);

        return new Response($response);
    }

    /**
     * Update the domain record with the remote JSON API.
     *
     * @param object $record
     * @param string[] $fields
     *      the fields to send, if sending sparse field-sets.
     * @param EncodingParametersInterface|null $parameters
     * @return Response
     * @throws JsonApiException
     *      if the remote server replies with an error.
     */
    public function update($record, array $fields = [], EncodingParametersInterface $parameters = null)
    {
        return new Response($this->sendRecord(
            'PATCH',
            $this->serializeRecord($record, $fields),
            $parameters
        ));
    }

    /**
     * Delete the domain record from the remote JSON API.
     *
     * @param object $record
     * @return Response
     * @throws JsonApiException
     *      if the remote server replies with an error.
     */
    public function delete($record)
    {
        return new Response($this->request('DELETE', $this->recordUri($record)));
    }

    /**
     * @param $method
     * @param array $serializedRecord
     *      the encoded record
     * @param EncodingParametersInterface|null $parameters
     * @return ResponseInterface
     */
    private function sendRecord($method, array $serializedRecord, EncodingParametersInterface $parameters = null)
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
    private function request($method, $uri, array $options = [])
    {
        try {
            return $this->http->request($method, $uri, $options);
        } catch (BadResponseException $ex) {
            $statusCode = $ex->getResponse() ? $ex->getResponse()->getStatusCode() : null;
            throw new JsonApiException([], $statusCode, $ex);
        }
    }

}
