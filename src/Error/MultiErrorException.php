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

use CloudCreativity\JsonApi\Contracts\Error\ErrorCollectionInterface;
use CloudCreativity\JsonApi\Contracts\Error\ErrorsAwareInterface;

/**
 * Class MultiErrorException
 * @package CloudCreativity\JsonApi
 */
class MultiErrorException extends \RuntimeException implements ErrorsAwareInterface
{

    /**
     * @var ErrorCollectionInterface
     */
    private $errors;

    /**
     * @param ErrorCollectionInterface $errors
     * @param $message
     * @param \Exception|null $previous
     */
    public function __construct(ErrorCollectionInterface $errors, $message = null, \Exception $previous = null)
    {
        parent::__construct($message, null, $previous);

        $this->errors = $errors;
    }

    /**
     * @return ErrorCollectionInterface
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
