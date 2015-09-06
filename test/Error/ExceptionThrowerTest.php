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

    protected function checkException($method, ErrorObject $expected)
    {
        try {
            call_user_func([$this->thrower, $method]);
            $this->fail('No exception thrown.');
        } catch (ErrorException $actual) {
            $this->assertEquals($expected, $actual->getError());
        } catch (\Exception $e) {
            $this->fail('Invalid exception thrown.');
        }
    }

    public function testBadRequest()
    {
        $expected = new ErrorObject([
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Bad Request',
        ]);

        $this->checkException('throwBadRequest', $expected);
    }

    public function testForbidden()
    {
        $expected = new ErrorObject([
            ErrorObject::STATUS => 403,
            ErrorObject::TITLE => 'Forbidden',
        ]);

        $this->checkException('throwForbidden', $expected);
    }

    public function testNotAcceptable()
    {
        $expected = new ErrorObject([
            ErrorObject::STATUS => 406,
            ErrorObject::TITLE => 'Not Acceptable',
        ]);

        $this->checkException('throwNotAcceptable', $expected);
    }

    public function testConfict()
    {
        $expected = new ErrorObject([
            ErrorObject::STATUS => 409,
            ErrorObject::TITLE => 'Conflict',
        ]);

        $this->checkException('throwConflict', $expected);
    }

    public function testUnsupportedMediaType()
    {
        $expected = new ErrorObject([
            ErrorObject::STATUS => 415,
            ErrorObject::TITLE => 'Unsupported Media Type',
        ]);

        $this->checkException('throwUnsupportedMediaType', $expected);
    }
}
