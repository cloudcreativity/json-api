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
use CloudCreativity\JsonApi\Exceptions\StoreException;
use CloudCreativity\JsonApi\Object\ResourceIdentifier;
use CloudCreativity\JsonApi\Object\StandardObject;
use CloudCreativity\JsonApi\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

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

        $this->setExpectedException(StoreException::class);
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
     * @param array $adapters
     * @return Store
     */
    private function store(array $adapters)
    {
        $store = new Store();

        foreach ($adapters as $adapter) {
            $store->register($adapter);
        }

        return $store;
    }

    /**
     * @param ResourceIdentifier $identifier
     * @param bool $exists
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function willExist(ResourceIdentifier $identifier, $exists = true)
    {
        $mock = $this->adapter($identifier->type());
        $mock->method('exists')->with($identifier)->willReturn($exists);

        return $mock;
    }

    /**
     * @param ResourceIdentifier $identifier
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function willNotExist(ResourceIdentifier $identifier)
    {
        return $this->willExist($identifier, false);
    }

    /**
     * @param ResourceIdentifier $identifier
     * @param $object
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function willFind(ResourceIdentifier $identifier, $object)
    {
        $mock = $this->willExist($identifier);
        $mock->method('find')->with($identifier)->willReturn($object);

        return $mock;
    }

    /**
     * @param ResourceIdentifier $identifier
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function willNotFind(ResourceIdentifier $identifier)
    {
        $mock = $this->willExist($identifier);
        $mock->method('find')->with($identifier)->willReturn(null);

        return $mock;
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
