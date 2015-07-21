<?php

namespace Appativity\JsonApi\Resource\Relationships;

use Appativity\JsonApi\Resource\Identifier\Identifier;

class RelationshipsTest extends \PHPUnit_Framework_TestCase
{

    const BELONGS_TO = 'foo';
    const HAS_MANY = 'bars';
    const MISSING = 'foobar';

    /**
     * @var array
     */
    protected $data;

    /**
     * @var RelationshipObject
     */
    protected $belongsTo;

    /**
     * @var RelationshipObject
     */
    protected $hasMany;

    protected function setUp()
    {
        $this->belongsTo = new RelationshipObject([
            RelationshipObject::DATA => [
                Identifier::TYPE => 'foo',
                Identifier::ID => 123,
            ]
        ]);

        $this->hasMany = new RelationshipObject([
            RelationshipObject::DATA => [
                [
                    Identifier::TYPE => 'bar',
                    Identifier::ID => 1,
                ],
                [
                    Identifier::TYPE => 'bar',
                    Identifier::ID => 2,
                ],
            ]
        ]);

        $this->data = [
            static::BELONGS_TO => $this->belongsTo->toArray(),
            static::HAS_MANY => $this->hasMany->toArray(),
        ];
    }

    public function testConstruct()
    {
        $relationships = new Relationships($this->data);

        $this->assertEquals($this->data, $relationships->toArray());

        return $relationships;
    }

    /**
     * @depends testConstruct
     */
    public function testGet(Relationships $relationships)
    {
        $this->assertEquals($this->belongsTo, $relationships->get(static::BELONGS_TO));
        $this->assertEquals($this->hasMany, $relationships->get(static::HAS_MANY));
    }

    /**
     * @depends testConstruct
     */
    public function testGetMissing(Relationships $relationships)
    {
        $this->setExpectedException('RuntimeException');
        $relationships->get(static::MISSING);
    }

    /**
     * @depends testConstruct
     */
    public function testHas(Relationships $relationships)
    {
        $this->assertTrue($relationships->has(static::BELONGS_TO));
        $this->assertFalse($relationships->has(static::MISSING));
    }

    /**
     * @depends testConstruct
     */
    public function testKeys(Relationships $relationships)
    {
        $this->assertEquals(array_keys($this->data), $relationships->keys());
    }

    /**
     * @depends testConstruct
     */
    public function testIteration(Relationships $relationships)
    {
        $expected = [
            static::BELONGS_TO => $relationships->get(static::BELONGS_TO),
            static::HAS_MANY => $relationships->get(static::HAS_MANY),
        ];

        $this->assertEquals($expected, iterator_to_array($relationships));
    }

    /**
     * @depends testConstruct
     */
    public function testCount(Relationships $relationships)
    {
        $this->assertSame(count($this->data), count($relationships));
    }

    /**
     * @depends testConstruct
     */
    public function testHasAll(Relationships $relationships)
    {
        $this->assertTrue($relationships->hasAll(array_keys($this->data)));
        $this->assertFalse($relationships->hasAll([static::BELONGS_TO, static::MISSING]));
    }

    /**
     * @depends testConstruct
     */
    public function testHasAny(Relationships $relationships)
    {
        $this->assertTrue($relationships->hasAny([static::MISSING, static::BELONGS_TO]));
        $this->assertFalse($relationships->hasAny([static::MISSING]));
    }

    /**
     * @depends testConstruct
     */
    public function testHasOnly(Relationships $relationships)
    {
        $this->assertTrue($relationships->hasOnly(array_keys($this->data)));
        $this->assertFalse($relationships->hasOnly([static::BELONGS_TO, static::MISSING]));
    }
}
