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
use Neomerx\JsonApi\Contracts\Integration\ExceptionThrowerInterface;

/**
 * Class ExceptionThrower
 * @package CloudCreativity\JsonApi\Error
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
        throw new ErrorException([
            ErrorObjectInterface::STATUS => 400,
            ErrorObjectInterface::TITLE => 'Bad Request'
        ]);
    }

    /**
     * Throw 'Forbidden' exception (HTTP code 403).
     *
     * @return void
     */
    public function throwForbidden()
    {
        throw new ErrorException([
            ErrorObjectInterface::STATUS => 403,
            ErrorObjectInterface::TITLE => 'Forbidden',
        ]);
    }

    /**
     * Throw 'Not Acceptable' exception (HTTP code 406).
     *
     * @return void
     */
    public function throwNotAcceptable()
    {
        throw new ErrorException([
            ErrorObjectInterface::STATUS => 406,
            ErrorObjectInterface::TITLE => 'Not Acceptable',
        ]);
    }

    /**
     * Throw 'Conflict' exception (HTTP code 409).
     *
     * @return void
     */
    public function throwConflict()
    {
        throw new ErrorException([
            ErrorObjectInterface::STATUS => 409,
            ErrorObjectInterface::TITLE => 'Conflict',
        ]);
    }

    /**
     * Throw 'Unsupported Media Type' exception (HTTP code 415).
     *
     * @return void
     */
    public function throwUnsupportedMediaType()
    {
        throw new ErrorException([
            ErrorObjectInterface::STATUS => 415,
            ErrorObjectInterface::TITLE => 'Unsupported Media Type',
        ]);
    }

}
