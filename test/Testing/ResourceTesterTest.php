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
 * Class ResourceTesterTest
 *
 * @package CloudCreativity\JsonApi
 */
final class ResourceTesterTest extends TestCase
{

    public function testNoType()
    {
        $content = <<<JSON_API
{
    "data": {
        "id": "123",
        "attributes": {
            "title": "My First Post"
        }
    }
}
JSON_API;

        $document = DocumentTester::create($content);

        $this->willFail(function () use ($document) {
            $document->assertResource();
        });
    }

    public function testEmptyType()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "",
        "id": "123"
    }
}
JSON_API;

        $document = DocumentTester::create($content);

        $this->willFail(function () use ($document) {
            $document->assertResource();
        });
    }

    public function testNoId()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "attributes": {
            "title": "My First Post"
        }
    }
}
JSON_API;

        $document = DocumentTester::create($content);

        $this->willFail(function () use ($document) {
            $document->assertResource();
        });
    }

    public function testEmptyId()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "id": ""
    }
}
JSON_API;

        $document = DocumentTester::create($content);

        $this->willFail(function () use ($document) {
            $document->assertResource();
        });
    }

    /**
     * @return ResourceTester
     */
    public function testAttributes()
    {

        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "id": "123",
        "attributes": {
            "title": "My First Post",
            "tags": ["news", "misc"],
            "content": "This is my first post",
            "rank": 1
        }
    }
}
JSON_API;

        $resource = DocumentTester::create($content)->assertResource();

        $resource->assertAttribute('title', 'My First Post')
            ->assertAttribute('rank', '1')
            ->assertAttributeIs('rank', 1);

        $this->willFail(function () use ($resource) {
            $resource->assertAttribute('rank', 2);
        });

        $this->willFail(function () use ($resource) {
            $resource->assertAttributeIs('rank', '1');
        });

        return $resource;
    }

    /**
     * @param ResourceTester $resource
     * @depends testAttributes
     */
    public function testAttributesSubset(ResourceTester $resource)
    {
        $resource->assertAttributesSubset([
            'title' => 'My First Post',
            'content' => 'This is my first post',
        ]);

        $this->willFail(function () use ($resource) {
            $resource->assertAttributesSubset([
                'title' => 'My First Post',
                'tags' => ['news', 'other'],
            ]);
        });
    }

    public function testRelationshipsSubset()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "id": "123",
        "relationships": {
            "author": {
                "data": {
                    "type": "users",
                    "id": "123"
                }
            },
            "comments": {
                "data": [
                    {"type": "comments", "id": 1},
                    {"type": "comments", "id": 2}
                ]
            }
        }
    }
}
JSON_API;

        $resource = DocumentTester::create($content)->assertResource();

        $resource->assertRelationshipsSubset([
            'author' => ['data' => ['type' => 'users', 'id' => '123']],
            'comments' => [],
        ]);

        $this->willFail(function () use ($resource) {
            $resource->assertRelationshipsSubset([
                'author' => ['data' => ['id' => '456']]
            ]);
        });
    }
}
