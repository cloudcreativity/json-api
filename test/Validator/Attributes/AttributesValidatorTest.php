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

namespace CloudCreativity\JsonApi\Validator\Attributes;

use CloudCreativity\JsonApi\Error\ErrorCollection;
use CloudCreativity\JsonApi\Error\ErrorObject;
use CloudCreativity\JsonApi\Error\SourceObject;
use CloudCreativity\JsonApi\Validator\ValidatorTestCase;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface;

class AttributesValidatorTest extends ValidatorTestCase
{

    const KEY_A = 'foo';
    const KEY_B = 'bar';
    const KEY_C = 'baz';

    protected $data;

    protected function setUp()
    {
        $data = new \stdClass();
        $data->{static::KEY_A} = 'foobar';
        $data->{static::KEY_B} = 'bazbat';

        $this->data = $data;
    }

    public function testIsValid()
    {
        $validator = new RulesValidator();

        $this->assertTrue($validator->isValid($this->data));
        $this->assertTrue($validator->getErrors()->isEmpty());
    }

    public function testNotAllowed()
    {
        $validator = new RulesValidator();

        $this->assertSame($validator, $validator->setAllowedKeys([static::KEY_A]));
        $this->assertFalse($validator->isValid($this->data));

        /** @var ErrorInterface $error */
        $error = current($validator->getErrors()->getAll());

        $this->assertInstanceOf(ErrorInterface::class, $error);
        $this->assertEquals(RulesValidator::ERROR_UNRECOGNISED_ATTRIBUTE, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
        $this->assertEquals('/' . static::KEY_B, $error->getSource()->getPointer());
    }

    public function testMissingRequired()
    {
        $required = $this->getMock(ValidatorInterface::class);

        $required->method('isValid')
            ->willReturn(true);

        $required->method('isRequired')
            ->willReturn(true);

        $validator = new RulesValidator([
            static::KEY_A => $required,
            static::KEY_C => $required,
        ]);

        $this->assertFalse($validator->isValid($this->data));

        $error = $this->getError($validator);
        $this->assertEquals(RulesValidator::ERROR_REQUIRED, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
        $this->assertEquals('/' . static::KEY_C, $error->source()->getPointer());
    }

    public function testKeyValidator()
    {
        $mock = $this->getMock(ValidatorInterface::class);

        $mock->expects($this->once())
            ->method('isValid')
            ->with($this->data->{static::KEY_A})
            ->willReturn(true);

        $validator = new RulesValidator();

        $this->assertSame($validator, $validator->setValidator(static::KEY_A, $mock));

        $validator->isValid($this->data);
    }

    public function testKeyValidatorInvalid()
    {
        $error = new ErrorObject([
            ErrorObject::CODE => 'test-invalid',
            ErrorObject::STATUS => 400,
            ErrorObject::SOURCE => [
                SourceObject::POINTER => '/extra',
            ]
        ]);

        $expected = clone $error;
        $expected->setSource([
            SourceObject::POINTER => '/' . static::KEY_A . '/extra',
        ]);

        $mock = $this->getMock(ValidatorInterface::class);

        $mock->method('getErrors')
            ->willReturn(new ErrorCollection([$error]));

        $validator = new RulesValidator();
        $validator->setValidator(static::KEY_A, $mock);

        $this->assertFalse($validator->isValid($this->data));

        $err = $this->getError($validator);
        $this->assertEquals($expected, $err);
    }
}
