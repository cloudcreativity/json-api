<?php

/**
 * Copyright 2017 Cloud Creativity Limited
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
use CloudCreativity\JsonApi\Factories\Factory;
use CloudCreativity\JsonApi\Object\ResourceIdentifier;
use CloudCreativity\JsonApi\TestCase;
use CloudCreativity\Utils\Object\StandardObject;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class StoreTest
 *
 * @package CloudCreativity\JsonApi
 */
class StoreTest extends TestCase
{

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->factory = new Factory();
    }

    public function testQuery()
    {
        $params = $this->factory->createQueryParameters();
        $expected = $this->factory->createPage([]);

        $store = $this->store([
            'posts' => $this->willNotQuery(),
            'users' => $this->willQuery($params, $expected),
        ]);

        $this->assertSame($expected, $store->query('users', $params));
    }

    public function testCannotQuery()
    {
        $store = $this->store(['posts' => $this->willNotQuery()]);
        $this->expectException(RuntimeException::class);
        $store->query('users', $this->factory->createQueryParameters());
    }

    public function testExists()
    {
        $identifier = ResourceIdentifier::create('users', '99');

        $store = $this->store([
            'posts' => $this->adapter(),
            'users' => $this->willExist('99')
        ]);

        $this->assertTrue($store->isType('users'));
        $this->assertTrue($store->exists($identifier));
    }

    public function testDoesNotExist()
    {
        $identifier = ResourceIdentifier::create('users', '99');

        $store = $this->store([
            'posts' => $this->adapter(),
            'users' => $this->willNotExist('99')
        ]);

        $this->assertTrue($store->isType('users'));
        $this->assertFalse($store->exists($identifier));
    }

    public function testCannotDetermineExistence()
    {
        $identifier = ResourceIdentifier::create('users', '99');
        $store = $this->store(['posts' => $this->adapter()]);

        $this->assertFalse($store->isType('users'));
        $this->expectException(RuntimeException::class);
        $store->exists($identifier);
    }

    public function testFind()
    {
        $identifier = ResourceIdentifier::create('users', '99');
        $expected = new StandardObject();

        $store = $this->store([
            'posts' => $this->adapter(),
            'users' => $this->willFind('99', $expected)
        ]);

        $this->assertSame($expected, $store->find($identifier));
        $this->assertSame($expected, $store->findRecord($identifier));
    }

    public function testCannotFind()
    {
        $identifier = ResourceIdentifier::create('users', '99');

        $store = $this->store([
            'posts' => $this->adapter(),
            'users' => $this->willNotFind('99')
        ]);

        $this->assertNull($store->find($identifier));
        $this->expectException(RecordNotFoundException::class);
        $this->expectExceptionMessage('users:99');
        $store->findRecord($identifier);
    }

    /**
     * If exists is called multiple times, we expect the adapter to only be queried once.
     */
    public function testExistsCalledOnce()
    {
        $identifier = ResourceIdentifier::create('users', '99');

        $store = $this->store([
            'posts' => $this->adapter(),
            'users' => $this->willExist('99', true, $this->once())
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
            'posts' => $this->adapter(),
            'users' => $this->willFind('99', $expected, $this->once()),
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

        $mock = $this->adapter();
        $mock->expects($this->never())->method('exists');
        $mock->method('find')->with('99')->willReturn($expected);

        $store = $this->store(['users' => $mock]);
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

        $mock = $this->adapter();
        $mock->expects($this->never())->method('exists');

        $store = $this->store(['users' => $mock]);
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

        $mock = $this->adapter();
        $mock->expects($this->once())->method('exists')->with('99')->willReturn(false);
        $mock->expects($this->never())->method('find');

        $store = $this->store(['users' => $mock]);
        $this->assertFalse($store->exists($identifier));
        $this->assertNull($store->find($identifier));
    }

    /**
     * @param array $adapters
     * @return Store
     */
    private function store(array $adapters)
    {
        $container = $this->factory->createAdapterContainer($adapters);

        return $this->factory->createStore($container);
    }

    /**
     * @param string $resourceId
     * @param bool $exists
     * @param $expectation
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function willExist($resourceId, $exists = true, $expectation = null)
    {
        $expectation = $expectation ?: $this->any();

        $mock = $this->adapter();
        $mock->expects($expectation)
            ->method('exists')
            ->with($resourceId)
            ->willReturn($exists);

        return $mock;
    }

    /**
     * @param $resourceId
     * @param $expectation
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function willNotExist($resourceId, $expectation = null)
    {
        return $this->willExist($resourceId, false, $expectation);
    }

    /**
     * @param $resourceId
     * @param $object
     * @param $expectation
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function willFind($resourceId, $object, $expectation = null)
    {
        $mock = $this->adapter();
        $mock->expects($expectation ?: $this->any())
            ->method('find')
            ->with($resourceId)
            ->willReturn($object);

        return $mock;
    }

    /**
     * @param $resourceId
     * @param $expectation
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function willNotFind($resourceId, $expectation = null)
    {
        return $this->willFind($resourceId, null, $expectation);
    }

    /**
     * @param $params
     * @param $results
     * @param null $expectation
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function willQuery($params, $results, $expectation = null)
    {
        $mock = $this->adapter();
        $mock->expects($expectation ?: $this->any())
            ->method('query')
            ->with($params)
            ->willReturn($results);

        return $mock;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function willNotQuery()
    {
        $mock = $this->adapter();
        $mock->expects($this->never())
            ->method('query');

        return $mock;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function adapter()
    {
        return $this->getMockBuilder(AdapterInterface::class)->getMock();
    }
}
