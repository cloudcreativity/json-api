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

use CloudCreativity\JsonApi\TestCase;
use Neomerx\JsonApi\Document\Error;

/**
 * Class ValidationExceptionTest
 * @package CloudCreativity\JsonApi
 */
final class ValidationExceptionTest extends TestCase
{

    public function testResolveErrorStatusNoStatus()
    {
        $ex = new ValidationException(new Error());
        $this->assertEquals(ValidationException::DEFAULT_HTTP_CODE, $ex->getHttpCode());
    }

    public function testResolveErrorStatusUsesDefaultWithMultiple()
    {
        $ex = new ValidationException([new Error(), new Error()], 499);
        $this->assertEquals(499, $ex->getHttpCode());
    }

    public function testResolveErrorStatusUsesErrorStatus()
    {
        $ex = new ValidationException([new Error(), new Error(null, null, 422)]);
        $this->assertEquals(422, $ex->getHttpCode());
    }

    public function testResolveErrorStatus4xx()
    {
        $ex = new ValidationException([new Error(null, null, 422), new Error(null, null, 415)]);
        $this->assertEquals(400, $ex->getHttpCode());
    }

    public function testResolveErrorStatus5xx()
    {
        $ex = new ValidationException([new Error(null, null, 501), new Error(null, null, 503)]);
        $this->assertEquals(500, $ex->getHttpCode());
    }

    public function testResolveErrorStatusMixed()
    {
        $a = new Error(null, null, 422);
        $b = new Error(null, null, 501);
        $ex = new ValidationException([$a, $b]);

        $this->assertEquals(500, $ex->getHttpCode());
        $this->assertSame([$a, $b], $ex->getErrors()->getArrayCopy());
    }
}
