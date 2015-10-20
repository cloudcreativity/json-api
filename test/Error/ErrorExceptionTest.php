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

use Exception;

class ErrorExceptionTest extends \PHPUnit_Framework_TestCase
{

    public function testWithErrorObject()
    {
        $error = new ErrorObject([
            ErrorObject::TITLE => 'Foo',
            ErrorObject::CODE => 'bar',
        ]);

        $exception = new ErrorException($error);

        $this->assertSame($error, $exception->getError());

        $expected = new ErrorCollection([$error]);

        $this->assertEquals($expected, $exception->getErrors());
    }

    public function testWithArray()
    {
        $arr = [
            ErrorException::TITLE => 'Foo',
            ErrorException::DETAIL => 'Bar',
            ErrorException::CODE => 'baz',
            ErrorException::STATUS => 501,
        ];

        $exception = new ErrorException($arr);
        $expected = new ErrorObject($arr);

        $this->assertEquals($expected, $exception->getError());
    }

    public function testPreviousException()
    {
        $previous = new Exception('My previous exception');
        $exception = new ErrorException(new ErrorObject(), $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }
}
