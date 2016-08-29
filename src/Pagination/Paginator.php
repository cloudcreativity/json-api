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

namespace CloudCreativity\JsonApi\Pagination;

use CloudCreativity\JsonApi\Contracts\Pagination\PaginatorInterface;
use CloudCreativity\JsonApi\Contracts\Http\ApiInterface;
use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterface;

class Paginator implements PaginatorInterface
{

    private $api;

    private $request;

    public function __construct(ApiInterface $api, RequestInterface $request)
    {
        $this->api = $api;
        $this->request = $request;
    }

    /**
     * @inheritdoc
     */
    public function getCurrentPage()
    {
        $key = $this->api->getPagingStrategy()->getPage();
        $page = $this->getParam($key);
        $page = is_numeric($page) ? (int) $page : 0;

        return 0 < $page ? $page : null;
    }

    /**
     * Get the number of resources per-page.
     *
     * @param int $default
     *      the default to use if the client has not specified a per-page amount.
     * @param int|null $max
     *      the maximum allowed per-page, or null if no maximum.
     * @return int
     */
    public function getPerPage($default = 15, $max = null)
    {
        $key = $this->api->getPagingingStrategy()->getPerPage();
        $perPage = (int) $this->getParam($key, $default);

        if (is_int($max) && $perPage > $max) {
            $perPage = $max;
        }

        if (1 > $perPage) {
            $perPage = 1;
        }

        return (0 < $perPage) ? $perPage : 1;
    }

    /**
     * @return array
     */
    protected function getParams()
    {
        $requestParameters = $this->request->getParameters();

        return (array) $requestParameters->getPaginationParameters();
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getParam($key, $default = null)
    {
        $params = $this->getParams();

        return isset($params[$key]) ? $params[$key] : null;
    }
}
