<?php

namespace CloudCreativity\JsonApi\Object\Resource;

use CloudCreativity\JsonApi\Object\StandardObject;
use CloudCreativity\JsonApi\Object\Relationships\Relationships;
use CloudCreativity\JsonApi\Object\ResourceIdentifier\ResourceIdentifier;

class ResourceTest extends \PHPUnit_Framework_TestCase
{

  const TYPE = 'foo';
  const ID = 123;

  protected $data;

  protected function setUp()
  {
    $data = new \stdClass();
    $data->{Resource::TYPE} = static::TYPE;
    $data->{Resource::ID} = static::ID;
    $data->{Resource::ATTRIBUTES} = new \stdClass();
    $data->{Resource::ATTRIBUTES}->foo = 'bar';
    $data->{Resource::RELATIONSHIPS} = new \stdClass();
    $data->{Resource::RELATIONSHIPS}->baz = null;
    $data->{Resource::META} = new \stdClass();
    $data->{Resource::META}->bat = 'foobar';

    $this->data = $data;
  }

  public function testGetType()
  {
    $object = new Resource($this->data);
    $this->assertSame(static::TYPE, $object->getType());
  }

  public function testGetId()
  {
    $object = new Resource($this->data);
    $this->assertSame(static::ID, $object->getId());
  }

  public function testHasId()
  {
    $object = new Resource($this->data);
    $this->assertTrue($object->hasId());
    unset($this->data->{Resource::ID});
    $this->assertFalse($object->hasId());
  }

  public function testGetIdentifier()
  {
    $expected = new ResourceIdentifier();
    $expected->setType(static::TYPE)->setId(static::ID);

    $object = new Resource($this->data);
    $this->assertEquals($expected, $object->getIdentifier());
  }

  public function testGetAttributes()
  {
    $object = new Resource($this->data);
    $expected = new StandardObject($this->data->{Resource::ATTRIBUTES});

    $this->assertEquals($expected, $object->getAttributes());
  }

  public function testGetEmptyAttributes()
  {
    unset($this->data->{Resource::ATTRIBUTES});
    $object = new Resource($this->data);
    $this->assertEquals(new StandardObject(), $object->getAttributes());
  }

  public function testHasAttributes()
  {
    $object = new Resource($this->data);
    $this->assertTrue($object->hasAttributes());
    unset($this->data->{Resource::ATTRIBUTES});
    $this->assertFalse($object->hasAttributes());
  }

  public function testGetRelationships()
  {
    $expected = new Relationships($this->data->{Resource::RELATIONSHIPS});
    $object = new Resource($this->data);

    $this->assertEquals($expected, $object->getRelationships());
  }

  public function testHasRelationships()
  {
    $object = new Resource($this->data);
    $this->assertTrue($object->hasRelationships());
    unset($this->data->{Resource::RELATIONSHIPS});
    $this->assertFalse($object->hasRelationships());
  }

  public function getMeta()
  {
    $expected = new StandardObject($this->data->{Resource::META});
    $object = new Resource($this->data);
    $this->assertEquals($expected, $object->getMeta());
  }

  public function testHasMeta()
  {
    $object = new Resource($this->data);
    $this->assertTrue($object->hasMeta());
    unset($this->data->{Resource::META});
    $this->assertFalse($object->hasMeta());
  }
}
