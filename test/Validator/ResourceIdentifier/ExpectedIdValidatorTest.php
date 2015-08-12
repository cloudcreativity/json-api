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

namespace CloudCreativity\JsonApi\Validator\ResourceIdentifier;

use Neomerx\JsonApi\Contracts\Document\ErrorInterface;

class ExpectedIdValidatorTest extends \PHPUnit_Framework_TestCase
{

    const EXPECTED = 123;
    const NOT_EXPECTED = 999;

    public function testSet()
    {
        $validator = new ExpectedIdValidator();

        $this->assertSame($validator, $validator->setExpected(static::EXPECTED));
        $this->assertSame(static::EXPECTED, $validator->getExpected());

        return $validator;
    }

    /**
     * @depends testSet
     */
    public function testValid(ExpectedIdValidator $validator)
    {
        $this->assertTrue($validator->isValid(static::EXPECTED));
        $this->assertTrue($validator->isValid((string) static::EXPECTED));
        $this->assertTrue($validator->getErrors()->isEmpty());
    }

    /**
     * @depends testSet
     */
    public function testInvalidValue(ExpectedIdValidator $validator)
    {
        $this->assertFalse($validator->isValid(true));

        /** @var ErrorInterface $error */
        $error = current($validator->getErrors()->getAll());

        $this->assertInstanceOf(ErrorInterface::class, $error);
        $this->assertEquals(ExpectedIdValidator::INVALID_VALUE, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
    }

    /**
     * @depends testSet
     */
    public function testUnexpectedId(ExpectedIdValidator $validator)
    {
        $this->assertFalse($validator->isValid(static::NOT_EXPECTED));

        /** @var ErrorInterface $error */
        $error = current($validator->getErrors()->getAll());

        $this->assertInstanceOf(ErrorInterface::class, $error);
        $this->assertEquals(ExpectedIdValidator::UNEXPECTED_ID, $error->getCode());
        $this->assertEquals(409, $error->getStatus());
    }
}
