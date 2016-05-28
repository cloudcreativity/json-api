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

namespace CloudCreativity\JsonApi\Object;

use CloudCreativity\JsonApi\TestCase;
use stdClass;

class RelationshipTest extends TestCase
{

    protected $belongsTo;
    protected $hasMany;

    protected function setUp()
    {
        $this->belongsTo = new stdClass();
        $this->belongsTo->{ResourceIdentifier::TYPE} = 'foo';
        $this->belongsTo->{ResourceIdentifier::ID} = 123;

        $a = new stdClass();
        $a->{ResourceIdentifier::TYPE} = 'bar';
        $a->{ResourceIdentifier::ID} = 456;

        $b = new stdClass();
        $b->{ResourceIdentifier::TYPE} = 'baz';
        $b->{ResourceIdentifier::ID} = 789;

        $this->hasMany = [$a, $b];
    }

    public function testBelongsTo()
    {
        $input = new stdClass();
        $input->{Relationship::DATA} = $this->belongsTo;

        $object = new Relationship($input);
        $expected = new ResourceIdentifier($this->belongsTo);

        $this->assertEquals($expected, $object->data());
        $this->assertTrue($object->isHasOne());
        $this->assertFalse($object->isHasMany());
    }

    public function testEmptyBelongsTo()
    {
        $input = new stdClass();
        $input->{Relationship::DATA} = null;

        $object = new Relationship($input);

        $this->assertNull($object->data());
        $this->assertTrue($object->isHasOne());
        $this->assertFalse($object->isHasMany());
    }

    public function testHasMany()
    {
        $input = new stdClass();
        $input->{Relationship::DATA} = $this->hasMany;

        $object = new Relationship($input);
        $expected = ResourceIdentifierCollection::create($this->hasMany);

        $this->assertEquals($expected, $object->data());
        $this->assertTrue($object->isHasMany());
        $this->assertFalse($object->isHasOne());
    }

    public function testEmptyHasMany()
    {
        $input = new stdClass();
        $input->{Relationship::DATA} = [];

        $object = new Relationship($input);

        $this->assertEquals(new ResourceIdentifierCollection(), $object->data());
        $this->assertTrue($object->isHasMany());
        $this->assertFalse($object->isHasOne());
    }

    public function testGetMeta()
    {
        $object = new Relationship();

        $this->assertFalse($object->hasMeta());
        $this->assertEquals(new StandardObject(), $object->meta());

        $input = new stdClass();
        $input->meta = new stdClass();
        $input->meta->foo = 'bar';

        $object->setProxy($input);

        $this->assertTrue($object->hasMeta());
        $this->assertEquals(new StandardObject($input->meta), $object->meta());
    }
}
