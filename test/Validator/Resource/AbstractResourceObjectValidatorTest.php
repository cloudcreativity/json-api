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

namespace CloudCreativity\JsonApi\Validator\Resource;

use CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface;
use CloudCreativity\JsonApi\Error\ErrorCollection;
use CloudCreativity\JsonApi\Error\ErrorObject;
use CloudCreativity\JsonApi\Object\Resource\ResourceObject;
use Neomerx\JsonApi\Document\Error;

class AbstractResourceObjectValidatorTest extends \PHPUnit_Framework_TestCase
{

    const EXPECTED_TYPE = 'foo';
    const UNEXPECTED_TYPE = 'bar';

    protected $type;

    /**
     * @var AbstractResourceObjectValidator
     */
    protected $validator;

    /**
     * @var \stdClass
     */
    protected $input;

    /**
     * @var ErrorObject
     */
    protected $error;

    protected function setUp()
    {
        $this->validator = $this->getMockForAbstractClass(AbstractResourceObjectValidator::class);
        $this->type = $this->getMock(ValidatorInterface::class);

        $this->validator
            ->method('getTypeValidator')
            ->willReturn($this->type);

        $this->type
            ->method('isValid')
            ->will($this->returnValueMap([
                [static::EXPECTED_TYPE, true],
            ]));

        $this->input = new \stdClass();
        $this->input->{ResourceObject::TYPE} = static::EXPECTED_TYPE;

        $this->error = new ErrorObject();
        $this->error->setTitle('Foo');
        $this->error->setCode('Bar');
    }

    public function testIsValid()
    {
        $this->assertTrue($this->validator->isValid($this->input));
    }

