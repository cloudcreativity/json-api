<?php

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
        $parsed = \json_decode($content, $assoc, $depth, $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw InvalidJsonException::create();
        }

        if (!$assoc && !is_object($parsed)) {
            throw new InvalidJsonException(null, 'JSON is not an object.');
        }

        if ($assoc && !is_array($parsed)) {
            throw new InvalidJsonException(null, 'JSON is not an object or array.');
        }

        return $parsed;
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
     * @param MessageInterface $message
     * @return bool
     */
    function http_contains_body(MessageInterface $message)
    {
        if ($message->hasHeader('Content-Length')) {
            return true;
        };

        return $message->hasHeader('Transfer-Encoding');
    }
}
