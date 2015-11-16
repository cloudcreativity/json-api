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

class ErrorsAwareTraitTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ErrorsAwareTrait
     */
    private $trait;

    protected function setUp()
    {
        $this->trait = $this->getMockForTrait(__NAMESPACE__ . '\ErrorsAwareTrait');
    }

    public function testSet()
    {
        $errors = new ErrorCollection();
        $this->assertSame($this->trait, $this->trait->setErrors($errors));
        $this->assertSame($errors, $this->trait->getErrors());
    }

    public function testNoneSet()
    {
        $this->assertEquals(new ErrorCollection(), $this->trait->getErrors());
    }
}
