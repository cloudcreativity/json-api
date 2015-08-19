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
use CloudCreativity\JsonApi\Validator\Relationships\BelongsToValidator;
use CloudCreativity\JsonApi\Validator\Relationships\HasManyValidator;
use CloudCreativity\JsonApi\Validator\ResourceIdentifier\ExpectedIdValidator;
use CloudCreativity\JsonApi\Validator\ResourceIdentifier\ExpectedTypeValidator;
use CloudCreativity\JsonApi\Validator\Type\StringValidator;
use CloudCreativity\JsonApi\Validator\ValidatorTestCase;

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

        $this->assertEquals($expected, $validator->getKeyedAttributes()->getValidator($key));
    }

    public function testAttrWithClass()
    {
        $key = 'foo';
        $expected = new StringValidator();
        $validator = new ResourceObjectValidator();

        $validator->attr($key, get_class($expected));
        $this->assertEquals($expected, $validator->getKeyedAttributes()->getValidator($key));
    }

    public function testAttrWithValidator()
    {
        $key = 'foo';
        $mock = $this->getMock(ValidatorInterface::class);
        $validator = new ResourceObjectValidator();

        $validator->attr($key, $mock);
        $this->assertSame($mock, $validator->getKeyedAttributes()->getValidator($key));
    }

    public function testBelongsTo()
    {
        $key = 'foo';
        $type = 'bar';

        $expected = new BelongsToValidator();
        $expected->setTypes($type);
        $expected->setAllowEmpty(false);

        $validator = new ResourceObjectValidator();

        $this->assertSame($validator, $validator->belongsTo($key, $type, [
            BelongsToValidator::ALLOW_EMPTY => false,
        ]));

        $this->assertEquals($expected, $validator->getKeyedRelationships()->getValidator($key));
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

        $this->assertEquals($expected, $validator->getKeyedRelationships()->getValidator($key));
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

    public function testRequired()
    {
        $a = 'foo';
        $b = 'bar';

        $validator = new ResourceObjectValidator();
        $validator->attr($a);
        $validator->belongsTo($b, 'type');

        $this->assertSame($validator, $validator->required([$a, $b]));
        $this->assertEquals([$a], $validator->getKeyedAttributes()->getRequiredKeys());
        $this->assertEquals([$b], $validator->getKeyedRelationships()->getRequiredKeys());
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
