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

namespace CloudCreativity\JsonApi\Exceptions;

use Exception;
use Neomerx\JsonApi\Exceptions\JsonApiException;

/**
 * Class InvalidJsonException
 * @package CloudCreativity\JsonApi
 */
class InvalidJsonException extends JsonApiException
{

    /**
     * @var int
     */
    private $jsonError;

    /**
     * @var string
     */
    private $jsonErrorMessage;

    /**
     * @param int $defaultHttpCode
     * @param Exception|null $previous
     * @return InvalidJsonException
     */
    public static function create($defaultHttpCode = self::HTTP_CODE_BAD_REQUEST, Exception $previous = null)
    {
        return new self(json_last_error(), json_last_error_msg(), $defaultHttpCode, $previous);
    }

    /**
     * InvalidJsonException constructor.
     * @param int $jsonError
     * @param string $jsonErrorMessage
     * @param int $defaultHttpCode
     * @param Exception|null $previous
     */
    public function __construct(
        $jsonError,
        $jsonErrorMessage,
        $defaultHttpCode = self::HTTP_CODE_BAD_REQUEST,
        Exception $previous = null
    ) {
        parent::__construct([], $defaultHttpCode, $previous);
        $this->jsonError = $jsonError;
        $this->jsonErrorMessage = $jsonErrorMessage;
    }

    /**
     * @return int
     */
    public function getJsonError()
    {
        return $this->jsonError;
    }

    /**
     * @return string
     */
    public function getJsonErrorMessage()
    {
        return $this->jsonErrorMessage;
    }
}
