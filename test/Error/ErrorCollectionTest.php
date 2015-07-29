<?php

namespace CloudCreativity\JsonApi\Error;

use Neomerx\JsonApi\Contracts\Document\ErrorInterface;

class ErrorCollectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ErrorObject
     */
    protected $a;

    /**
     * @var ErrorObject
     */
    protected $b;

    protected $interface;

    protected function setUp()
    {
        $this->a = new ErrorObject();
        $this->a->setId('foo');

        $this->b = new ErrorObject();
        $this->b->setId('bar');

        $this->interface = $this->getMock(ErrorInterface::class);
    }

    public function testConstruct()
    {
        $collection = new ErrorCollection([$this->a, $this->b, $this->interface]);

        $this->assertSame([$this->a, $this->b, $this->interface], $collection->getAll());

        return $collection;
    }

    /**
     * @depends testConstruct
     */
    public function testIterator(ErrorCollection $collection)
    {
        $this->assertSame($collection->getAll(), iterator_to_array($collection));
    }

    /**
     * @depends testConstruct
     */
    public function testCountable(ErrorCollection $collection)
    {
        $this->assertSame(count($collection->getAll()), count($collection));
    }

    /**
     * @depends testConstruct
     */
    public function testClone(ErrorCollection $collection)
    {
        $clone = clone $collection;
        $this->assertEquals($collection->getAll(), $clone->getAll());
        $this->assertNotSame($collection->getAll(), $clone->getAll());
    }

    public function testAdd()
    {
        $collection = new ErrorCollection();

        $this->assertSame($collection, $collection->add($this->interface));
        $collection->add($this->a);
        $this->assertSame([$this->interface, $this->a], $collection->getAll());
    }

    public function testAddMany()
    {
        $collection = new ErrorCollection();
        $expected = [$this->a, $this->b, $this->interface];

        $this->assertSame($collection, $collection->addMany($expected));
        $this->assertSame($expected, $collection->getAll());

        return $collection;
    }

    /**
     * @depends testAddMany
     */
    public function testClear(ErrorCollection $collection)
    {
        $this->assertSame($collection, $collection->clear());
        $this->assertEmpty($collection->getAll());
    }

    public function testIsEmpty()
    {
        $collection = new ErrorCollection();

        $this->assertTrue($collection->isEmpty());
        $collection->add($this->a);
        $this->assertFalse($collection->isEmpty());
    }

    public function testError()
    {
        $collection = new ErrorCollection();
        $expected = [$this->a, $this->b, $this->interface];

        $this->assertSame($collection, $collection->error($this->a));

        $collection->error($this->b->toArray())
            ->error($this->interface);

        $this->assertEquals($expected, $collection->getAll());
    }

    public function testMerge()
    {
        $expected = [$this->a, $this->b, $this->interface];
        $a = new ErrorCollection([$this->a, $this->b]);
        $b = new ErrorCollection([$this->interface]);

        $this->assertSame($a, $a->merge($b));
        $this->assertEquals($expected, $a->getAll());
        $this->assertNotSame($expected, $a->getAll(), 'Expecting errors to be cloned when merging.');
    }

    public function testGetStatus5xx()
    {
        $this->a->setStatus(503);
        $collection = new ErrorCollection([$this->a, $this->b]);
        $this->assertEquals(503, $collection->getStatus());
    }

    public function testGetStatusMixed5xx()
    {
        $this->a->setStatus(501);
        $this->b->setStatus(502);
        $collection = new ErrorCollection([$this->a, $this->b]);
        $this->assertEquals(500, $collection->getStatus());
    }

    public function testGetStatus4xx()
    {
        $this->a->setStatus(422);
        $collection = new ErrorCollection([$this->a, $this->b]);
        $this->assertEquals(422, $collection->getStatus());
    }

    public function testGetStatusMixed4xx()
    {
        $this->a->setStatus(401);
        $this->b->setStatus(402);
        $collection = new ErrorCollection([$this->a, $this->b]);
        $this->assertEquals(400, $collection->getStatus());
    }

    public function testGetStatusUnknown()
    {
        $collection = new ErrorCollection([$this->a, $this->b, $this->interface]);
        $this->assertEquals(500, $collection->getStatus());
    }

    public function testSetSourcePointer()
    {
        $pointer = '/data/foo/bar';
        $collection = new ErrorCollection([$this->a, $this->b, $this->interface]);

        $this->assertSame($collection, $collection->setSourcePointer($pointer));
        $this->assertSame($pointer, $this->a->source()->getPointer());
        $this->assertSame($pointer, $this->b->source()->getPointer());
    }

    public function testSetSourcePointerWithCallback()
    {
        $pointerA = '/foo/bar';
        $pointerB = '/baz/bat';
        $prefix = '/data';

        $collection = new ErrorCollection([$this->a, $this->b, $this->interface]);
        $this->a->source()->setPointer($pointerA);
        $this->b->source()->setPointer($pointerB);

        $collection->setSourcePointer(function ($current) use ($prefix) {
            return sprintf('%s%s', $prefix, $current);
        });

        $this->assertSame(sprintf('%s%s', $prefix, $pointerA), $this->a->source()->getPointer());
        $this->assertSame(sprintf('%s%s', $prefix, $pointerB), $this->b->source()->getPointer());
    }
}
