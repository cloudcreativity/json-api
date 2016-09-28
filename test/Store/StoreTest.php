<?php

/**
 * Copyright 2016 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Store;

use CloudCreativity\JsonApi\Contracts\Store\AdapterInterface;
use CloudCreativity\JsonApi\Exceptions\RecordNotFoundException;
use CloudCreativity\JsonApi\Exceptions\RuntimeException;
use CloudCreativity\JsonApi\Object\ResourceIdentifier;
use CloudCreativity\JsonApi\Object\StandardObject;
use CloudCreativity\JsonApi\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class StoreTest
 * @package CloudCreativity\JsonApi
 */
final class StoreTest extends TestCase
{

    public function testExists()
    {
        $identifier = ResourceIdentifier::create('users', '99');

        $store = $this->store([
            $this->adapter(),
            $this->willExist($identifier)
        ]);

        $this->assertTrue($store->exists($identifier));
    }

    public function testDoesNotExist()
    {
        $identifier = ResourceIdentifier::create('users', '99');

        $store = $this->store([
            $this->adapter(),
            $this->willNotExist($identifier)
        ]);

        $this->assertFalse($store->exists($identifier));
    }

    public function testCannotDetermineExistence()
    {
        /** @var AdapterInterface $adapter */
        $adapter = $this->adapter();
        $identifier = ResourceIdentifier::create('users', '99');
        $store = $this->store([$adapter]);

        $this->setExpectedException(RuntimeException::class);
        $store->exists($identifier);
    }

    public function testFind()
    {
        $identifier = ResourceIdentifier::create('users', '99');
        $expected = new StandardObject();

        $store = $this->store([
            $this->adapter(),
            $this->willFind($identifier, $expected)
        ]);

        $this->assertSame($expected, $store->find($identifier));
        $this->assertSame($expected, $store->findRecord($identifier));
    }

    public function testCannotFind()
    {
        $identifier = ResourceIdentifier::create('users', '99');

        $store = $this->store([
            $this->adapter(),
            $this->willNotFind($identifier)
        ]);

        $this->assertNull($store->find($identifier));
        $this->setExpectedException(RecordNotFoundException::class, 'users:99');
        $store->findRecord($identifier);
    }

    /**
     * If exists is called multiple times, we expect the adapter to only be queried once.
     */
    public function testExistsCalledOnce()
    {
        $identifier = ResourceIdentifier::create('users', '99');

        $store = $this->store([
            $this->adapter(),
            $this->willExist($identifier, true, $this->once())
        ]);

        $this->assertTrue($store->exists($identifier));
        $this->assertTrue($store->exists($identifier));
    }

    /**
     * If find is called multiple times, we expected the adapter to only be queried once.
     */
    public function testFindCalledOnce()
    {
        $identifier = ResourceIdentifier::create('users', '99');
        $expected = new StandardObject();

        $store = $this->store([
            $this->adapter(),
            $this->willFind($identifier, $expected, $this->once()),
        ]);

        $this->assertSame($expected, $store->find($identifier));
        $this->assertSame($expected, $store->find($identifier));
    }

    /**
     * If find returns the objects, then exists is called, the adapter does not need to be queried
     * because the store already knows that it exists.
     */
    public function testFindBeforeExists()
    {
        $identifier = ResourceIdentifier::create('users', '99');
        $expected = new StandardObject();

        $mock = $this->adapter($identifier->getType());
        $mock->expects($this->never())->method('exists');
        $mock->method('find')->with($identifier)->willReturn($expected);

        $store = $this->store([$mock]);
        $this->assertSame($expected, $store->find($identifier));
        $this->assertTrue($store->exists($identifier));
    }

    /**
     * If find does not return the object, then exists is called, the adapter does not need to be
     * queried because the store already knows that it does not exist.
     */
    public function testFindNoneBeforeExists()
    {
        $identifier = ResourceIdentifier::create('users', '99');

        $mock = $this->adapter($identifier->getType());
        $mock->expects($this->never())->method('exists');

        $store = $this->store([$mock]);
        $this->assertNull($store->find($identifier));
        $this->assertFalse($store->exists($identifier));
    }

    /**
     * If exists returns false and then find is called, null should be returned without the adapter
     * being queried because the store already knows it does not exist.
     */
    public function testDoesNotExistBeforeFind()
    {
        $identifier = ResourceIdentifier::create('users', '99');

        $mock = $this->adapter($identifier->getType());
        $mock->expects($this->once())->method('exists')->with($identifier)->willReturn(false);
        $mock->expects($this->never())->method('find')->with($identifier);

        $store = $this->store([$mock]);
        $this->assertFalse($store->exists($identifier));
        $this->assertNull($store->find($identifier));
    }

    /**
     * @param array $adapters
     * @return Store
     */
    private function store(array $adapters)
    {
        $store = new Store();
        $store->registerMany($adapters);

        return $store;
    }

    /**
     * @param ResourceIdentifier $identifier
     * @param bool $exists
     * @param $expectation
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function willExist(ResourceIdentifier $identifier, $exists = true, $expectation = null)
    {
        $expectation = $expectation ?: $this->any();

        $mock = $this->adapter($identifier->getType());
        $mock->expects($expectation)
            ->method('exists')
            ->with($identifier)
            ->willReturn($exists);

        return $mock;
    }

    /**
     * @param ResourceIdentifier $identifier
     * @param $expectation
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function willNotExist(ResourceIdentifier $identifier, $expectation = null)
    {
        return $this->willExist($identifier, false, $expectation);
    }

    /**
     * @param ResourceIdentifier $identifier
     * @param $object
     * @param $expectation
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function willFind(ResourceIdentifier $identifier, $object, $expectation = null)
    {
        $expectation = $expectation ?: $this->any();

        $mock = $this->adapter($identifier->getType());
        $mock->expects($expectation)
            ->method('find')
            ->with($identifier)
            ->willReturn($object);

        return $mock;
    }

    /**
     * @param ResourceIdentifier $identifier
     * @param $expectation
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function willNotFind(ResourceIdentifier $identifier, $expectation = null)
    {
        return $this->willFind($identifier, null, $expectation);
    }

    /**
     * @param array|null $types
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function adapter($types = null)
    {
        if (is_string($types)) {
            $types = [[$types, true]];
        } elseif (!is_array($types)) {
            $types = [['posts', true]];
        }

        $mock = $this->getMockBuilder(AdapterInterface::class)->getMock();
        $mock->method('recognises')->willReturnMap($types);

        return $mock;
    }
}
