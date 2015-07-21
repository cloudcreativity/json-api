<?php

namespace Appativity\JsonApi\Resource\Identifier;

class IdentifierCollectionTest extends \PHPUnit_Framework_TestCase
{

    protected $a;
    protected $b;

    protected function setUp()
    {
        $this->a = Identifier::create([
            Identifier::TYPE => 'foo',
            Identifier::ID => 123,
        ]);

        $this->b = Identifier::create([
            Identifier::TYPE => 'bar',
            Identifier::ID => 456,
        ]);
    }

    public function testConstruct()
    {
        $collection = new IdentifierCollection([$this->a, $this->b]);
        $this->assertSame([$this->a, $this->b], $collection->getAll());

        return $collection;
    }

    /**
     * @depends testConstruct
     */
    public function testIterator(IdentifierCollection $collection)
    {
        $expected = $collection->getAll();
        $this->assertEquals($expected, iterator_to_array($collection));
    }

    /**
     * @depends testConstruct
     */
    public function testCountable(IdentifierCollection $collection)
    {
        $this->assertSame(2, count($collection));
    }

    /**
     * @depends testConstruct
     */
    public function testClear(IdentifierCollection $collection)
    {
        $this->assertSame($collection, $collection->clear());
        $this->assertEmpty($collection->getAll());
    }

    public function testIsEmpty()
    {
        $collection = new IdentifierCollection();
        $this->assertTrue($collection->isEmpty());
        $collection->add($this->a);
        $this->assertFalse($collection->isEmpty());
    }

    public function testAdd()
    {
        $collection = new IdentifierCollection();

        $this->assertSame($collection, $collection->add($this->a));
        $collection->add($this->b);
        $this->assertSame([$this->a, $this->b], $collection->getAll());

        return $collection;
    }

    /**
     * @depends testAdd
     */
    public function testAddIgnoresDuplicates(IdentifierCollection $collection)
    {
        $expected = $collection->getAll();

        $collection->add($this->a)->add($this->b);

        $this->assertEquals($expected, $collection->getAll());
    }

    public function testHas()
    {
        $collection = new IdentifierCollection([$this->a]);

        $this->assertTrue($collection->has(clone $this->a));
        $this->assertFalse($collection->has($this->b));
    }

    public function testArrayExchangeable()
    {
        $arr = [
            $this->a->toArray(),
            $this->b->toArray(),
        ];

        $expected = new IdentifierCollection([$this->a, $this->b]);
        $actual = new IdentifierCollection();

        $this->assertSame($actual, $actual->exchangeArray($arr));
        $this->assertEquals($expected, $actual);
        $this->assertEquals($arr, $expected->toArray());

        return $expected;
    }

    /**
     * @depends testArrayExchangeable
     */
    public function testCreate(IdentifierCollection $expected)
    {
        $actual = IdentifierCollection::create($expected->toArray());
        $this->assertEquals($expected, $actual);
    }

    public function testIsComplete()
    {
        $collection = new IdentifierCollection();

        $this->assertTrue($collection->isComplete());
        $collection->add($this->a);
        $this->assertTrue($collection->isComplete());
        $collection->add(new Identifier());
        $this->assertFalse($collection->isComplete());
    }

    public function testIsOnly()
    {
        $collection = new IdentifierCollection();

        $this->assertTrue($collection->isOnly($this->a->getType()));
        $collection->add($this->a);
        $this->assertTrue($collection->isOnly($this->a->getType()));

        $collection->add($this->b);
        $this->assertFalse($collection->isOnly($this->a->getType()));
        $this->assertFalse($collection->isOnly($this->b->getType()));

        $this->assertTrue($collection->isOnly([
            $this->a->getType(),
            $this->b->getType(),
        ]));
    }

    public function testGetIds()
    {
        $collection = new IdentifierCollection([$this->a, $this->b]);
        $expected = [$this->a->getId(), $this->b->getId()];

        $this->assertEquals($expected, $collection->getIds());
    }

    public function testMap()
    {
        $collection = new IdentifierCollection([$this->a, $this->b]);

        $expected = [
            $this->a->getType() => [
                $this->a->getId(),
            ],
            $this->b->getType() => [
                $this->b->getId(),
            ],
        ];

        $this->assertEquals($expected, $collection->map());

        return $collection;
    }

    /**
     * @depends testMap
     */
    public function testMapWithTypeConversion(IdentifierCollection $collection)
    {
        $a = 'Alias-A';
        $b = 'Alias-B';

        $map = [
            $this->a->getType() => $a,
            $this->b->getType() => $b,
        ];

        $expected = [
            $a => [$this->a->getId()],
            $b => [$this->b->getId()],
        ];

        $this->assertEquals($expected, $collection->map($map));
    }
}
