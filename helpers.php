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

namespace CloudCreativity\JsonApi;

use CloudCreativity\JsonApi\Exceptions\InvalidJsonException;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Psr\Http\Message\MessageInterface;

if (!function_exists('CloudCreativity\JsonApi\json_decode')) {
    /**
     * Decodes a JSON string.
     *
     * @param $content
     * @param bool $assoc
     * @param int $depth
     * @param int $options
     * @return mixed
     * @throws JsonApiException
     */
    function json_decode($content, $assoc = false, $depth = 512, $options = 0)
    {
        $decoded = \json_decode($content, $assoc, $depth, $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw InvalidJsonException::create();
        }

        if (!$assoc && !is_object($decoded)) {
            throw new InvalidJsonException(null, 'JSON is not an object.');
        }

        if ($assoc && !is_array($decoded)) {
            throw new InvalidJsonException(null, 'JSON is not an object or array.');
        }

        return $decoded;
    }
}

if (!function_exists('CloudCreativity\JsonApi\http_contains_body')) {
    /**
     * Does the HTTP message contain body content?
     *
     * "The presence of a message-body in a request is signaled by the inclusion of a Content-Length or
     * Transfer-Encoding header field in the request's message-headers."
     * https://www.w3.org/Protocols/rfc2616/rfc2616-sec4.html#sec4.3
     *
     * However, some browsers send a Content-Length header with an empty string for e.g. GET requests
     * without any message-body. Therefore rather than checking for the existence of a Content-Length
     * header, we will allow an empty value to indicate that the request does not contain body.
     *
     * @param MessageInterface $message
     * @return bool
     */
    function http_contains_body(MessageInterface $message)
    {
        if ($message->hasHeader('Transfer-Encoding')) {
            return true;
        };

        if (!$contentLength = $message->getHeader('Content-Length')) {
            return false;
        }

        return 0 < $contentLength[0];
    }
}
