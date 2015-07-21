<?php

namespace Appativity\JsonApi\Resource\Identifier;

class IdentifierTest extends \PHPUnit_Framework_TestCase
{

    const TYPE = 'foo';
    const ID = 123;

    public function testConstruct()
    {
        $identifier = new Identifier(static::TYPE, static::ID);

        $this->assertSame(static::TYPE, $identifier->getType());
        $this->assertSame(static::ID, $identifier->getId());
    }

    public function testType()
    {
        $identifier = new Identifier();

        $this->assertFalse($identifier->hasType());
        $this->assertSame($identifier, $identifier->setType(static::TYPE));
        $this->assertSame(static::TYPE, $identifier->getType());
        $this->assertTrue($identifier->hasType());

        return $identifier;
    }

    /**
     * @depends testType
     */
    public function testIsType(Identifier $identifier)
    {
        $this->assertTrue($identifier->isType(static::TYPE));
        $this->assertFalse($identifier->isType('invalid-type'));
        $this->assertTrue($identifier->isType(['not-a-match', static::TYPE]));
    }

    /**
     * @depends testType
     */
    public function testId(Identifier $identifier)
    {
        $this->assertFalse($identifier->hasId());
        $this->assertSame($identifier, $identifier->setId(static::ID));
        $this->assertSame(static::ID, $identifier->getId());
        $this->assertTrue($identifier->hasId());

        return $identifier;
    }

    /**
     * @depends testType
     */
    public function testArrayExchangeable(Identifier $identifier)
    {
        $arr = [
            Identifier::TYPE => static::TYPE,
            Identifier::ID => static::ID,
        ];

        $this->assertEquals($arr, $identifier->toArray());

        $check = new Identifier();

        $this->assertSame($check, $check->exchangeArray($arr));
        $this->assertEquals($identifier, $check);

        return $identifier;
    }

    /**
     * @depends testArrayExchangeable
     */
    public function testCreate(Identifier $expected)
    {
        $actual = Identifier::create($expected->toArray());
        $this->assertEquals($expected, $actual);
    }

    public function testIsComplete()
    {
        $this->assertFalse((new Identifier())->isComplete());
        $this->assertFalse((new Identifier())->setType(static::TYPE)->isComplete());
        $this->assertFalse((new Identifier())->setId(static::ID)->isComplete());

        $complete = new Identifier();
        $complete->setType(static::TYPE)->setId(static::ID);

        $this->assertTrue($complete->isComplete());
    }

    public function testMapType()
    {
        $identifier = (new Identifier())->setType(static::TYPE);
        $expected = 'My\Class';

        $map = [
            'not-a-match' => 'unexpected',
            static::TYPE => $expected,
        ];

        $this->assertSame($expected, $identifier->mapType($map));

        $this->setExpectedException('RuntimeException');
        $identifier->mapType([
            'not-a-match' => 'unexpected',
        ]);
    }
}
