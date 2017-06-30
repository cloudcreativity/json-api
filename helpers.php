<?php

namespace CloudCreativity\JsonApi;

use CloudCreativity\JsonApi\Exceptions\InvalidJsonException;
use Neomerx\JsonApi\Exceptions\JsonApiException;

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
