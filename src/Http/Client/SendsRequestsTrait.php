<?php

namespace CloudCreativity\JsonApi\Http\Client;

use CloudCreativity\JsonApi\Contracts\Object\DocumentInterface;
use CloudCreativity\JsonApi\Contracts\Object\ResourceIdentifierInterface;
use CloudCreativity\JsonApi\Encoder\Encoder;
use CloudCreativity\JsonApi\Object\Document;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Neomerx\JsonApi\Contracts\Http\Query\QueryParametersParserInterface;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface;
use Neomerx\JsonApi\Encoder\Parameters\EncodingParameters;
use Neomerx\JsonApi\Http\Headers\MediaType;
use Psr\Http\Message\ResponseInterface;
use function CloudCreativity\JsonApi\json_decode;

trait SendsRequestsTrait
{

    /**
     * @var ContainerInterface
     */
    protected $schemas;

    /**
     * @var Encoder
     */
    protected $encoder;

    /**
     * @param $record
     * @param array $fields
     * @return array
     */
    protected function serializeRecord($record, array $fields = [])
    {
        $resourceType = $this->schemas->getSchema($record)->getResourceType();
        $parameters = $fields ? new EncodingParameters(null, [$resourceType => $fields]) : null;

        return $this->encoder->serializeData($record, $parameters);
    }

    /**
     * @param object $record
     * @return string
     */
    protected function recordUri($record)
    {
        $schema = $this->schemas->getSchema($record);

        return $this->resourceUri($schema->getResourceType(), $schema->getId($record));
    }

    /**
     * @param ResourceIdentifierInterface|string $resourceType
     * @param string|null $resourceId
     * @return string
     */
    protected function resourceUri($resourceType, $resourceId = null)
    {
        if ($resourceType instanceof ResourceIdentifierInterface) {
            $resourceId = $resourceType->getId();
            $resourceType = $resourceType->getType();
        }

        return $resourceId ? "$resourceType/$resourceId" : $resourceType;
    }

    /**
     * @param bool $body
     *      whether HTTP request body is being sent.
     * @param array $existing
     * @return array
     */
    protected function normalizeHeaders($body = false, array $existing = [])
    {
        $existing['Accept'] = MediaType::JSON_API_MEDIA_TYPE;
        $existing['Content-Type'] = $body ? MediaType::JSON_API_MEDIA_TYPE : null;

        return array_filter($existing);
    }

    /**
     * @param EncodingParametersInterface $parameters
     * @return array
     */
    protected function parseQuery(EncodingParametersInterface $parameters)
    {
        return array_filter(array_merge((array) $parameters->getUnrecognizedParameters(), [
            QueryParametersParserInterface::PARAM_INCLUDE =>
                implode(',', (array) $parameters->getIncludePaths()),
            QueryParametersParserInterface::PARAM_FIELDS =>
                $this->parseQueryFieldsets((array) $parameters->getFieldSets()),
        ]));
    }

    /**
     * @param EncodingParametersInterface $parameters
     * @return array
     */
    protected function parseSearchQuery(EncodingParametersInterface $parameters)
    {
        return array_filter(array_merge($this->parseQuery($parameters), [
            QueryParametersParserInterface::PARAM_SORT =>
                implode(',', (array) $parameters->getSortParameters()),
            QueryParametersParserInterface::PARAM_PAGE =>
                $parameters->getPaginationParameters(),
            QueryParametersParserInterface::PARAM_FILTER =>
                $parameters->getFilteringParameters(),
        ]));
    }

    /**
     * @param ResponseInterface $response
     * @return DocumentInterface
     */
    protected function decode(ResponseInterface $response)
    {
        $content = (string) $response->getBody();

        return new Document(json_decode($content));
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
