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

namespace CloudCreativity\JsonApi\Validator\Relationships;

use CloudCreativity\JsonApi\Error\ErrorObject;
use CloudCreativity\JsonApi\Error\ErrorCollection;
use CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface;

class RelationshipsValidatorTest extends \PHPUnit_Framework_TestCase
{

    const KEY_A = 'foo';
    const KEY_B = 'bars';

    /**
     * @var RelationshipsValidator
     */
    protected $validator;
    protected $input;
    protected $a;
    protected $b;

    protected function setUp()
    {
        $this->input = new \stdClass();
        $this->input->{static::KEY_A} = new \stdClass();
        $this->input->{static::KEY_A}->foo = 'bar';
        $this->input->{static::KEY_B} = new \stdClass();
        $this->input->{static::KEY_B}->baz = 'bat';

        $this->a = $this->getMock(ValidatorInterface::class);
        $this->b = $this->getMock(ValidatorInterface::class);

        $this->validator = new RelationshipsValidator();
    }

    public function testValid()
    {
        $this->assertSame($this->validator, $this->validator->setValidators([
            static::KEY_A => $this->a,
            static::KEY_B => $this->b,
        ]));

        $this->a->expects($this->once())
            ->method('isValid')
            ->with($this->input->{static::KEY_A})
            ->willReturn(true);

        $this->b->expects($this->once())
            ->method('isValid')
            ->with($this->input->{static::KEY_B})
            ->willReturn(true);

        $this->assertTrue($this->validator->isValid($this->input));
    }

    public function testInvalidValue()
    {
        $this->assertFalse($this->validator->isValid(null));

        /** @var ErrorObject $error */
        $error = current($this->validator->getErrors()->getAll());

        $this->assertInstanceOf(ErrorObject::class, $error);
        $this->assertEquals(RelationshipsValidator::ERROR_INVALID_VALUE, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
    }

    public function testInvalidKey()
    {
        $pointer = '/error/pointer';

        $err = new ErrorObject();
        $err->setCode('foo')
            ->source()
            ->setPointer($pointer);

        $expected = clone $err;
        $expected->source()->setPointer(sprintf('/%s%s', static::KEY_B, $pointer));

        $this->a->method('isValid')->willReturn(true);
        $this->b->method('isValid')->willReturn(false);
        $this->b->method('getErrors')->willReturn(new ErrorCollection([$err]));

        $this->validator->setValidators([
            static::KEY_A => $this->a,
            static::KEY_B => $this->b,
        ]);

        $this->assertFalse($this->validator->isValid($this->input));

        /** @var ErrorObject $error */
        $error = current($this->validator->getErrors()->getAll());

        $this->assertInstanceOf(ErrorObject::class, $error);
        $this->assertEquals($expected, $error);
    }

    public function testUnrecognisedKey()
    {
        $this->validator->setValidator(static::KEY_A, $this->a);

        $this->a->method('isValid')->willReturn(true);

        $this->assertFalse($this->validator->isValid($this->input));

        /** @var ErrorObject $error */
        $error = current($this->validator->getErrors()->getAll());

        $this->assertInstanceOf(ErrorObject::class, $error);
        $this->assertEquals(RelationshipsValidator::ERROR_UNRECOGNISED_RELATIONSHIP, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
    }

    public function testRequired()
    {
        $this->assertFalse($this->validator->hasRequiredKeys());

        $this->assertSame($this->validator, $this->validator->setRequiredKeys([
            static::KEY_A,
            static::KEY_B,
        ]));

        $this->assertTrue($this->validator->hasRequiredKeys());

        $this->validator->setValidators([
            static::KEY_A => $this->a,
            static::KEY_B => $this->b,
        ]);

        $this->a->method('isValid')->willReturn(true);
        $this->b->method('isValid')->willReturn(true);

        $this->assertTrue($this->validator->isValid($this->input));

        unset($this->input->{static::KEY_B});

        $this->assertFalse($this->validator->isValid($this->input));

        /** @var ErrorObject $error */
        $error = current($this->validator->getErrors()->getAll());

        $this->assertInstanceOf(ErrorObject::class, $error);
        $this->assertEquals(RelationshipsValidator::ERROR_REQUIRED_RELATIONSHIP, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
    }
}
