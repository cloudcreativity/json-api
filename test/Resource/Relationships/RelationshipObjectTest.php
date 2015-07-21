<?php

namespace Appativity\JsonApi\Resource\Relationships;

use Appativity\JsonApi\Resource\Identifier\Identifier;
use Appativity\JsonApi\Resource\Identifier\IdentifierCollection;

class RelationshipObjectTest extends \PHPUnit_Framework_TestCase
{

    protected $belongsTo = [
        RelationshipObject::DATA => [
            Identifier::TYPE => 'foo',
            Identifier::ID => 123,
        ],
    ];

    protected $hasMany = [
        RelationshipObject::DATA => [
            [
                Identifier::TYPE => 'bar',
                Identifier::ID => 123,
            ],
            [
                Identifier::TYPE => 'baz',
                Identifier::ID => 234,
            ],
        ],
    ];

    public function testBelongsTo()
    {
        $object = new RelationshipObject($this->belongsTo);
        $expected = Identifier::create($this->belongsTo[RelationshipObject::DATA]);

        $this->assertEquals($expected, $object->getData());
        $this->assertTrue($object->isBelongsTo());
        $this->assertFalse($object->isHasMany());
    }

    public function testEmptyBelongsTo()
    {
        $object = new RelationshipObject([
            RelationshipObject::DATA => null,
        ]);

        $this->assertNull($object->getData());
        $this->assertTrue($object->isBelongsTo());
        $this->assertFalse($object->isHasMany());
    }

    public function testHasMany()
    {
        $object = new RelationshipObject($this->hasMany);
        $expected = IdentifierCollection::create($this->hasMany[RelationshipObject::DATA]);

        $this->assertEquals($expected, $object->getData());
        $this->assertTrue($object->isHasMany());
        $this->assertFalse($object->isBelongsTo());
    }

    public function testEmptyHasMany()
    {
        $object = new RelationshipObject([
            RelationshipObject::DATA => [],
        ]);

        $this->assertEquals(new IdentifierCollection(), $object->getData());
        $this->assertTrue($object->isHasMany());
        $this->assertFalse($object->isBelongsTo());
    }

    public function testToArray()
    {
        $object = new RelationshipObject($this->belongsTo);

        $this->assertEquals($this->belongsTo, $object->toArray());
    }
}
