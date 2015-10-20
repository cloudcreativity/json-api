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

namespace CloudCreativity\JsonApi\Exceptions;

use CloudCreativity\JsonApi\Error\ErrorException;
use Neomerx\JsonApi\Contracts\Integration\ExceptionThrowerInterface;

/**
 * Class ExceptionThrower
 * @package CloudCreativity\JsonApi
 */
class ExceptionThrower implements ExceptionThrowerInterface
{

    /**
     * Throw 'Bad request' exception (HTTP code 400).
     *
     * @return void
     */
    public function throwBadRequest()
    {
        throw ErrorException::create('Bad Request', 400);
    }

    /**
     * Throw 'Forbidden' exception (HTTP code 403).
     *
     * @return void
     */
    public function throwForbidden()
    {
        throw ErrorException::create('Forbidden', 403);
    }

    /**
     * Throw 'Not Acceptable' exception (HTTP code 406).
     *
     * @return void
     */
    public function throwNotAcceptable()
    {
        throw ErrorException::create('Not Acceptable', 406);
    }

    /**
     * Throw 'Conflict' exception (HTTP code 409).
     *
     * @return void
     */
    public function throwConflict()
    {
        throw ErrorException::create('Conflict', 409);
    }

    /**
     * Throw 'Unsupported Media Type' exception (HTTP code 415).
     *
     * @return void
     */
    public function throwUnsupportedMediaType()
    {
        throw ErrorException::create('Unsupported Media Type', 415);
    }
}
