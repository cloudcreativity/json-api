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

namespace CloudCreativity\JsonApi\Validator\Type;

use CloudCreativity\JsonApi\Validator\ValidatorTestCase;

/**
 * Class DateValidatorTest
 * @package CloudCreativity\JsonApi
 */
class DateValidatorTest extends ValidatorTestCase
{

    public function testValid()
    {
        $validator = new DateValidator();
        $date = new \DateTime();

        $this->assertTrue($validator->isValid($date->format(DateValidator::FORMAT_ISO_8601)));
        $this->assertTrue($validator->isValid($date->format(DateValidator::FORMAT_ISO_8601_MILLISECONDS)));

        return $validator;
    }

    /**
     * @depends testValid
     */
    public function testInvalid(DateValidator $validator)
    {
        $date = new \DateTime();

        $this->assertFalse($validator->isValid($date->format('Y-m-d')));
        $error = $this->getError($validator);
        $this->assertEquals(DateValidator::ERROR_INVALID_VALUE, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
    }

    public function testCustomFormat()
    {
        $format = 'H:i:s';
        $validator = new DateValidator([$format]);
        $date = new \DateTime();

        $this->assertTrue($validator->isValid($date->format($format)));
        $this->assertFalse($validator->isValid($date->format(DateValidator::FORMAT_ISO_8601)));
    }
}
