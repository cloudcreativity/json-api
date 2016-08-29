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

namespace CloudCreativity\JsonApi\Http\Responses;

use CloudCreativity\JsonApi\Contracts\Repositories\ErrorRepositoryInterface;
use CloudCreativity\JsonApi\Exceptions\MutableErrorCollection as Errors;
use CloudCreativity\JsonApi\Exceptions\InvalidArgumentException;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use Neomerx\JsonApi\Contracts\Http\ResponsesInterface;
use Neomerx\JsonApi\Exceptions\ErrorCollection;
use Neomerx\JsonApi\Contracts\Http\Query\QueryParametersParserInterface;
use CloudCreativity\JsonApi\Contracts\Pagination\PageInterface;

/**
 * Class ResponseFactory
 * @package CloudCreativity\JsonApi
 */
class ResponseFactory
{

    /**
     * @var ResponsesInterface
     */
    private $responses;

    /**
     * @var ErrorRepositoryInterface
     */
    private $errors;

    /**
     * ResponseFactory constructor.
     * @param ResponsesInterface $responses
     * @param ErrorRepositoryInterface $errors
     */
    public function __construct(
        ResponsesInterface $responses,
        ErrorRepositoryInterface $errors
    ) {
        $this->responses = $responses;
        $this->errors = $errors;
    }

    /**
     * @param $statusCode
     * @param array $headers
     * @return Response
     */
    public function statusCode($statusCode, array $headers = [])
    {
        /** @var Response $response */
        return $this->responses->getCodeResponse($statusCode, $headers);
    }

    /**
     * @param array $headers
     * @return Response
     */
    public function noContent(array $headers = [])
    {
        return $this->statusCode(Response::HTTP_NO_CONTENT, $headers);
    }

    /**
     * @param mixed $meta
     * @param int $statusCode
     * @param array $headers
     * @return Response
     */
    public function meta($meta, $statusCode = Response::HTTP_OK, array $headers = [])
    {
        return $this->responses->getMetaResponse($meta, $statusCode, $headers);
    }

    /**
     * @param mixed $data
     * @param array $links
     * @param mixed|null $meta
     * @param int $statusCode
     * @param array $headers
     * @return Response
     */
    public function content(
        $data,
        array $links = [],
        $meta = null,
        $statusCode = Response::HTTP_OK,
        array $headers = []
    ) {
        if ($data instanceof PageInterface) {
            list ($data, $meta, $links) = $this->extractPage($data, $meta, $links);
        }

        return $this->responses->getContentResponse($data, $statusCode, $links, $meta, $headers);
    }

    /**
     * @param object $resource
     * @param array $links
     * @param mixed|null $meta
     * @param array $headers
     * @return Response
     */
    public function created($resource, array $links = [], $meta = null, array $headers = [])
    {
        return $this->responses->getCreatedResponse($resource, $links, $meta, $headers);
    }

    /**
     * @param $data
     * @param array $links
     * @param mixed|null $meta
     * @param int $statusCode
     * @param array $headers
     * @return Response
     */
    public function relationship(
        $data,
        array $links = [],
        $meta = null,
        $statusCode = Response::HTTP_OK,
        array $headers = []
    ) {
        if ($data instanceof PageInterface) {
            list ($data, $meta, $links) = $this->extractPage($data, $meta, $links);
        }

        return $this->responses->getIdentifiersResponse($data, $statusCode, $links, $meta, $headers);
    }

    /**
     * @param ErrorInterface|string $error
     *      the error object or a string error key to get the error from the repository.
     * @param int|string|null $statusCode
     * @param array $headers
     * @return Response
     */
    public function error($error, $statusCode = null, array $headers = [])
    {
        if (is_string($error) && !empty($error)) {
            $error = $this->errors->error($error);
        }

        if (!$error instanceof ErrorInterface) {
            throw new InvalidArgumentException('Expecting an error object or a string error key.');
        }

        return $this->errors($error, $statusCode, $headers);
    }

    /**
     * @param ErrorInterface|ErrorInterface[]|ErrorCollection $errors
     * @param int|string|null $statusCode
     * @param array $headers
     * @return Response
     */
    public function errors($errors, $statusCode = null, array $headers = [])
    {
        if (is_null($statusCode)) {
            $statusCode = Errors::cast($errors)->getHttpStatus();
        }

        return $this->responses->getErrorResponse($errors, $statusCode, $headers);
    }

    private function mergeMeta($existing, $key, $value)
    {
        $existing = $existing ?: [];

        if (is_array($existing)) {
            $existing[$key] = $value;
        } elseif (is_object($existing)) {
            $existing->{$key} = $value;
        } else {
            throw new InvalidArgumentException('Meta is not a valid value - expecting an array or object.');
        }

        return $existing;
    }

    private function mergeLinks(array $existing, array $merge)
    {
        return array_replace($existing, $merge);
    }

    private function extractPage(PageInterface $page, $meta, $links)
    {
        $key = QueryParametersParserInterface::PARAM_PAGE;

        return [
            $page->getData(),
            $this->mergeMeta($meta, $key, $page->getMeta()),
            $this->mergeLinks($links, $page->getLinks()),
        ];
    }
}
