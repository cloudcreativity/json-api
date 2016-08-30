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

use CloudCreativity\JsonApi\Contracts\Http\Responses\ErrorResponseInterface;
use CloudCreativity\JsonApi\Contracts\Http\Responses\ResponseFactoryInterface;
use CloudCreativity\JsonApi\Contracts\Pagination\PageInterface;
use CloudCreativity\JsonApi\Contracts\Repositories\ErrorRepositoryInterface;
use CloudCreativity\JsonApi\Exceptions\InvalidArgumentException;
use Neomerx\JsonApi\Contracts\Http\Query\QueryParametersParserInterface;
use Neomerx\JsonApi\Contracts\Http\ResponsesInterface;

/**
 * Class ResponseFactory
 * @package CloudCreativity\JsonApi
 */
class ResponseFactory implements ResponseFactoryInterface
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
     * @inheritdoc
     */
    public function statusCode($statusCode, array $headers = [])
    {
        return $this->responses->getCodeResponse($statusCode, $headers);
    }

    /**
     * @inheritdoc
     */
    public function noContent(array $headers = [])
    {
        return $this->statusCode(204, $headers);
    }

    /**
     * @inheritdoc
     */
    public function meta($meta, $statusCode = 200, array $headers = [])
    {
        return $this->responses->getMetaResponse($meta, $statusCode, $headers);
    }

    /**
     * @inheritdoc
     */
    public function content(
        $data,
        array $links = [],
        $meta = null,
        $statusCode = 200,
        array $headers = []
    ) {
        if ($data instanceof PageInterface) {
            list ($data, $meta, $links) = $this->extractPage($data, $meta, $links);
        }

        return $this->responses->getContentResponse($data, $statusCode, $links, $meta, $headers);
    }

    /**
     * @inheritdoc
     */
    public function created($resource, array $links = [], $meta = null, array $headers = [])
    {
        return $this->responses->getCreatedResponse($resource, $links, $meta, $headers);
    }

    /**
     * @inheritdoc
     */
    public function relationship(
        $data,
        array $links = [],
        $meta = null,
        $statusCode = 200,
        array $headers = []
    ) {
        if ($data instanceof PageInterface) {
            list ($data, $meta, $links) = $this->extractPage($data, $meta, $links);
        }

        return $this->responses->getIdentifiersResponse($data, $statusCode, $links, $meta, $headers);
    }

    /**
     * @inheritdoc
     */
    public function error($errors, $defaultStatusCode = null, array $headers = [])
    {
        if (is_string($errors) && !empty($errors)) {
            $errors = $this->errors->error($errors);
        }

        $response = new ErrorResponse($errors, $defaultStatusCode, $headers);

        return $this->errors($response);
    }

    /**
     * @inheritdoc
     */
    public function errors(ErrorResponseInterface $errors)
    {
        return $this
            ->responses
            ->getErrorResponse($errors->getErrors(), $errors->getHttpCode(), $errors->getHeaders());
    }

    /**
     * @param PageInterface $page
     * @param $meta
     * @param $links
     * @return array
     */
    private function extractPage(PageInterface $page, $meta, $links)
    {
        $key = QueryParametersParserInterface::PARAM_PAGE;

        return [
            $page->getData(),
            $this->mergeMeta($meta, $key, $page->getMeta()),
            $this->mergeLinks($links, $page->getLinks()),
        ];
    }

    /**
     * @param $existing
     * @param $key
     * @param $value
     * @return array
     */
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

    /**
     * @param array $existing
     * @param array $merge
     * @return array
     */
    private function mergeLinks(array $existing, array $merge)
    {
        return array_replace($existing, $merge);
    }
}
