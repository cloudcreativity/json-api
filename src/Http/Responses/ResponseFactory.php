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

namespace CloudCreativity\JsonApi\Http\Responses;

use CloudCreativity\JsonApi\Contracts\Http\HttpServiceInterface;
use CloudCreativity\JsonApi\Contracts\Http\Responses\ErrorResponseInterface;
use CloudCreativity\JsonApi\Contracts\Http\Responses\ResponseFactoryInterface;
use CloudCreativity\JsonApi\Contracts\Pagination\PageInterface;
use Neomerx\JsonApi\Contracts\Document\DocumentInterface;
use Neomerx\JsonApi\Contracts\Http\ResponsesInterface;

/**
 * Class ResponseFactory
 *
 * @package CloudCreativity\JsonApi
 */
class ResponseFactory implements ResponseFactoryInterface
{

    /**
     * @var ResponsesInterface
     */
    private $responses;

    /**
     * @var HttpServiceInterface
     */
    private $httpService;

    /**
     * ResponseFactory constructor.
     *
     * @param ResponsesInterface $responses
     * @param HttpServiceInterface $httpService
     */
    public function __construct(ResponsesInterface $responses, HttpServiceInterface $httpService)
    {
        $this->responses = $responses;
        $this->httpService = $httpService;
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
        if (is_string($errors)) {
            $errors = $this->httpService->getApi()->getErrors()->error($errors);
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
        return [
            $page->getData(),
            $this->mergePageMeta($meta, $page),
            $this->mergePageLinks($links, $page),
        ];
    }

    /**
     * @param object|array|null $existing
     * @param PageInterface $page
     * @return array
     */
    private function mergePageMeta($existing, PageInterface $page)
    {
        if (!$merge = $page->getMeta()) {
            return $existing;
        }

        $existing = (array) $existing ?: [];

        if ($key = $page->getMetaKey()) {
            $existing[$key] = $merge;
            return $existing;
        }

        return array_replace($existing, (array) $merge);
    }

    /**
     * @param array $existing
     * @param PageInterface $page
     * @return array
     */
    private function mergePageLinks(array $existing, PageInterface $page)
    {
        return array_replace($existing, array_filter([
            DocumentInterface::KEYWORD_FIRST => $page->getFirstLink(),
            DocumentInterface::KEYWORD_PREV => $page->getPreviousLink(),
            DocumentInterface::KEYWORD_NEXT => $page->getNextLink(),
            DocumentInterface::KEYWORD_LAST => $page->getLastLink(),
        ]));
    }

}
