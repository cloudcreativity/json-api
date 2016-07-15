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

namespace CloudCreativity\JsonApi\Testing;

use Closure;
use CloudCreativity\JsonApi\TestCase as BaseTestCase;
use PHPUnit_Framework_AssertionFailedError;

/**
 * Class TestCase
 * @package CloudCreativity\JsonApi
 */
class TestCase extends BaseTestCase
{

    /**
     * @param Closure $closure
     * @param string $message
     */
    protected function willFail(Closure $closure, $message = '')
    {
        $didFail = false;

        try {
            $closure();
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $didFail = true;
        }

        $this->assertTrue($didFail, $message ?: 'Expecting test to fail.');
    }
}
