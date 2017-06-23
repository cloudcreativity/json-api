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

namespace CloudCreativity\JsonApi\Http\Middleware;

use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Contracts\Http\HttpFactoryInterface;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Trait NegotiatesContent
 *
 * @package CloudCreativity\JsonApi\Http\Middleware
 */
trait NegotiatesContent
{

    /**
     * Perform content negotiation.
     *
     * @param HttpFactoryInterface $httpFactory
     * @param ServerRequestInterface $request
     * @param CodecMatcherInterface $codecMatcher
     * @throws JsonApiException
     * @see http://jsonapi.org/format/#content-negotiation
     */
    protected function doContentNegotiation(
        HttpFactoryInterface $httpFactory,
        ServerRequestInterface $request,
        CodecMatcherInterface $codecMatcher
    ) {
        $parser = $httpFactory->createHeaderParametersParser();
        $checker = $httpFactory->createHeadersChecker($codecMatcher);

        $checker->checkHeaders($parser->parse($request, $this->doesContainBody($request)));
    }

    /**
     * Does the request contain a message body?
     *
     * "The presence of a message-body in a request is signaled by the inclusion of a Content-Length or
     * Transfer-Encoding header field in the request's message-headers."
     * https://www.w3.org/Protocols/rfc2616/rfc2616-sec4.html#sec4.3
     *
     * @param ServerRequestInterface $request
     * @return bool
     */
    protected function doesContainBody(ServerRequestInterface $request)
    {
        return $request->hasHeader('Content-Length') || $request->hasHeader('Transfer-Encoding');
    }
}
