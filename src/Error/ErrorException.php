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
use CloudCreativity\JsonApi\Contracts\Error\ErrorsAwareInterface;
use Exception;
use InvalidArgumentException;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use RuntimeException;

/**
 * Class ErrorException
 * @package CloudCreativity\JsonApi
 */
class ErrorException extends RuntimeException implements ErrorsAwareInterface
{

    const ID = ErrorObjectInterface::ID;
    const LINKS = ErrorObjectInterface::LINKS;
    const STATUS = ErrorObjectInterface::STATUS;
    const CODE = ErrorObjectInterface::CODE;
    const TITLE = ErrorObjectInterface::TITLE;
    const DETAIL = ErrorObjectInterface::DETAIL;
    const SOURCE = ErrorObjectInterface::SOURCE;
    const META = ErrorObjectInterface::META;

    /**
     * @var ErrorInterface
     */
    private $error;

    /**
     * @param ErrorInterface|array $errorOrArray
     * @param Exception|null $previous
     */
    public function __construct($errorOrArray, Exception $previous = null)
    {
        if (is_array($errorOrArray)) {
            $error = new ErrorObject($errorOrArray);
        } elseif ($errorOrArray instanceof ErrorInterface) {
            $error = $errorOrArray;
        } else {
            throw new InvalidArgumentException(sprintf('Expecting an %s object or an array to cast to an error object.', ErrorInterface::class));
        }

        $code = is_numeric($error->getCode()) ? (int) $error->getCode() : null;

        parent::__construct($error->getTitle(), $code, $previous);

        $this->error = $error;
    }

    /**
     * @return ErrorInterface
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return ErrorCollection
     */
    public function getErrors()
    {
        return new ErrorCollection([$this->getError()]);
    }
}
