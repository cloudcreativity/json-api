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

use CloudCreativity\JsonApi\Error\ThrowableError;
use Exception;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;

class ExceptionThrowerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ExceptionThrower
     */
    protected $thrower;

    protected function setUp()
    {
        $this->thrower = new ExceptionThrower();
    }

    protected function checkException($method, $status)
    {
        try {
            call_user_func([$this->thrower, $method]);
            $this->fail('No exception thrown.');
        } catch (ThrowableError $actual) {
            $this->assertEquals($status, $actual->getStatus());
        } catch (Exception $e) {
            $this->fail('Invalid exception thrown.');
        }
    }

    public function testBadRequest()
    {
        $this->checkException('throwBadRequest', 400);
    }

    public function testForbidden()
    {
        $this->checkException('throwForbidden', 403);
    }

    public function testNotAcceptable()
    {
        $this->checkException('throwNotAcceptable', 406);
    }

    public function testConfict()
    {
        $this->checkException('throwConflict', 409);
    }

    public function testUnsupportedMediaType()
    {
        $this->checkException('throwUnsupportedMediaType', 415);
    }
}
