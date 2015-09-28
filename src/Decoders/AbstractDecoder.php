<?php

/**
 * Copyright 2015 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Decoders;

use CloudCreativity\JsonApi\Error\ThrowableError;
use Neomerx\JsonApi\Contracts\Decoder\DecoderInterface;

/**
 * Class AbstractDecoder
 * @package CloudCreativity\JsonApi
 */
abstract class AbstractDecoder implements DecoderInterface
{

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
            throw new ThrowableError([
                ThrowableError::TITLE => 'Invalid JSON',
                ThrowableError::DETAIL => 'Request body content could not be parsed as JSON: ' . json_last_error_msg(),
            ], 400);
        }

        return $parsed;
    }
}
