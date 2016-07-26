<?php

namespace CloudCreativity\JsonApi\Testing;

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
