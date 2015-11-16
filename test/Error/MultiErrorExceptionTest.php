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

class MultiErrorExceptionTest extends \PHPUnit_Framework_TestCase
{

    public function testConstruct()
    {
        $errors = new ErrorCollection();
        $msg = 'My Message';
        $previous = new Exception('Previous');

        $ex = new MultiErrorException($errors, $msg, $previous);

        $this->assertSame($errors, $ex->getErrors());
        $this->assertEquals($msg, $ex->getMessage());
        $this->assertSame($previous, $ex->getPrevious());
    }

    public function testConstructOnlyErrors()
    {
        $errors = new ErrorCollection();
        $ex = new MultiErrorException($errors);

        $this->assertSame($errors, $ex->getErrors());
    }
}
