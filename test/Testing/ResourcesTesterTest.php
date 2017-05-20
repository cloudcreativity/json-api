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

namespace CloudCreativity\JsonApi\Testing;

/**
 * Class ResourcesTesterTest
 *
 * @package CloudCreativity\JsonApi
 */
class ResourcesTesterTest extends TestCase
{

    public function testTypes()
    {
        $content = <<<JSON_API
{
    "data": [
        {
            "type": "posts",
            "id": "1",
            "attributes": {
                "type": "My first post"
            }
        },
        {
            "type": "posts",
            "id": "2",
            "attributes": {
                "type": "My second post"
            }
        }
    ]
}
JSON_API;

        $document = DocumentTester::create($content);

        $collection = $document
            ->assertResourceCollection()
            ->assertTypes('posts');

        $this->willFail(function () use ($collection) {
            $collection->assertTypes('comments');
        });

        return $collection;
    }

    /**
     * @param ResourcesTester $collection
     * @depends testTypes
     */
    public function testResource(ResourcesTester $collection)
    {
        $resource = $collection->assertContainsResource('posts', 1)->assertResource('posts', '2');
        $this->assertInstanceOf(ResourceTester::class, $resource);
        $this->assertTrue($resource->is('posts', '2'));

        $this->willFail(function () use ($collection) {
            $collection->assertResource('posts', '3');
        });

        $this->willFail(function () use ($collection) {
            $collection->assertContainsResource('comments', 1);
        });
    }

    /**
     * @param ResourcesTester $collection
     * @depends testTypes
     */
    public function testContainsOnly(ResourcesTester $collection)
    {
        $collection->assertContainsOnly([
            'posts' => ['2', '1'],
        ]);

        $this->willFail(function () use ($collection) {
            $collection->assertContainsOnly([
                'posts' => [1, '3'],
            ]);
        });

        $this->willFail(function () use ($collection) {
            $collection->assertContainsOnly(['posts' => '2']);
        });
    }

    public function testPolymorphicTypes()
    {
        $content = <<<JSON_API
{
    "data": [
        {
            "type": "posts",
            "id": "1",
            "attributes": {
                "type": "My first post"
            }
        },
        {
            "type": "posts",
            "id": "2",
            "attributes": {
                "type": "My second post"
            }
        },
        {
            "type": "comments",
            "id": "99",
            "attributes": {
                "content": "This is my comment"
            }
        }
    ]
}
JSON_API;

        $document = DocumentTester::create($content);

        $collection = $document
            ->assertResourceCollection()
            ->assertTypes(['posts', 'comments']);

        $this->willFail(function () use ($collection) {
            $collection->assertTypes(['posts', 'users']);
        });

        return $collection;
    }

    /**
     * @param ResourcesTester $collection
     * @depends testPolymorphicTypes
     */
    public function testPolymorphicContainsOnly(ResourcesTester $collection)
    {
        $collection->assertContainsOnly([
            'posts' => ['2', '1'],
            'comments' => '99',
        ]);

        $this->willFail(function () use ($collection) {
            $collection->assertContainsOnly([
                'posts' => [1, '2'],
                'comments' => '123',
            ]);
        });

        $this->willFail(function () use ($collection) {
            $collection->assertContainsOnly(['posts' => ['1', '2']]);
        });
    }
}
