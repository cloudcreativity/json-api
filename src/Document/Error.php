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

namespace CloudCreativity\JsonApi\Document;

use Neomerx\JsonApi\Contracts\Document\DocumentInterface;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use Neomerx\JsonApi\Document\Error as BaseError;
use Neomerx\JsonApi\Exceptions\JsonApiException;

/**
 * Class Error
 * @package CloudCreativity\JsonApi
 */
class Error extends BaseError
{

    /** Keywords for array exchanging */
    const ID = DocumentInterface::KEYWORD_ERRORS_ID;
    const STATUS = DocumentInterface::KEYWORD_ERRORS_STATUS;
    const CODE = DocumentInterface::KEYWORD_ERRORS_CODE;
    const TITLE = DocumentInterface::KEYWORD_ERRORS_TITLE;
    const DETAIL = DocumentInterface::KEYWORD_ERRORS_DETAIL;
    const META = DocumentInterface::KEYWORD_ERRORS_META;
    const SOURCE = DocumentInterface::KEYWORD_ERRORS_SOURCE;
    const LINKS = DocumentInterface::KEYWORD_ERRORS_LINKS;
    const LINKS_ABOUT = DocumentInterface::KEYWORD_ERRORS_ABOUT;

    /**
     * Create an error object from an array.
     *
     * @param array $input
     * @return Error
     */
    public static function create(array $input)
    {
        $id = isset($input[self::ID]) ? $input[self::ID] : null;
        $links = isset($input[self::LINKS]) ? $input[self::LINKS] : [];
        $aboutLink = isset($links[self::LINKS_ABOUT]) ? $links[self::LINKS_ABOUT] : null;
        $status = isset($input[self::STATUS]) ? $input[self::STATUS] : null;
        $code = isset($input[self::CODE]) ? $input[self::CODE] : null;
        $title = isset($input[self::TITLE]) ? $input[self::TITLE] : null;
        $detail = isset($input[self::DETAIL]) ? $input[self::DETAIL] : null;
        $source = isset($input[self::SOURCE]) ? $input[self::SOURCE] : null;
        $meta = isset($input[self::META]) ? $input[self::META] : null;

        return new self(
            $id,
            $aboutLink,
            $status,
            $code,
            $title,
            $detail,
            $source,
            $meta
        );
    }

    /**
     * Create an error object from an array, ensuring the pointer is as specified.
     *
     * @param array $input
     * @param $pointer
     * @return Error
     */
    public static function createWithPointer(array $input, $pointer)
    {
        if (!isset($input[self::SOURCE]) || !is_array($input[self::SOURCE])) {
            $input[self::SOURCE] = [];
        }

        $input[self::SOURCE][self::SOURCE_POINTER] = $pointer;

        return self::create($input);
    }

    /**
     * Create an error object from an array, ensuring the parameter is as specified.
     *
     * @param array $input
     * @param $parameter
     * @return Error
     */
    public static function createWithParameter(array $input, $parameter)
    {
        if (!isset($input[self::SOURCE]) || !is_array($input[self::SOURCE])) {
            $input[self::SOURCE] = [];
        }

        $input[self::SOURCE][self::SOURCE_PARAMETER] = $parameter;

        return self::create($input);
    }

    /**
     * Get the most applicable HTTP status code.
     *
     * From the spec:
     * When a server encounters multiple problems for a single request, the most generally applicable HTTP error
     * code SHOULD be used in the response. For instance, 400 Bad Request might be appropriate for multiple
     * 4xx errors or 500 Internal Server Error might be appropriate for multiple 5xx errors.
     *
     * @param ErrorInterface|ErrorInterface[]|ErrorCollection
     * @param string|int $default
     *      the default to use if an error status cannot be resolved.
     * @return string|int
     */
    public static function resolveHttpStatus($errors, $default = JsonApiException::DEFAULT_HTTP_CODE)
    {
        if ($errors instanceof ErrorInterface) {
            return $errors->getStatus() ?: $default;
        }

        $request = null;
        $internal = null;

        /** @var ErrorInterface $error */
        foreach ($errors as $error) {

            $status = $error->getStatus();

            if (400 <= $status && 499 >= $status) {
                $request = is_null($request) ? $status : ($request == $status) ? $status : 400;
            } elseif (500 <= $status && 599 >= $status) {
                $internal = is_null($internal) ? $status : ($internal == $status) ? $status : 500;
            }
        }

        if (!$request && !$internal) {
            return $default;
        } elseif ($request && $internal) {
            return 500;
        }

        return $request ?: $internal;
    }
}
