<?php

namespace Appativity\JsonApi\Resource;

use Appativity\JsonApi\Resource\Identifier\Identifier;
use Appativity\JsonApi\Resource\Attributes\Attributes;
use Appativity\JsonApi\Resource\Relationships\RelationshipObject;
use Appativity\JsonApi\Resource\Relationships\Relationships;

class ResourceObjectTest extends \PHPUnit_Framework_TestCase
{

    protected $data = [
        ResourceObject::TYPE => 'foo',
        ResourceObject::ID => 123,
        ResourceObject::ATTRIBUTES => [
            'foo' => 'bar',
            'baz' => 'bat',
        ],
        ResourceObject::RELATIONSHIPS => [
            'foobar' => [
                RelationshipObject::DATA => [
                    Identifier::TYPE => 'foo',
                    Identifier::ID => 234,
                ],
            ],
            'bazbats' => [
                RelationshipObject::DATA => [
                    [
                        Identifier::TYPE => 'baz',
                        Identifier::ID => 1,
                    ],
                    [
                        Identifier::TYPE => 'bat',
                        Identifier::ID => 2,
                    ],
                ],
            ],
        ],
    ];

    public function testConstruct()
    {
        $object = new ResourceObject($this->data);

        $this->assertEquals($this->data, $object->toArray());

        return $object;
    }

    /**
     * @depends testConstruct
     */
    public function testGetIdentifier(ResourceObject $object)
    {
        $expected = new Identifier($this->data[ResourceObject::TYPE], $this->data[ResourceObject::ID]);
        $actual = $object->getIdentifier();

        $this->assertEquals($expected, $actual);
        $this->assertNotSame($actual, $object->getIdentifier());
    }

    /**
     * @depends testConstruct
     */
    public function testGetAttributes(ResourceObject $object)
    {
        $expected = new Attributes($this->data[ResourceObject::ATTRIBUTES]);
        $actual = $object->getAttributes();

        $this->assertEquals($expected, $actual);
        $this->assertNotSame($actual, $object->getAttributes());
    }

    /**
     * @depends testConstruct
     */
    public function testGetRelationships(ResourceObject $object)
    {
        $expected = new Relationships($this->data[ResourceObject::RELATIONSHIPS]);
        $actual = $object->getRelationships();

        $this->assertEquals($expected, $actual);
        $this->assertNotSame($actual, $object->getRelationships());
    }
}
