<?php

namespace Appativity\JsonApi\Resource\Attributes;

class AttributesTest extends \PHPUnit_Framework_TestCase
{

    const KEY_A = 'foo';
    const VALUE_A = 'bar';
    const KEY_B = 'baz';
    const VALUE_B = 'bat';
    const KEY_EMPTY = 'foobar';
    const MISSING = 'bazbat';

    protected $data = [
        self::KEY_A => self::VALUE_A,
        self::KEY_B => self::VALUE_B,
        self::KEY_EMPTY => null,
    ];

    public function testSet()
    {
        $attributes = new Attributes();
        $key = static::KEY_A;
        $value = static::VALUE_A;

        $this->assertFalse($attributes->has($key));
        $this->assertSame($attributes, $attributes->set($key, $value));
        $this->assertSame($value, $attributes->get($key));
        $this->assertTrue($attributes->has($key));
    }

    public function testSetNull()
    {
        $attributes = new Attributes();
        $key = static::KEY_EMPTY;

        $this->assertFalse($attributes->has($key));
        $attributes->set($key, null);
        $this->assertTrue($attributes->has($key));
        $this->assertNull($attributes->get($key, false));
    }

    public function testGetDefault()
    {
        $attributes = new Attributes();
        $key = static::KEY_A;

        $this->assertNull($attributes->get($key));
        $this->assertFalse($attributes->get($key, false));
    }

    public function testConstruct()
    {
        $attributes = new Attributes($this->data);

        $this->assertEquals($this->data, $attributes->getAll());

        return $attributes;
    }

    /**
     * @depends testConstruct
     */
    public function testIterator(Attributes $attributes)
    {
        $this->assertEquals($this->data, iterator_to_array($attributes));
    }

    /**
     * @depends testConstruct
     */
    public function testCountable(Attributes $attributes)
    {
        $this->assertSame(count($this->data), count($attributes));
    }

    /**
     * @depends testConstruct
     */
    public function testKeys(Attributes $attributes)
    {
        $this->assertEquals(array_keys($this->data), $attributes->keys());
    }

    /**
     * @depends testConstruct
     */
    public function testHasAll(Attributes $attributes)
    {
        $this->assertTrue($attributes->hasAll(array_keys($this->data)));
        $this->assertTrue($attributes->hasAll([static::KEY_B]));
        $this->assertFalse($attributes->hasAll([static::KEY_B, static::MISSING]));
    }

    /**
     * @depends testConstruct
     */
    public function testHasAny(Attributes $attributes)
    {
        $this->assertTrue($attributes->hasAny([static::MISSING, static::KEY_A]));
        $this->assertFalse($attributes->hasAny([static::MISSING]));
    }

    public function testHasOnly()
    {
        $attributes = new Attributes();
        $attributes->set(static::KEY_A, static::VALUE_A);

        $this->assertTrue($attributes->hasOnly(array_keys($this->data)));
        $attributes->set(static::MISSING, 'An unexpected value.');
        $this->assertFalse($attributes->hasOnly(array_keys($this->data)));
    }
}
