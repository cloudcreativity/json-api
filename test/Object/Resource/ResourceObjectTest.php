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

namespace CloudCreativity\JsonApi\Object\Resource;

use CloudCreativity\JsonApi\Object\StandardObject;
use CloudCreativity\JsonApi\Object\Relationships\Relationships;
use CloudCreativity\JsonApi\Object\ResourceIdentifier\ResourceIdentifier;

class ResourceObjectTest extends \PHPUnit_Framework_TestCase
{

    const TYPE = 'foo';
    const ID = 123;

    protected $data;

    protected function setUp()
    {
        $data = new \stdClass();
        $data->{ResourceObject::TYPE} = static::TYPE;
        $data->{ResourceObject::ID} = static::ID;
        $data->{ResourceObject::ATTRIBUTES} = new \stdClass();
        $data->{ResourceObject::ATTRIBUTES}->foo = 'bar';
        $data->{ResourceObject::RELATIONSHIPS} = new \stdClass();
        $data->{ResourceObject::RELATIONSHIPS}->baz = null;
        $data->{ResourceObject::META} = new \stdClass();
        $data->{ResourceObject::META}->bat = 'foobar';

        $this->data = $data;
    }

    public function testGetType()
    {
        $object = new ResourceObject($this->data);
        $this->assertSame(static::TYPE, $object->getType());
    }

    public function testGetId()
    {
        $object = new ResourceObject($this->data);
        $this->assertSame(static::ID, $object->getId());
    }

    public function testHasId()
    {
        $object = new ResourceObject($this->data);
        $this->assertTrue($object->hasId());
        unset($this->data->{ResourceObject::ID});
        $this->assertFalse($object->hasId());
    }

    public function testGetIdentifier()
    {
        $expected = new ResourceIdentifier();
        $expected->setType(static::TYPE)->setId(static::ID);

        $object = new ResourceObject($this->data);
        $this->assertEquals($expected, $object->getIdentifier());
    }

    public function testGetAttributes()
    {
        $object = new ResourceObject($this->data);
        $expected = new StandardObject($this->data->{ResourceObject::ATTRIBUTES});

        $this->assertEquals($expected, $object->getAttributes());
    }

    public function testGetEmptyAttributes()
    {
        unset($this->data->{ResourceObject::ATTRIBUTES});
        $object = new ResourceObject($this->data);
        $this->assertEquals(new StandardObject(), $object->getAttributes());
    }

    public function testHasAttributes()
    {
        $object = new ResourceObject($this->data);
        $this->assertTrue($object->hasAttributes());
        unset($this->data->{ResourceObject::ATTRIBUTES});
        $this->assertFalse($object->hasAttributes());
    }

    public function testGetRelationships()
    {
        $expected = new Relationships($this->data->{ResourceObject::RELATIONSHIPS});
        $object = new ResourceObject($this->data);

        $this->assertEquals($expected, $object->getRelationships());
    }

    public function testHasRelationships()
    {
        $object = new ResourceObject($this->data);
        $this->assertTrue($object->hasRelationships());
        unset($this->data->{ResourceObject::RELATIONSHIPS});
        $this->assertFalse($object->hasRelationships());
    }

    public function getMeta()
    {
        $expected = new StandardObject($this->data->{ResourceObject::META});
        $object = new ResourceObject($this->data);
        $this->assertEquals($expected, $object->getMeta());
    }

    public function testHasMeta()
    {
        $object = new ResourceObject($this->data);
        $this->assertTrue($object->hasMeta());
        unset($this->data->{ResourceObject::META});
        $this->assertFalse($object->hasMeta());
    }
}
