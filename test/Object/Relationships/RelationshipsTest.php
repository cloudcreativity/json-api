<?php

namespace CloudCreativity\JsonApi\Object\Relationships;

use CloudCreativity\JsonApi\Object\ResourceIdentifier\ResourceIdentifier;

class RelationshipsTest extends \PHPUnit_Framework_TestCase
{

    const KEY_A = 'foo';
    const KEY_B = 'bar';

    protected $data;

    protected function setUp()
    {
        $belongsTo = new \stdClass();
        $belongsTo->{ResourceIdentifier::TYPE} = 'foo';
        $belongsTo->{ResourceIdentifier::ID} = 123;

        $a = new \stdClass();
        $a->{Relationship::DATA} = $belongsTo;

        $b = new \stdClass();
        $b->{Relationship::DATA} = null;

        $this->data = new \stdClass();
        $this->data->{static::KEY_A} = $a;
        $this->data->{static::KEY_B} = $b;
    }

    public function testGet()
    {
        $object = new Relationships($this->data);
        $a = new Relationship($this->data->{static::KEY_A});
        $b = new Relationship($this->data->{static::KEY_B});

        $this->assertEquals($a, $object->get(static::KEY_A));
        $this->assertEquals($b, $object->get(static::KEY_B));

        return $object;
    }

    /**
     * @depends testGet
     */
    public function testIterator(Relationships $object)
    {
        $expected = [
            static::KEY_A => $object->get(static::KEY_A),
            static::KEY_B => $object->get(static::KEY_B),
        ];

        $this->assertEquals($expected, iterator_to_array($object));
    }

    public function testMagicGet()
    {
        $object = new Relationships($this->data);

        $this->assertEquals($this->data->{static::KEY_A}, $object->{static::KEY_A});
    }
}
