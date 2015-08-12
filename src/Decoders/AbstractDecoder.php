<?php

namespace CloudCreativity\JsonApi\Decoders;

use CloudCreativity\JsonApi\Error\ErrorException;
use CloudCreativity\JsonApi\Error\ErrorObject;
use Neomerx\JsonApi\Contracts\Decoder\DecoderInterface;

abstract class AbstractDecoder implements DecoderInterface
{

    const ERROR_INVALID_JSON = 'invalid-json';

    /**
     * @param $content
     * @param bool|false $assoc
     * @param int $depth
     * @param int $options
     * @return mixed
     */
    public function parseJson($content, $assoc = false, $depth = 512, $options = 0)
    {
        $parsed = json_decode($content, $assoc, $depth, $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            $error = new ErrorObject([
                ErrorObject::TITLE => 'Invalid JSON',
                ErrorObject::DETAIL => 'Request body content could not be parsed as JSON: ' . json_last_error_msg(),
                ErrorObject::CODE => static::ERROR_INVALID_JSON,
                ErrorObject::STATUS => 400,
            ]);

            throw new ErrorException($error);
        }

        return $parsed;
    }
}