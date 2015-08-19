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

use CloudCreativity\JsonApi\Validator\ValidatorTestCase;

class ExpectedTypeValidatorTest extends ValidatorTestCase
{

    const EXPECTED = 'Foo';
    const NOT_EXPECTED = 'Bar';

    public function testSet()
    {
        $validator = new ExpectedTypeValidator();

        $this->assertSame($validator, $validator->setExpected(static::EXPECTED));
        $this->assertSame(static::EXPECTED, $validator->getExpected());

        return $validator;
    }

    /**
     * @depends testSet
     */
    public function testValid(ExpectedTypeValidator $validator)
    {
        $this->assertTrue($validator->isValid(static::EXPECTED));
        $this->assertTrue($validator->getErrors()->isEmpty());
    }

    /**
     * @depends testSet
     */
    public function testInvalidFormat(ExpectedTypeValidator $validator)
    {
        $this->assertFalse($validator->isValid(123));

        $error = $this->getError($validator);
        $this->assertEquals(ExpectedTypeValidator::INVALID_VALUE, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
    }

    /**
     * @depends testSet
     */
    public function testUnexpectedType(ExpectedTypeValidator $validator)
    {
        $this->assertFalse($validator->isValid(static::NOT_EXPECTED));

        $error = $this->getError($validator);
        $this->assertEquals(ExpectedTypeValidator::UNSUPPORTED_TYPE, $error->getCode());
        $this->assertEquals(409, $error->getStatus());
    }
}
