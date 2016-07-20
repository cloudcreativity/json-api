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

namespace CloudCreativity\JsonApi\Contracts\Http;

use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface ContentNegotiatorInterface
 * @package CloudCreativity\JsonApi
 */
interface ContentNegotiatorInterface
{

    /**
     * Do content negotiation as per the JSON API spec.
     *
     * @param CodecMatcherInterface $codecMatcher
     * @param ServerRequestInterface $request
     * @return void
     * @throws JsonApiException
     *      if content negotiation fails.
     * @see http://jsonapi.org/format/#content-negotiation
     */
    public function doContentNegotiation(
        CodecMatcherInterface $codecMatcher,
        ServerRequestInterface $request
    );
}