    public function testInvalid()
    {
        $this->assertFalse($this->validator->isValid([]));

        /** @var ErrorObject $error */
        $error = current($this->validator->getErrors()->getAll());

        $this->assertInstanceOf(ErrorObject::class, $error);
        $this->assertEquals(AbstractResourceObjectValidator::ERROR_INVALID_VALUE, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
    }

    public function testMissingType()
    {
        $this->assertFalse($this->validator->isValid(new \stdClass()));

        /** @var ErrorObject $error */
        $error = current($this->validator->getErrors()->getAll());

        $this->assertInstanceOf(ErrorObject::class, $error);
        $this->assertEquals(AbstractResourceObjectValidator::ERROR_MISSING_TYPE, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
    }

    public function testTypeValidator()
    {
        $expected = clone $this->error;
        $expected->source()->setPointer('/' . ResourceObject::TYPE);

        $this->type->method('getErrors')->willReturn(new ErrorCollection([$this->error]));

        $this->input->{ResourceObject::TYPE} = static::UNEXPECTED_TYPE;

        $this->assertFalse($this->validator->isValid($this->input));

        $error = current($this->validator->getErrors()->getAll());
        $this->assertEquals($expected, $error);
    }

    public function testMissingId()
    {
        $this->validator
            ->method('getIdValidator')
            ->willReturn($this->getMock(ValidatorInterface::class));

        $this->assertFalse($this->validator->isValid($this->input));

        /** @var ErrorObject $error */
        $error = current($this->validator->getErrors()->getAll());
        $this->assertInstanceOf(ErrorObject::class, $error);
        $this->assertEquals(AbstractResourceObjectValidator::ERROR_MISSING_ID, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
    }

    public function testUnexpectedId()
    {
        $this->input->{ResourceObject::ID} = 123;

        $this->assertFalse($this->validator->isValid($this->input));

        /** @var ErrorObject $error */
        $error = current($this->validator->getErrors()->getAll());
        $this->assertInstanceOf(ErrorObject::class, $error);
        $this->assertEquals(AbstractResourceObjectValidator::ERROR_UNEXPECTED_ID, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
        $this->assertEquals('/' . ResourceObject::ID, $error->source()->getPointer());
    }

    public function testValidId()
    {
        $id = 123;
        $this->input->{ResourceObject::ID} = $id;

        $idValidator = $this->getMock(ValidatorInterface::class);
        $idValidator->expects($this->once())
            ->method('isValid')
            ->with($id)
            ->willReturn(true);

        $this->validator
            ->method('getIdValidator')
            ->willReturn($idValidator);

        $this->assertTrue($this->validator->isValid($this->input));
    }

    public function testInvalidId()
    {
        $this->input->{ResourceObject::ID} = 123;

        $idValidator = $this->getMock(ValidatorInterface::class);
        $idValidator->method('getErrors')
            ->willReturn(new ErrorCollection([$this->error]));

        $this->validator
            ->method('getIdValidator')
            ->willReturn($idValidator);

        $expected = clone $this->error;
        $expected->source()->setPointer('/' . ResourceObject::ID);

        $this->assertFalse($this->validator->isValid($this->input));

        $error = current($this->validator->getErrors()->getAll());
        $this->assertEquals($expected, $error);
    }

    public function testMissingAttributes()
    {
        $this->validator
            ->method('isExpectingAttributes')
            ->willReturn(true);

        $this->assertFalse($this->validator->isValid($this->input));

        /** @var ErrorObject $error */
        $error = current($this->validator->getErrors()->getAll());
        $this->assertInstanceOf(ErrorObject::class, $error);
        $this->assertEquals(AbstractResourceObjectValidator::ERROR_MISSING_ATTRIBUTES, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
    }

    public function testValidAttributes()
    {
        $attributes = new \stdClass;
        $attributes->foo = 'bar';

        $this->input->{ResourceObject::ATTRIBUTES} = $attributes;

        $attrValidator = $this->getMock(ValidatorInterface::class);
        $attrValidator->method('isValid')
            ->with($attributes)
            ->willReturn(true);

        $this->validator
            ->method('getAttributesValidator')
            ->willReturn($attrValidator);

        $this->assertTrue($this->validator->isValid($this->input));
    }

    public function testInvalidAttributes()
    {
        $this->input->{ResourceObject::ATTRIBUTES} = new \stdClass();

        $attrValidator = $this->getMock(ValidatorInterface::class);

        $attrValidator->method('isValid')
            ->willReturn(false);

        $attrValidator->method('getErrors')
            ->willReturn(new ErrorCollection([$this->error]));

        $this->validator
            ->method('getAttributesValidator')
            ->willReturn($attrValidator);

        $current = '/foo';
        $this->error->source()->setPointer($current);

        $expected = clone $this->error;
        $expected->source()->setPointer(sprintf('/%s%s', ResourceObject::ATTRIBUTES, $current));

        $this->assertFalse($this->validator->isValid($this->input));

        $error = current($this->validator->getErrors()->getAll());
        $this->assertEquals($expected, $error);
    }

    public function testMissingRelationships()
    {
        $this->validator
            ->method('isExpectingRelationships')
            ->willReturn(true);

        $this->assertFalse($this->validator->isValid($this->input));

        /** @var ErrorObject $error */
        $error = current($this->validator->getErrors()->getAll());
        $this->assertInstanceOf(ErrorObject::class, $error);
        $this->assertEquals(AbstractResourceObjectValidator::ERROR_MISSING_RELATIONSHIPS, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
    }

    public function testValidRelationships()
    {
        $rel = new \stdClass();
        $rel->foo = 'bar';

        $this->input->{ResourceObject::RELATIONSHIPS} = $rel;

        $relValidator = $this->getMock(ValidatorInterface::class);
        $relValidator->method('isValid')
            ->with($rel)
            ->willReturn(true);

        $this->validator
            ->method('getRelationshipsValidator')
            ->willReturn($relValidator);

        $this->assertTrue($this->validator->isValid($this->input));
    }

    public function testInvalidRelationships()
    {
        $this->input->{ResourceObject::RELATIONSHIPS} = new \stdClass();
        $current = '/foo';
        $this->error->source()->setPointer($current);

        $relValidator = $this->getMock(ValidatorInterface::class);
        $relValidator->method('getErrors')
            ->willReturn(new ErrorCollection([$this->error]));

        $this->validator
            ->method('getRelationshipsValidator')
            ->willReturn($relValidator);

        $expected = clone $this->error;
        $expected->source()->setPointer(sprintf('/%s%s', ResourceObject::RELATIONSHIPS, $current));

        $this->assertFalse($this->validator->isValid($this->input));

        $error = current($this->validator->getErrors()->getAll());
        $this->assertEquals($expected, $error);
    }
}