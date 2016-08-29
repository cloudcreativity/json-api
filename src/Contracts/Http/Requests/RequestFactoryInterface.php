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

namespace CloudCreativity\JsonApi\Contracts\Http\Requests;

use CloudCreativity\JsonApi\Contracts\Http\ApiInterface;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Psr\Http\Message\ServerRequestInterface;

interface RequestFactoryInterface
{

    /**
     * Build the JSON API request
     *
     * @param ApiInterface $api
     * @param ServerRequestInterface $request
     * @return RequestInterface
     * @throws JsonApiException
     *      if the request is not compliant with the JSON API spec.
     */
    public function build(ApiInterface $api, ServerRequestInterface $request);
}
