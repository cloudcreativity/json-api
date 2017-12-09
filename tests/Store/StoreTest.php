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
use CloudCreativity\JsonApi\Contracts\Store\ContainerInterface;
use CloudCreativity\JsonApi\Contracts\Store\RelationshipAdapterInterface;
use CloudCreativity\JsonApi\Exceptions\RecordNotFoundException;
use CloudCreativity\JsonApi\Exceptions\RuntimeException;
use CloudCreativity\JsonApi\Object\ResourceIdentifier;
use CloudCreativity\JsonApi\Object\ResourceIdentifierCollection;
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
     * @var ContainerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $container;

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->container = $this->createMock(ContainerInterface::class);
    }

    /**
     * A query request must be handed off to the adapter for the resource type
     * specified.
     */
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

    /**
     * If there is no adapter for the resource type, an exception must be thrown.
     */
    public function testCannotQuery()
    {
        $store = $this->store(['posts' => $this->willNotQuery()]);
        $this->expectException(RuntimeException::class);
        $store->query('users', $this->factory->createQueryParameters());
    }

    /**
     * A query record request must be handed off to the adapter for the resource type
     * specified in the identifier.
     */
    public function testQueryRecord()
    {
        $identifier = ResourceIdentifier::create('users', '1');
        $params = $this->factory->createQueryParameters();
        $expected = new StandardObject();

        $store = $this->store([
            'posts' => $this->willNotQuery(),
            'users' => $this->willQueryRecord('1', $params, $expected)
        ]);

        $this->assertSame($expected, $store->queryRecord($identifier, $params));
    }

    /**
     * If there is no adapter for the resource type, an exception must be thrown.
     */
    public function testCannotQueryRecord()
    {
        $store = $this->store(['posts' => $this->willNotQuery()]);
        $this->expectException(RuntimeException::class);
        $store->queryRecord(ResourceIdentifier::create('users', '1'), $this->factory->createQueryParameters());
    }

    /**
     * A query related request must be handed off to the relationship adapter provided by the
     * resource adapter.
     */
    public function testQueryRelated()
    {
        $parameters = $this->factory->createQueryParameters();
        $record = new StandardObject();
        $expected = $this->factory->createPage([]);

        $store = $this->store([
            'users' => $this->willNotQuery(),
            'posts' => $this->willQueryRelated($record, 'user', $parameters, $expected),
        ]);

        $this->assertSame($expected, $store->queryRelated('posts', $record, 'user', $parameters));
    }

    /**
     * A query relationship request must be handed off to the relationship adapter provided by
     * the resource adapter.
     */
    public function testQueryRelationship()
    {
        $parameters = $this->factory->createQueryParameters();
        $record = new StandardObject();
        $expected = $this->factory->createPage([]);

        $store = $this->store([
            'users' => $this->willNotQuery(),
            'posts' => $this->willQueryRelationship($record, 'user', $parameters, $expected),
        ]);

        $this->assertSame($expected, $store->queryRelationship('posts', $record, 'user', $parameters));
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
        $this->assertSame($expected, $store->findOrFail($identifier));
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
        $store->findOrFail($identifier);
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
     * A find many request hands the ids off to the adapter of each resource type,
     * and returns an empty array if no records are found.
     */
    public function testFindManyReturnsEmpty()
    {
        $identifiers = ResourceIdentifierCollection::create([
            (object) ['type' => 'posts', 'id' => '1'],
            (object) ['type' => 'users', 'id' => '99'],
            (object) ['type' => 'posts', 'id' => '3'],
        ]);

        $store = $this->store([
            'posts' => $this->willFindMany(['1', '3']),
            'users' => $this->willFindMany(['99']),
            'tags' => $this->willNotFindMany(),
        ]);

        $this->assertSame([], $store->findMany($identifiers));
    }

    /**
     * A find many request hands the ids off to the adapter of each resource type,
     * and returns an array containing all found records.
     */
    public function testFindMany()
    {
        $identifiers = ResourceIdentifierCollection::create([
            $post = (object) ['type' => 'posts', 'id' => '1'],
            (object) ['type' => 'posts', 'id' => '3'],
            $user = (object) ['type' => 'users', 'id' => '99'],
        ]);

        $store = $this->store([
            'posts' => $this->willFindMany(['1', '3'], [$post]),
            'users' => $this->willFindMany(['99'], [$user]),
            'tags' => $this->willNotFindMany(),
        ]);

        $this->assertSame([$post, $user], $store->findMany($identifiers));
    }

    /**
     * An exception is thrown if a resource type in the find many identifiers
     * is not recognised.
     */
    public function testCannotFindMany()
    {
        $identifiers = ResourceIdentifierCollection::create([
            $post = (object) ['type' => 'posts', 'id' => '1'],
            (object) ['type' => 'posts', 'id' => '3'],
            $user = (object) ['type' => 'users', 'id' => '99'],
        ]);

        $store = $this->store([
            'posts' => $this->willFindMany(['1', '3']),
        ]);

        $this->expectException(RuntimeException::class);
        $store->findMany($identifiers);
    }

    /**
     * @param array $adapters
     * @return Store
     */
    private function store(array $adapters)
    {
        $this->container
            ->method('getAdapterByResourceType')
            ->willReturnCallback(function ($resourceType) use ($adapters) {
                return isset($adapters[$resourceType]) ? $adapters[$resourceType] : null;
            });

        return new Store($this->container);
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
     * @param $record
     * @param $expectation
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function willFind($resourceId, $record, $expectation = null)
    {
        $mock = $this->adapter();
        $mock->expects($expectation ?: $this->any())
            ->method('find')
            ->with($resourceId)
            ->willReturn($record);

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
     * @param array $resourceIds
     * @param array $results
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function willFindMany(array $resourceIds, array $results = [])
    {
        $mock = $this->adapter();
        $mock->expects($this->atLeastOnce())
            ->method('findMany')
            ->with($resourceIds)
            ->willReturn($results);

        return $mock;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function willNotFindMany()
    {
        $mock = $this->adapter();
        $mock->expects($this->never())->method('findMany');

        return $mock;
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
     * @param $resourceId
     * @param $params
     * @param $record
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function willQueryRecord($resourceId, $params, $record)
    {
        $mock = $this->adapter();
        $mock->expects($this->atLeastOnce())
            ->method('queryRecord')
            ->with($resourceId, $params)
            ->willReturn($record);

        return $mock;
    }

    /**
     * @param $record
     * @param $relationshipName
     * @param $parameters
     * @param $expected
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function willQueryRelated($record, $relationshipName, $parameters, $expected)
    {
        $mock = $this->relationshipAdapter();
        $mock->expects($this->atLeastOnce())
            ->method('queryRelated')
            ->with($record, $relationshipName, $parameters)
            ->willReturn($expected);

        return $this->adapter([$relationshipName => $mock]);
    }

    /**
     * @param $record
     * @param $relationshipName
     * @param $parameters
     * @param $expected
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function willQueryRelationship($record, $relationshipName, $parameters, $expected)
    {
        $mock = $this->relationshipAdapter();
        $mock->expects($this->atLeastOnce())
            ->method('queryRelationship')
            ->with($record, $relationshipName, $parameters)
            ->willReturn($expected);

        return $this->adapter([$relationshipName => $mock]);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function willNotQuery()
    {
        $mock = $this->adapter();
        $mock->expects($this->never())->method('query');
        $mock->expects($this->never())->method('queryRecord');
        $mock->expects($this->never())->method('related');

        return $mock;
    }

    /**
     * @param array $relationships
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function adapter(array $relationships = [])
    {
        $mock = $this->createMock(AdapterInterface::class);

        $mock->method('related')->willReturnCallback(function ($name) use ($relationships) {
            return $relationships[$name];
        });

        return $mock;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function relationshipAdapter()
    {
        $mock = $this->createMock(RelationshipAdapterInterface::class);

        $mock->expects($this->once())
            ->method('withAdapters')
            ->with($this->container)
            ->willReturnSelf();

        return $mock;
    }
}
