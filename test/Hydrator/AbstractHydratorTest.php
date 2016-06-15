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

namespace CloudCreativity\JsonApi\Hydrator;

use CloudCreativity\JsonApi\Exceptions\HydratorException;
use CloudCreativity\JsonApi\TestCase;
use stdClass;

/**
 * Class AbstractHydratorTest
 * @package CloudCreativity\JsonApi
 */
final class AbstractHydratorTest extends TestCase
{

    /**
     * @var TestHydrator
     */
    private $hydrator;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->hydrator = new TestHydrator();
    }

    public function testHydration()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "attributes": {
            "title": "My First Post",
            "content": "Here is some content..."
        },
        "relationships": {
            "user": {
                "data": {
                    "type": "users",
                    "id": "123"
                }
            },
            "tags": {
                "data": [
                    {
                        "type": "tags",
                        "id": "456"
                    },
                    {
                        "type": "tags",
                        "id": "789"
                    }
                ]
            },
            "ignored": {
                "data": {
                    "type": "ignored",
                    "id": "999"
                }
            }
        }
    }
}
JSON_API;

        $document = $this->decode($content);
        $record = new stdClass();

        $expected = new stdClass();
        $expected->title = 'My First Post';
        $expected->content = 'Here is some content...';
        $expected->user_id = "123";
        $expected->tag_ids = ["456", "789"];

        $this->hydrator->hydrate($document->resource(), $record);
        $this->assertEquals($expected, $record);
    }

    public function testHydrateRelationship()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "users",
        "id": "999"
    }
}
JSON_API;

        $record = new stdClass();
        $record->user_id = "123";

        $document = $this->decode($content);
        $this->hydrator->hydrateRelationship('user', $document->relationship(), $record);
        $this->assertEquals("999", $record->user_id);
    }

    public function testHydrateRelationshipNotRecognised()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "users",
        "id": "999"
    }
}
JSON_API;

        $document = $this->decode($content);
        $this->setExpectedException(HydratorException::class);
        $this->hydrator->hydrateRelationship('foo', $document->relationship(), new stdClass());
    }
}
