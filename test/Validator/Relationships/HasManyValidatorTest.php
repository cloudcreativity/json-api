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

use CloudCreativity\JsonApi\Object\ResourceIdentifier\ResourceIdentifier;
use CloudCreativity\JsonApi\Object\ResourceIdentifier\ResourceIdentifierCollection;
use CloudCreativity\JsonApi\Object\Relationships\Relationship;
use CloudCreativity\JsonApi\Validator\ValidatorTestCase;

class HasManyValidatorTest extends ValidatorTestCase
{

    const TYPE_A = 'foo';
    const ID_A = 123;

    const TYPE_B = 'bar';
    const ID_B = 456;

    protected $a;
    protected $b;
    protected $input;

    /**
     * @var HasManyValidator
     */
    protected $validator;

    protected function setUp()
    {
        $this->a = new \stdClass();
        $this->a->{ResourceIdentifier::TYPE} = static::TYPE_A;
        $this->a->{ResourceIdentifier::ID} = static::ID_A;

        $this->b = new \stdClass();
        $this->b->{ResourceIdentifier::TYPE} = static::TYPE_B;
        $this->b->{ResourceIdentifier::ID} = static::ID_B;

        $this->input = new \stdClass();
        $this->input->{Relationship::DATA} = [$this->a, $this->b];

        $this->validator = new HasManyValidator();
        $this->validator->setTypes([static::TYPE_A, static::TYPE_B]);
    }

    public function testValid()
    {
        $this->assertTrue($this->validator->isValid($this->input));
        $this->input->{Relationship::DATA} = [];
        $this->assertTrue($this->validator->isValid($this->input));
    }

    public function testNotValid()
    {
        $this->assertFalse($this->validator->isValid([]));

        $error = $this->getError($this->validator);
        $this->assertEquals(HasManyValidator::ERROR_INVALID_VALUE, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
    }

    public function testBelongsTo()
    {
        $this->input->{Relationship::DATA} = null;
        $this->assertFalse($this->validator->isValid($this->input));

        $error = $this->getError($this->validator);
        $this->assertEquals(HasManyValidator::ERROR_INVALID_VALUE, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
        $this->assertEquals('/data', $error->source()->getPointer());
    }

    public function testInvalidType()
    {
        $this->validator->setTypes(static::TYPE_A);
        $this->assertFalse($this->validator->isValid($this->input));

        $error = $this->getError($this->validator);
        $this->assertEquals(HasManyValidator::ERROR_INVALID_TYPE, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
        $this->assertEquals('/data/1/type', $error->source()->getPointer());
    }

    public function testMissingType()
    {
        unset($this->b->{ResourceIdentifier::TYPE});

        $this->assertFalse($this->validator->isValid($this->input));
        $error = $this->getError($this->validator);
        $this->assertEquals(HasManyValidator::ERROR_INCOMPLETE_IDENTIFIER, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
        $this->assertEquals('/data/1', $error->source()->getPointer());
    }

    public function testInvalidId()
    {
        $this->b->{ResourceIdentifier::ID} = null;
        $this->assertFalse($this->validator->isValid($this->input));

        $error = $this->getError($this->validator);
        $this->assertEquals(HasManyValidator::ERROR_INVALID_ID, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
        $this->assertEquals('/data/1/id', $error->source()->getPointer());
    }


    public function testMissingId()
    {
        unset($this->b->{ResourceIdentifier::ID});

        $this->assertFalse($this->validator->isValid($this->input));
        $error = $this->getError($this->validator);
        $this->assertEquals(HasManyValidator::ERROR_INCOMPLETE_IDENTIFIER, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
        $this->assertEquals('/data/1', $error->source()->getPointer());
    }

    public function testEmptyNotAcceptable()
    {
        $this->input->{Relationship::DATA} = [];
        $this->assertSame($this->validator, $this->validator->setAllowEmpty(false));
        $this->assertFalse($this->validator->isValid($this->input));

        $error = $this->getError($this->validator);
        $this->assertEquals(HasManyValidator::ERROR_EMPTY_DISALLOWED, $error->getCode());
        $this->assertEquals(422, $error->getStatus());
        $this->assertEquals('/data', $error->source()->getPointer());
    }

    public function testCallback()
    {
        $expected = new ResourceIdentifierCollection([
            new ResourceIdentifier($this->a),
            new ResourceIdentifier($this->b),
        ]);

        $called = false;
        $callback = function (ResourceIdentifierCollection $actual) use (&$called, $expected) {
            $this->assertEquals($expected, $actual);
            $called = true;
            return true;
        };

        $this->assertSame($this->validator, $this->validator->setCallback($callback));
        $this->assertTrue($this->validator->isValid($this->input));
        $this->assertTrue($called);
    }

    /**
     * @depends testCallback
     */
    public function testCallbackInvalid()
    {
        $this->validator->setCallback(function () {
            return false;
        });

        $this->assertFalse($this->validator->isValid($this->input));

        $error = $this->getError($this->validator);
        $this->assertEquals(HasManyValidator::ERROR_INVALID_COLLECTION, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
        $this->assertEquals('/data', $error->source()->getPointer());
    }

    /**
     * @depends testCallback
     */
    public function testCallbackInvalidIndexes()
    {
        $this->validator->setCallback(function () {
            return [1];
        });

        $this->assertFalse($this->validator->isValid($this->input));

        $error = $this->getError($this->validator);
        $this->assertEquals(HasManyValidator::ERROR_NOT_FOUND, $error->getCode());
        $this->assertEquals(404, $error->getStatus());
        $this->assertEquals('/data/1', $error->source()->getPointer());
    }

}
