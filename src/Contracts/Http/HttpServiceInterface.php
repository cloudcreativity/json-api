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

namespace CloudCreativity\JsonApi\Contracts\Http;

use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterface;
use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterpreterInterface;
use CloudCreativity\JsonApi\Exceptions\RuntimeException;

/**
 * Interface HttpServiceInterface
 * @package CloudCreativity\JsonApi
 */
interface HttpServiceInterface
{

    /**
     * Get the interpreter to use to analyze the inbound HTTP request.
     *
     * @return RequestInterpreterInterface
     */
    public function getRequestInterpreter();

    /**
     * Get the JSON API that is handling the current HTTP request.
     *
     * @return ApiInterface
     * @throws RuntimeException
     *      if there is no current JSON API
     */
    public function getApi();

    /**
     * Is the current request being handled by a JSON API?
     *
     * @return bool
     */
    public function hasApi();

    /**
     * Get the JSON API request sent by the client.
     *
     * @return RequestInterface
     * @throws RuntimeException
     *      if there is no current valid JSON API request
     */
    public function getRequest();

    /**
     * Is there a current JSON API request?
     *
     * @return bool
     */
    public function hasRequest();
}
