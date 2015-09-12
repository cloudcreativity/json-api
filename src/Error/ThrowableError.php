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

namespace CloudCreativity\JsonApi\Error;

use CloudCreativity\JsonApi\Contracts\Error\ErrorObjectInterface;
use Exception;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use RuntimeException;

/**
 * Class ThrowableError
 * @package CloudCreativity\JsonApi
 */
class ThrowableError extends RuntimeException implements ErrorInterface
{

    const TITLE = ErrorObjectInterface::TITLE;
    const ID = ErrorObjectInterface::ID;
    const LINKS = ErrorObjectInterface::LINKS;
    const DETAIL = ErrorObjectInterface::DETAIL;
    const SOURCE = ErrorObjectInterface::SOURCE;
    const META = ErrorObjectInterface::META;

    /**
     * @var int|string|null
     */
    private $id;

    /**
     * @var array|null
     */
    private $links;

    /**
     * @var int|string
     */
    private $status;

    /**
     * @var string|null
     */
    private $detail;

    /**
     * @var mixed|null
     */
    private $source;

    /**
     * @var array|null
     */
    private $meta;

    /**
     * @param string $titleOrArray
     * @param null $code
     * @param Exception|null $previous
     */
    public function __construct($titleOrArray, $status = null, $code = null, Exception $previous = null)
    {
        if (is_array($titleOrArray)) {
            $this->exchangeArray($titleOrArray);
            $title = isset($titleOrArray[static::TITLE]) ? $titleOrArray[static::TITLE] : null;
        } else {
            $title = $titleOrArray;
        }

        $this->status = ($status) ?: 500;

        parent::__construct($title, $code, $previous);
    }

    /**
     * Get a unique identifier for this particular occurrence of the problem.
     *
     * @return int|string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get links that may lead to further details about this particular occurrence of the problem.
     *
     * @return null|array<string,\Neomerx\JsonApi\Contracts\Schema\LinkInterface>
     */
    public function getLinks()
    {
        return ($this->links) ? (array) $this->links : null;
    }

    /**
     * Get the HTTP status code applicable to this problem, expressed as a string value.
     *
     * @return string|null
     */
    public function getStatus()
    {
        return ($this->status) ? (string) $this->status : null;
    }

    /**
     * Get a short, human-readable summary of the problem.
     *
     * It should not change from occurrence to occurrence of the problem, except for purposes of localization.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->getMessage();
    }

    /**
     * Get a human-readable explanation specific to this occurrence of the problem.
     *
     * @return string|null
     */
    public function getDetail()
    {
        return ($this->detail) ? (string) $this->detail : null;
    }

    /**
     * An object containing references to the source of the error, optionally including any of the following members:
     *    "pointer"   - A JSON Pointer [RFC6901] to the associated entity in the request document
     *                  [e.g. "/data" for a primary data object, or "/data/attributes/title" for a specific attribute].
     *    "parameter" - An optional string indicating which query parameter caused the error.
     *
     * @return mixed|null
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Get error meta information.
     *
     * @return array|null
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @param array $input
     * @return $this
     */
    private function exchangeArray(array $input)
    {
        if (array_key_exists(static::ID, $input)) {
            $this->id = $input[static::ID];
        }

        if (array_key_exists(static::LINKS, $input)) {
            $this->links = $input[static::LINKS];
        }

        if (array_key_exists(static::DETAIL, $input)) {
            $this->detail = $input[static::DETAIL];
        }

        if (array_key_exists(static::SOURCE, $input)) {

            $source = $input[static::SOURCE];

            if (is_array($source)) {
                $source = (new SourceObject())->exchangeArray($source);
            }

            $this->source = $source;
        }

        if (array_key_exists(static::META, $input)) {
            $this->meta = $input[static::META];
        }

        return $this;
    }
}
