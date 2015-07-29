<?php

namespace CloudCreativity\JsonApi\Object\Relationships;

use CloudCreativity\JsonApi\Object\ResourceIdentifier\ResourceIdentifier;
use CloudCreativity\JsonApi\Object\ResourceIdentifier\ResourceIdentifierCollection;
use CloudCreativity\JsonApi\Object\StandardObject;

class RelationshipTest extends \PHPUnit_Framework_TestCase
{

    protected $belongsTo;
    protected $hasMany;

    protected function setUp()
    {
        $this->belongsTo = new \stdClass();
        $this->belongsTo->{ResourceIdentifier::TYPE} = 'foo';
        $this->belongsTo->{ResourceIdentifier::ID} = 123;

        $a = new \stdClass();
        $a->{ResourceIdentifier::TYPE} = 'bar';
        $a->{ResourceIdentifier::ID} = 456;

        $b = new \stdClass();
        $b->{ResourceIdentifier::TYPE} = 'baz';
        $b->{ResourceIdentifier::ID} = 789;

        $this->hasMany = [$a, $b];
    }

    public function testBelongsTo()
    {
        $input = new \stdClass();
        $input->{Relationship::DATA} = $this->belongsTo;

        $object = new Relationship($input);
        $expected = new ResourceIdentifier($this->belongsTo);

        $this->assertEquals($expected, $object->getData());
        $this->assertTrue($object->isBelongsTo());
        $this->assertFalse($object->isHasMany());
    }

    public function testEmptyBelongsTo()
    {
        $input = new \stdClass();
        $input->{Relationship::DATA} = null;

        $object = new Relationship($input);

        $this->assertNull($object->getData());
        $this->assertTrue($object->isBelongsTo());
        $this->assertFalse($object->isHasMany());
    }

    public function testHasMany()
    {
        $input = new \stdClass();
        $input->{Relationship::DATA} = $this->hasMany;

        $object = new Relationship($input);
        $expected = ResourceIdentifierCollection::create($this->hasMany);

        $this->assertEquals($expected, $object->getData());
        $this->assertTrue($object->isHasMany());
        $this->assertFalse($object->isBelongsTo());
    }

    public function testEmptyHasMany()
    {
        $input = new \stdClass();
        $input->{Relationship::DATA} = [];

        $object = new Relationship($input);

        $this->assertEquals(new ResourceIdentifierCollection(), $object->getData());
        $this->assertTrue($object->isHasMany());
        $this->assertFalse($object->isBelongsTo());
    }

    public function testGetMeta()
    {
        $object = new Relationship();

        $this->assertFalse($object->hasMeta());
        $this->assertEquals(new StandardObject(), $object->getMeta());

        $input = new \stdClass();
        $input->meta = new \stdClass();
        $input->meta->foo = 'bar';

        $object->setProxy($input);

        $this->assertTrue($object->hasMeta());
        $this->assertEquals(new StandardObject($input->meta), $object->getMeta());
    }
}
