<?php

namespace CloudCreativity\JsonApi\Http\Client;

use CloudCreativity\JsonApi\Encoder\Encoder;
use GuzzleHttp\Client;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Neomerx\JsonApi\Contracts\Http\Query\QueryParametersParserInterface;
use Neomerx\JsonApi\Http\Headers\MediaType;
use Psr\Http\Message\ResponseInterface;

class GuzzleClient
{

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Encoder
     */
    private $encoder;

    /**
     * GuzzleClient constructor.
     *
     * @param Client $client
     * @param Encoder $encoder
     */
    public function __construct(Client $client, Encoder $encoder)
    {
        $this->client = $client;
        $this->encoder = $encoder;
    }

    /**
     * Send the domain record to the remote JSON API.
     *
     * @param $record
     * @param EncodingParametersInterface|null $parameters
     * @return Response
     */
    public function create($record, EncodingParametersInterface $parameters = null)
    {
        return new Response($this->sendRecord('POST', $record, $parameters));
    }

    /**
     * @param $method
     * @param $record
     * @param EncodingParametersInterface|null $parameters
     * @return ResponseInterface
     */
    private function sendRecord($method, $record, EncodingParametersInterface $parameters = null)
    {
        $encoded = $this->encoder->serializeData($record, $parameters);
        $resourceType = $encoded['data']['type'];
        $resourceId = isset($encoded['data']['id']) ? $encoded['data']['id'] : null;
        $uri = $resourceId ? "$resourceType/$resourceId" : $resourceType;

        return $this->client->request($method, $uri, [
            'json' => $encoded,
            'query' => $parameters ? $this->parseQuery($parameters) : null,
            'headers' => [
                'Content-Type' => MediaType::JSON_API_MEDIA_TYPE,
                'Accept' => MediaType::JSON_API_MEDIA_TYPE,
            ],
        ]);
    }

    /**
     * @param EncodingParametersInterface $parameters
     * @return array
     */
    private function parseQuery(EncodingParametersInterface $parameters)
    {
        return array_filter([
            QueryParametersParserInterface::PARAM_INCLUDE =>
                implode(',', (array) $parameters->getIncludePaths()),
            QueryParametersParserInterface::PARAM_FIELDS =>
                $this->parseQueryFieldsets((array) $parameters->getFieldSets()),
        ]);
    }

    /**
     * @param array $fieldsets
     * @return array
     */
    private function parseQueryFieldsets(array $fieldsets)
    {
        return array_map(function ($values) {
            return implode(',', (array) $values);
        }, $fieldsets);
    }

}
