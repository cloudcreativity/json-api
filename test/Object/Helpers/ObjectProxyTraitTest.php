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

namespace CloudCreativity\JsonApi\Object\Helpers;

use CloudCreativity\JsonApi\Contracts\Object\StandardObjectInterface;
use CloudCreativity\JsonApi\TestCase;
use DateTime;
use stdClass;

/**
 * Class ObjectProxyTraitTest
 * @package CloudCreativity\JsonApi
 */
class ObjectProxyTraitTest extends TestCase
{

    /**
     * @var stdClass
     */
    protected $proxy;

    /**
     * @var ObjectProxyTrait
     */
    protected $trait;

    protected function setUp()
    {
        $this->proxy = new stdClass();
        $this->proxy->foo = 'foobar';
        $this->proxy->bar = 'bazbat';

        $this->trait = $this->getMockForTrait(__NAMESPACE__ . '\ObjectProxyTrait');
    }

    public function testSetProxy()
    {
        $this->assertSame($this->trait, $this->trait->setProxy($this->proxy));
        $this->assertSame($this->proxy, $this->trait->getProxy());
    }

    public function testGet()
    {
        $this->trait->setProxy($this->proxy);
        $this->assertSame('foobar', $this->trait->get('foo'));
        $this->assertNull($this->trait->get('baz'));
        $this->assertFalse($this->trait->get('baz', false));
    }

    public function testSet()
    {
        $this->assertSame($this->trait, $this->trait->set('foo', 'foobar'));
        $this->assertSame('foobar', $this->trait->get('foo'));
    }

    public function testHas()
    {
        $this->assertFalse($this->trait->has('foo'));
        $this->trait->set('foo', 'foobar');
        $this->assertTrue($this->trait->has('foo'));

        $this->trait->set('bar', null);
        $this->assertTrue($this->trait->has('bar'));
    }

    public function testHasAll()
    {
        $this->trait->setProxy($this->proxy);

        $this->assertTrue($this->trait->has(['foo', 'bar']));
        $this->assertFalse($this->trait->has(['foo', 'bar', 'baz']));
    }

    public function testHasAny()
    {
        $this->trait->setProxy($this->proxy);
        $this->assertTrue($this->trait->hasAny(['foo', 'baz']));
        $this->assertFalse($this->trait->hasAny(['baz']));
    }

    public function testRemove()
    {
        $this->trait->set('foo', 'foobar');
        $this->assertSame($this->trait, $this->trait->remove('foo'));
        $this->assertNull($this->trait->get('foo'));
        $this->assertFalse($this->trait->has('foo'));
    }

    public function testGetProperties()
    {
        $this->trait->setProxy($this->proxy);
        $expected = (array) $this->proxy;
        $expected['baz'] = null;
        $keys = ['foo', 'bar', 'baz'];

        $this->assertEquals($expected, $this->trait->getProperties($keys));
        $expected['baz'] = false;
        $this->assertEquals($expected, $this->trait->getProperties($keys, false));
    }

    public function testSetProperties()
    {
        $expected = (array) $this->proxy;

        $this->assertSame($this->trait, $this->trait->setProperties($expected));
        $this->assertEquals('foobar', $this->trait->get('foo'));
        $this->assertEquals('bazbat', $this->trait->get('bar'));
    }

    public function testGetMany()
    {
        $this->trait->setProxy($this->proxy);
        $expected = ['foo' => 'foobar'];

        $this->assertEquals($expected, $this->trait->getMany(['foo', 'baz']));
    }

    public function testRemoveProperties()
    {
        $this->trait->setProxy($this->proxy);

        $this->assertSame($this->trait, $this->trait->removeProperties(['foo', 'baz']));
        $this->assertFalse($this->trait->has('foo'));
    }

    public function testReduce()
    {
        $this->trait->setProxy($this->proxy);
        $expected = clone $this->proxy;
        unset($expected->bar);

        $this->assertSame($this->trait, $this->trait->reduce(['foo', 'baz']));
        $this->assertEquals($expected, $this->trait->getProxy());
    }

    public function testKeys()
    {
        $this->assertEmpty($this->trait->keys());
        $this->trait->setProxy($this->proxy);
        $this->assertEquals(['foo', 'bar'], $this->trait->keys());
    }

    public function testMapKey()
    {
        $alt = 'foo' . 'bar';

        $expected = clone $this->proxy;
        $expected->{$alt} = $expected->foo;
        unset($expected->foo);

        $this->trait->setProxy($this->proxy);
        $this->assertSame($this->trait, $this->trait->mapKey('foo', $alt));

        $this->assertEquals($expected, $this->trait->getProxy());
        $this->assertFalse($this->trait->has('foo'));
    }

    public function testMapKeys()
    {
        $altA = 'foo' . 'bar';
        $altB = 'bar' . 'foo';

        $expected = new stdClass();
        $expected->{$altA} = $this->proxy->foo;
        $expected->{$altB} = $this->proxy->bar;

        $this->trait->setProxy($this->proxy);

        $this->assertSame($this->trait, $this->trait->mapKeys([
            'foo' => $altA,
            'bar' => $altB,
            'baz' => 'ignored',
        ]));

        $this->assertEquals($expected, $this->trait->getProxy());
    }

    public function testTransformKeys()
    {
        $this->trait->setProxy($this->proxy);

        $expected = new stdClass();
        $expected->a = $this->proxy->foo;
        $expected->b = $this->proxy->bar;

        $this->assertSame($this->trait, $this->trait->transformKeys(function ($key) {
            return ('foo') === $key ? 'a' : 'b';
        }));

        $this->assertEquals($expected, $this->trait->getProxy());
    }

    public function testTransform()
    {
        $proxy = new stdClass();
        $proxy->start = '2015-01-01 12:00:00';
        $proxy->finish = '2016-01-01 23:59:59';

        $expected = new stdClass();
        $expected->start = new DateTime($proxy->start);
        $expected->finish = new DateTime($proxy->finish);

        $this->trait->setProxy($proxy);

        $this->assertSame($this->trait, $this->trait->transform([
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
            'foo' => 'foobar',
            'bar' => 'bazbat',
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

    public function testAsObject()
    {
        $expected = new stdClass();
        $expected->some = 'value';

        $this->proxy->foo = $expected;
        $this->trait->setProxy($this->proxy);

        $actual = $this->trait->asObject('foo');
        $this->assertInstanceOf(StandardObjectInterface::class, $actual);
        $this->assertEquals('value', $actual->get('some'));
    }
}
