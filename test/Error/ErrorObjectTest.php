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

namespace CloudCreativity\JsonApi\Error;

use InvalidArgumentException;
use Neomerx\JsonApi\Contracts\Schema\LinkInterface;

class ErrorObjectTest extends \PHPUnit_Framework_TestCase
{

    const ID = 'foo';
    const STATUS = '422';
    const CODE = 'error_code';
    const TITLE = 'Error Title';
    const DETAIL = 'Description of error.';

    protected $meta = [
        'foo' => 'bar',
        'baz' => 'bat',
    ];

    protected $links = [
        'about' => 'http://www.example.tld',
    ];

    public function testGettersSetters()
    {
        $stack = [
            'id' => static::ID,
            'status' => static::STATUS,
            'code' => static::CODE,
            'title' => static::TITLE,
            'detail' => static::DETAIL,
            'links' => $this->links,
            'meta' => $this->meta,
        ];

        $object = new ErrorObject();

        foreach ($stack as $key => $value) {

            $getter = 'get' . ucfirst($key);
            $setter = 'set' . ucfirst($key);

            $this->assertSame($object, call_user_func([$object, $setter], $value), sprintf('Expecting setter for "%s" to be chainable.', $key));
            $this->assertEquals($value, call_user_func([$object, $getter]), sprintf('Expecting correct value for "%s".', $key));
        }

        return $object;
    }

    public function testIntegerId()
    {
        $id = 500;

        $object = new ErrorObject();
        $object->setId($id);

        $this->assertSame($id, $object->getId());
    }

    public function testLinksObject()
    {
        $links = $this->getMock(LinkInterface::class);
        $object = new ErrorObject();

        $object->setLinks($links);
        $this->assertSame($links, $object->getLinks());
    }

    public function testCastToString()
    {
        $stack = [
            'status' => 500,
            'code' => 1234,
            'title' => 5678,
            'detail' => 1111,
        ];

        $object = new ErrorObject();

        foreach ($stack as $key => $value) {

            $setter = 'set' . ucfirst($key);
            $getter = 'get' . ucfirst($key);

            call_user_func([$object, $setter], $value);
            $this->assertSame((string) $value, call_user_func([$object, $getter]), sprintf('Expecting string value for "%s".', $key));
        }
    }

    public function testSource()
    {
        $object = new ErrorObject();
        $this->assertEquals(new SourceObject(), $object->source());
        return $object;
    }

    public function testSetSourceObject()
    {
        $object = new ErrorObject();
        $expected = new SourceObject();
        $this->assertSame($object, $object->setSource($expected));
        $this->assertSame($expected, $object->getSource());
    }

    public function testSetSourceArray()
    {
        $expected = new SourceObject();
        $expected->setPointer('/foo/bar');
        $object = new ErrorObject();

        $object->setSource($expected->toArray());
        $this->assertEquals($expected, $object->getSource());

        return $object;
    }

    public function testSetInvalidSource()
    {
        $this->setExpectedException(InvalidArgumentException::class);

        (new ErrorObject())->setSource('foo');
    }

    /**
     * @depends testSetSourceArray
     */
    public function testSetSourceNull(ErrorObject $object)
    {
        $this->assertTrue($object->hasSource());
        $object->setSource(null);
        $this->assertNull($object->getSource());
        $this->assertFalse($object->hasSource());
    }

    public function testInvalidMeta()
    {
        $this->setExpectedException(InvalidArgumentException::class);

        (new ErrorObject())->setMeta('invalid');
    }

    /**
     * @depends testGettersSetters
     */
    public function testArrayExchangeable(ErrorObject $object)
    {
        $source = new SourceObject();
        $source->setPointer('/foo/bar');
        $object->setSource($source);

        $arr = [
            ErrorObject::ID => static::ID,
            ErrorObject::STATUS => static::STATUS,
            ErrorObject::CODE => static::CODE,
            ErrorObject::TITLE => static::TITLE,
            ErrorObject::DETAIL => static::DETAIL,
            ErrorObject::LINKS => $this->links,
            ErrorObject::META => $this->meta,
            ErrorObject::SOURCE => $source,
        ];

        $this->assertEquals($arr, $object->toArray());

        $check = new ErrorObject();
        $this->assertSame($check, $check->exchangeArray($arr));
        $this->assertEquals($object, $check);

        return $object;
    }

    /**
     * @depends testArrayExchangeable
     */
    public function testCreate(ErrorObject $expected)
    {
        $actual = ErrorObject::create($expected->toArray());
        $this->assertEquals($expected, $actual);
    }

    /**
     * @depends testArrayExchangeable
     */
    public function testConstruct(ErrorObject $expected)
    {
        $actual = new ErrorObject($expected->toArray());
        $this->assertEquals($expected, $actual);
    }

    /**
     * @depends testArrayExchangeable
     */
    public function testClone(ErrorObject $object)
    {
        $expected = $object->source()->setPointer('/foo');
        $clone = clone $object;
        $actual = $clone->source();

        $this->assertEquals($expected, $actual);
        $this->assertNotSame($expected, $actual);
    }
}
