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

namespace CloudCreativity\JsonApi\Object;

use CloudCreativity\JsonApi\Contracts\Object\ResourceCollectionInterface;
use CloudCreativity\JsonApi\Exceptions\RuntimeException;
use CloudCreativity\JsonApi\Object\Resource as ResourceObject;
use CloudCreativity\JsonApi\TestCase;
use stdClass;

/**
 * Class ResourceCollectionTest
 *
 * @package CloudCreativity\JsonApi
 */
final class ResourceCollectionTest extends TestCase
{

    public function testCreate()
    {
        $document = <<<JSON_API
{
    "data": [
        {
            "type": "posts",
            "id": "123",
            "attributes": {
                "title": "My First Post"
            }
        },
        {
            "type": "posts",
            "id": "456",
            "attributes": {
                "title": "My Last Post"
            }
        }
    ]
}
JSON_API;

        $document = new Document(json_decode($document));
        $resources = $document->getResources();

        $this->assertInstanceOf(ResourceCollectionInterface::class, $resources);

        return $resources;
    }

    /**
     * @param ResourceCollection $resources
     * @depends testCreate
     */
    public function testHas(ResourceCollection $resources)
    {
        $this->assertTrue($resources->has(ResourceIdentifier::create('posts', '456')));
        $this->assertFalse($resources->has(ResourceIdentifier::create('comments', '456')));
    }

    /**
     * @param ResourceCollection $resources
     * @depends testCreate
     */
    public function testGet(ResourceCollection $resources)
    {
        $this->assertEquals($this->resourceA(), $resources->get(ResourceIdentifier::create('posts', '123')));
    }

    /**
     * @param ResourceCollection $resources
     * @depends testCreate
     */
    public function testGetMissingResource(ResourceCollection $resources)
    {
        $this->setExpectedException(RuntimeException::class);
        $resources->get(ResourceIdentifier::create('posts', '999'));
    }

    /**
     * @param ResourceCollection $resources
     * @depends testCreate
     */
    public function testAllAndIterator(ResourceCollection $resources)
    {
        $expected = [$this->resourceA(), $this->resourceB()];
        $this->assertEquals($expected, $resources->getAll());
        $this->assertEquals($expected, iterator_to_array($resources));
    }

    /**
     * @param ResourceCollection $resources
     * @depends testCreate
     */
    public function testCount(ResourceCollection $resources)
    {
        $this->assertEquals(2, count($resources));
    }

    /**
     * @param ResourceCollection $resources
     * @depends testCreate
     */
    public function testIsEmpty(ResourceCollection $resources)
    {
        $this->assertFalse($resources->isEmpty());
        $this->assertTrue((new ResourceCollection())->isEmpty());
    }

    /**
     * @param ResourceCollection $resources
     * @depends testCreate
     */
    public function testGetIds(ResourceCollection $resources)
    {
        $expected = [$this->resourceA()->getIdentifier(), $this->resourceB()->getIdentifier()];

        $this->assertEquals(new ResourceIdentifierCollection($expected), $resources->getIdentifiers());
    }

    /**
     * @return ResourceObject
     */
    private function resourceA()
    {
        $resource = new stdClass();
        $resource->type = 'posts';
        $resource->id = '123';
        $resource->attributes = new stdClass();
        $resource->attributes->title = 'My First Post';

        return new ResourceObject($resource);
    }

    /**
     * @return ResourceObject
     */
    private function resourceB()
    {
        $resource = new stdClass();
        $resource->type = 'posts';
        $resource->id = '456';
        $resource->attributes = new stdClass();
        $resource->attributes->title = 'My Last Post';

        return new ResourceObject($resource);
    }
}
