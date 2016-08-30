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

use CloudCreativity\JsonApi\Contracts\Http\HttpServiceInterface;
use CloudCreativity\JsonApi\Contracts\Pagination\PaginatorInterface;

/**
 * Class Paginator
 * @package CloudCreativity\JsonApi
 */
class Paginator implements PaginatorInterface
{

    /**
     * @var HttpServiceInterface
     */
    private $service;

    /**
     * Paginator constructor.
     * @param HttpServiceInterface $service
     */
    public function __construct(HttpServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * @inheritdoc
     */
    public function getCurrentPage()
    {
        $key = $this->service->getApi()->getPagingStrategy()->getPage();
        $page = $this->getParam($key);
        $page = is_numeric($page) ? (int) $page : 0;

        return 0 < $page ? $page : null;
    }

    /**
     * @inheritdoc
     */
    public function getPerPage($default = 15, $max = null)
    {
        $key = $this->service->getApi()->getPagingStrategy()->getPerPage();
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
        $encodingParameters = $this->service->getRequest()->getParameters();

        return (array) $encodingParameters->getPaginationParameters();
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
