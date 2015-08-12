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

use CloudCreativity\JsonApi\Contracts\Error\ErrorsAwareInterface;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;

/**
 * Class ErrorException
 * @package CloudCreativity\JsonApi
 */
class ErrorException extends \RuntimeException implements ErrorsAwareInterface
{

    /**
     * @var ErrorInterface
     */
    protected $_error;

    /**
     * @param ErrorInterface $error
     * @param \Exception|null $previous
     */
    public function __construct(ErrorInterface $error, \Exception $previous = null)
    {
        parent::__construct($error->getTitle(), $error->getCode(), $previous);

        $this->_error = $error;
    }

    /**
     * @return ErrorInterface
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * @return ErrorCollection
     */
    public function getErrors()
    {
        return new ErrorCollection([$this->getError()]);
    }
}