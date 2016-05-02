<?php

/**
 * Copyright 2015 Cloud Creativity Limited
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace CloudCreativity\JsonApi\Object;

use CloudCreativity\JsonApi\TestCase;
use DateTime;
use stdClass;

class ObjectProxyTraitTest extends TestCase
{

    const KEY_A = 'foo';
    const VALUE_A = 'foobar';

    const KEY_B = 'bar';
    const VALUE_B = 'bazbat';

    const KEY_C = 'baz';

    protected $proxy;

    /**
     * @var ObjectProxyTrait
     */
    protected $trait;

    protected function setUp()
    {
        $this->proxy = new stdClass();
        $this->proxy->{static::KEY_A} = static::VALUE_A;
        $this->proxy->{static::KEY_B} = static::VALUE_B;

        $this->trait = $this->getMockForTrait('CloudCreativity\JsonApi\Object\ObjectProxyTrait');
    }

    public function testSetProxy()
    {
        $this->assertSame($this->trait, $this->trait->setProxy($this->proxy));
        $this->assertSame($this->proxy, $this->trait->getProxy());
    }

    public function testGet()
    {
        $this->trait->setProxy($this->proxy);
        $this->assertSame(static::VALUE_A, $this->trait->get(static::KEY_A));
        $this->assertNull($this->trait->get(static::KEY_C));
        $this->assertFalse($this->trait->get(static::KEY_C, false));
    }

    public function testSet()
    {
        $this->assertSame($this->trait, $this->trait->set(static::KEY_A, static::VALUE_A));
        $this->assertSame(static::VALUE_A, $this->trait->get(static::KEY_A));
    }

    public function testHas()
    {
        $this->assertFalse($this->trait->has(static::KEY_A));
        $this->trait->set(static::KEY_A, static::VALUE_A);
        $this->assertTrue($this->trait->has(static::KEY_A));

        $this->trait->set(static::KEY_B, null);
        $this->assertTrue($this->trait->has(static::KEY_B));
    }

    public function testHasAll()
    {
        $this->trait->setProxy($this->proxy);

        $this->assertTrue($this->trait->hasAll([static::KEY_A, static::KEY_B]));
        $this->assertFalse($this->trait->hasAll([static::KEY_A, static::KEY_B, static::KEY_C]));
    }

    public function testHasAny()
    {
        $this->trait->setProxy($this->proxy);
        $this->assertTrue($this->trait->hasAny([static::KEY_A, static::KEY_C]));
        $this->assertFalse($this->trait->hasAny([static::KEY_C]));
    }

    public function testRemove()
    {
        $this->trait->set(static::KEY_A, static::VALUE_A);
        $this->assertSame($this->trait, $this->trait->remove(static::KEY_A));
        $this->assertNull($this->trait->get(static::KEY_A));
        $this->assertFalse($this->trait->has(static::KEY_A));
    }

    public function testGetProperties()
    {
        $this->trait->setProxy($this->proxy);
        $expected = (array) $this->proxy;
        $expected[static::KEY_C] = null;
        $keys = [static::KEY_A, static::KEY_B, static::KEY_C];

        $this->assertEquals($expected, $this->trait->getProperties($keys));
        $expected[static::KEY_C] = false;
        $this->assertEquals($expected, $this->trait->getProperties($keys, false));
    }

    public function testSetProperties()
    {
        $expected = (array) $this->proxy;

        $this->assertSame($this->trait, $this->trait->setProperties($expected));
        $this->assertEquals(static::VALUE_A, $this->trait->get(static::KEY_A));
        $this->assertEquals(static::VALUE_B, $this->trait->get(static::KEY_B));
    }

    public function testRemoveProperties()
    {
        $this->trait->setProxy($this->proxy);

        $this->assertSame($this->trait, $this->trait->removeProperties([static::KEY_A, static::KEY_C]));
        $this->assertFalse($this->trait->has(static::KEY_A));
    }

    public function testReduce()
    {
        $this->trait->setProxy($this->proxy);
        $expected = clone $this->proxy;
        unset($expected->{static::KEY_B});

        $this->assertSame($this->trait, $this->trait->reduce([static::KEY_A, static::KEY_C]));
        $this->assertEquals($expected, $this->trait->getProxy());
    }

    public function testKeys()
    {
        $this->assertEmpty($this->trait->keys());
        $this->trait->setProxy($this->proxy);
        $this->assertEquals([static::KEY_A, static::KEY_B], $this->trait->keys());
    }

    public function testMapKey()
    {
        $alt = static::KEY_A . static::KEY_B;

        $expected = clone $this->proxy;
        $expected->{$alt} = $expected->{static::KEY_A};
        unset($expected->{static::KEY_A});

        $this->trait->setProxy($this->proxy);
        $this->assertSame($this->trait, $this->trait->mapKey(static::KEY_A, $alt));

        $this->assertEquals($expected, $this->trait->getProxy());
        $this->assertFalse($this->trait->has(static::KEY_A));
    }

    public function testMapKeys()
    {
        $altA = static::KEY_A . static::KEY_B;
        $altB = static::KEY_B . static::KEY_A;

        $expected = new stdClass();
        $expected->{$altA} = $this->proxy->{static::KEY_A};
        $expected->{$altB} = $this->proxy->{static::KEY_B};

        $this->trait->setProxy($this->proxy);

        $this->assertSame($this->trait, $this->trait->mapKeys([
            static::KEY_A => $altA,
            static::KEY_B => $altB,
            static::KEY_C => 'ignored',
        ]));

        $this->assertEquals($expected, $this->trait->getProxy());
    }

    public function testTransformKeys()
    {
        $this->trait->setProxy($this->proxy);

        $expected = new stdClass();
        $expected->a = $this->proxy->{static::KEY_A};
        $expected->b = $this->proxy->{static::KEY_B};

        $this->assertSame($this->trait, $this->trait->transformKeys(function ($key) {
            return (static::KEY_A) === $key ? 'a' : 'b';
        }));

        $this->assertEquals($expected, $this->trait->getProxy());
    }

    public function testConvertValues()
    {
        $proxy = new stdClass();
        $proxy->start = '2015-01-01 12:00:00';
        $proxy->finish = '2016-01-01 23:59:59';

        $expected = new stdClass();
        $expected->start = new DateTime($proxy->start);
        $expected->finish = new DateTime($proxy->finish);

        $this->trait->setProxy($proxy);

        $this->assertSame($this->trait, $this->trait->convertValues([
            'start',
            'finish',
            'foo'
        ], function ($value) {
            return new DateTime($value);
        }));

        $this->assertEquals($expected, $this->trait->getProxy());
    }

    public function testArrayExchangeable()
    {
        $arr = [
            static::KEY_A => static::VALUE_A,
            static::KEY_B => static::VALUE_B,
        ];

        $this->assertSame($this->trait, $this->trait->exchangeArray($arr));
        $this->assertEquals($arr, $this->trait->toArray());
        $this->assertEquals($this->proxy, $this->trait->getProxy());
    }

    public function testToArray()
    {
        $object = new stdClass();
        $object->foo = 'bar';
        $object->baz = new stdClass();
        $object->baz->bat = 'bazbat';

        $expected = ObjectUtils::toArray($object);
        $this->trait->setProxy($object);
        $this->assertSame($expected, $this->trait->toArray());
    }
}
