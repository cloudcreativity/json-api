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
use CloudCreativity\JsonApi\Validator\Relationships\HasOneValidator;
use CloudCreativity\JsonApi\Validator\Relationships\HasManyValidator;
use CloudCreativity\JsonApi\Validator\ResourceIdentifier\ExpectedIdValidator;
use CloudCreativity\JsonApi\Validator\ResourceIdentifier\ExpectedTypeValidator;
use CloudCreativity\JsonApi\Validator\Type\StringValidator;
use CloudCreativity\JsonApi\Validator\ValidatorTestCase;
use OutOfBoundsException;

class ResourceObjectValidatorTest extends ValidatorTestCase
{

    public function testType()
    {
        $type = 'foo';
        $expected = new ExpectedTypeValidator($type);
        $validator = new ResourceObjectValidator();

        $this->assertSame($validator, $validator->type($type));
        $this->assertEquals($expected, $validator->getTypeValidator());
    }

    public function testId()
    {
        $id = 123;
        $expected = new ExpectedIdValidator($id);
        $validator = new ResourceObjectValidator();

        $this->assertSame($validator, $validator->id($id));
        $this->assertEquals($expected, $validator->getIdValidator());
    }

    public function testAttr()
    {
        $key = 'foo';

        $expected = new StringValidator();
        $expected->setAcceptNull(true);

        $validator = new ResourceObjectValidator();

        $this->assertSame($validator, $validator->attr($key, 'string', [
            StringValidator::ACCEPT_NULL => true,
        ]));

        $this->assertEquals($expected, $validator->getKeyValidator($key));
    }

    public function testAttrWithClass()
    {
        $key = 'foo';
        $expected = new StringValidator();
        $validator = new ResourceObjectValidator();

        $validator->attr($key, get_class($expected));
        $this->assertEquals($expected, $validator->getKeyValidator($key));
    }

    public function testAttrWithValidator()
    {
        $key = 'foo';
        $mock = $this->getMock(ValidatorInterface::class);
        $validator = new ResourceObjectValidator();

        $validator->attr($key, $mock);
        $this->assertSame($mock, $validator->getKeyValidator($key));
    }

    public function testHasOne()
    {
        $key = 'foo';
        $type = 'bar';

        $expected = new HasOneValidator();
        $expected->setTypes($type);
        $expected->setAllowEmpty(false);

        $validator = new ResourceObjectValidator();

        $this->assertSame($validator, $validator->hasOne($key, $type, [
            HasOneValidator::ALLOW_EMPTY => false,
        ]));

        $this->assertEquals($expected, $validator->getKeyValidator($key));
    }

    public function testHasMany()
    {
        $key = 'foo';
        $type = 'bar';

        $expected = new HasManyValidator();
        $expected->setTypes($type);
        $expected->setAllowEmpty(false);

        $validator = new ResourceObjectValidator();

        $this->assertSame($validator, $validator->hasMany($key, $type, [
            HasManyValidator::ALLOW_EMPTY => false,
        ]));

        $this->assertEquals($expected, $validator->getKeyValidator($key));
    }

    /**
     * Test for issue #19
     */
    public function testKeyValidatorAttributesNotKeyed()
    {
        /** @var ValidatorInterface $attributes */
        $attributes = $this->getMock(ValidatorInterface::class);

        $validator = new ResourceObjectValidator();
        $validator->setAttributesValidator($attributes)
            ->hasOne('foo', 'bar');

        $this->assertInstanceOf(ValidatorInterface::class, $validator->getKeyValidator('foo'));
    }

    /**
     * Test for issue #19
     */
    public function testKeyValidatorRelationshipsNotKeyed()
    {
        /** @var ValidatorInterface $relationships */
        $relationships = $this->getMock(ValidatorInterface::class);

        $validator = new ResourceObjectValidator();
        $validator->setRelationshipsValidator($relationships);

        $this->setExpectedException(OutOfBoundsException::class);
        $validator->getKeyValidator('foo');
    }

    public function testGetRelated()
    {
        $key = 'foo';
        $expected = new HasManyValidator();

        $validator = new ResourceObjectValidator();

        $validator
            ->getKeyedRelationships()
            ->setValidator($key, $expected);

        $this->assertSame($expected, $validator->getRelated($key));
    }

    public function testAllowed()
    {
        $a = 'foo';
        $b = 'bar';

        $validator = new ResourceObjectValidator();
        $validator->attr($a);

        $this->assertSame($validator, $validator->allowed([$a]));
        $this->assertTrue($validator->getKeyedAttributes()->isAllowedKey($a));
        $this->assertFalse($validator->getKeyedAttributes()->isAllowedKey($b));

        $this->markTestIncomplete('@todo test that relationships are also set as allowed via this method.');
    }

    public function testRestrict()
    {
        $a = 'foo';
        $b = 'bar';

        $validator = new ResourceObjectValidator();
        $validator->attr($a);

        $this->assertSame($validator, $validator->restrict());
        $this->assertTrue($validator->getKeyedAttributes()->isAllowedKey($a));
        $this->assertFalse($validator->getKeyedAttributes()->isAllowedKey($b));
    }

    public function testAcceptsEmptyAttributesAndRelationships()
    {
        $input = new \stdClass();
        $input->type = 'foo';
        $input->attributes = new \stdClass();
        $input->relationships = new \stdClass();

        $validator = new ResourceObjectValidator($input->type);

        $this->assertTrue($validator->isValid($input));
    }
}
