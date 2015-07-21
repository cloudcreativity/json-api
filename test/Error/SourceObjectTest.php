<?php

namespace Appativity\JsonApi\Error;

class SourceObjectTest extends \PHPUnit_Framework_TestCase
{

    const POINTER = '/foo/bar/baz';
    const PARAMETER = 'foobar';

    protected $data = [
        SourceObject::POINTER => self::POINTER,
        SourceObject::PARAMETER => self::PARAMETER,
    ];

    public function testConstruct()
    {
        $object = new SourceObject($this->data);

        $this->assertEquals($this->data, $object->toArray());

        return $object;
    }

    /**
     * @depends testConstruct
     */
    public function testAddCustom(SourceObject $object)
    {
        $key = 'foo';
        $value = 'bar';

        $object[$key] = $value;

        $this->assertEquals($value, $object[$key]);
        $this->assertEquals(array_merge($this->data, [$key => $value]), $object->toArray());
    }

    public function testJsonSerialize()
    {
        $object = new SourceObject($this->data);
        $this->assertEquals((object) $this->data, $object->jsonSerialize());
    }

    public function testSetPointer()
    {
        $object = new SourceObject();

        $this->assertNull($object->getPointer());
        $this->assertSame($object, $object->setPointer(static::POINTER));
        $this->assertSame(static::POINTER, $object[SourceObject::POINTER]);
        $this->assertSame(static::POINTER, $object->getPointer());
        $this->assertSame([SourceObject::POINTER => static::POINTER], $object->toArray());

        return $object;
    }

    /**
     * @depends testSetPointer
     */
    public function testSetPointerWithCallback(SourceObject $object)
    {
        $prefix = '/prefix';
        $expected = sprintf('%s%s', $prefix, static::POINTER);

        $object->setPointer(function ($current) use ($prefix) {
            return sprintf('%s%s', $prefix, $current);
        });

        $this->assertSame($expected, $object->getPointer());
    }

    public function testSetParameter()
    {
        $object = new SourceObject();

        $this->assertNull($object->getParameter());
        $this->assertSame($object, $object->setParameter(static::PARAMETER));
        $this->assertSame(static::PARAMETER, $object[SourceObject::PARAMETER]);
        $this->assertSame(static::PARAMETER, $object->getParameter());
        $this->assertSame([SourceObject::PARAMETER => static::PARAMETER], $object->toArray());
    }
}
