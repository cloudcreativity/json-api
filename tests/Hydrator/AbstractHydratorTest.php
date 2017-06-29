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

namespace CloudCreativity\JsonApi\Hydrator;

use CloudCreativity\JsonApi\Exceptions\RuntimeException;
use CloudCreativity\JsonApi\TestCase;
use DateTime;
use DateTimeZone;
use stdClass;

/**
 * Class AbstractHydratorTest
 *
 * @package CloudCreativity\JsonApi
 */
class AbstractHydratorTest extends TestCase
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
            "latest-tags": {
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

        $expected = (object) [
            'title' => 'My First Post',
            'content' => 'Here is some content...',
            'user_id' => '123',
            'tag_ids' => ['456', '789'],
        ];

        $this->hydrator->hydrate($document->getResource(), $record);
        $this->assertEquals($expected, $record);
    }

    public function testAttributeAliases()
    {
        $this->hydrator->attributes = [
            'title',
            'content',
            'published' => 'is_published',
        ];

        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "attributes": {
            "title": "My First Post",
            "content": "Here is some content...",
            "published": true
        }
    }
}
JSON_API;

        $document = $this->decode($content);
        $record = new stdClass();

        $expected = (object) [
            'title' => 'My First Post',
            'content' => 'Here is some content...',
            'is_published' => true,
        ];

        $this->hydrator->hydrate($document->getResource(), $record);
        $this->assertEquals($expected, $record);
    }

    public function testIgnoresAttributes()
    {
        $this->hydrator->attributes = ['title', 'content'];

        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "attributes": {
            "title": "My First Post",
            "content": "Here is some content...",
            "published": true
        }
    }
}
JSON_API;

        $document = $this->decode($content);
        $record = new stdClass();

        $this->hydrator->hydrate($document->getResource(), $record);
        $this->assertObjectNotHasAttribute('published', $record);
    }

    /**
     * Test for date conversion
     *
     * - Dates to be specified using the `dates` attribute on the hydrator
     * - Should cast W3C date strings, including timezone.
     * - As Javascript will include milliseconds, these need to work too.
     * - Empty (`null`) values should be respected.
     */
    public function testConvertsDates()
    {
        $this->hydrator->attributes = [
            'exact',
            'published-at' => 'published_at',
            'empty',
        ];

        $this->hydrator->dates = ['exact', 'empty', 'published-at'];

        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "attributes": {
            "title": "My First Post",
            "content": "Here is some content...",
            "published-at": "2017-07-01T12:30:00+01:00",
            "exact": "2017-07-10T13:00:00.150+10:00",
            "empty": null
        }
    }
}
JSON_API;

        $published = new DateTime('2017-07-01 12:30:00', new DateTimeZone('Europe/London'));
        $exact = new DateTime('2017-07-10 13:00:00.150', new DateTimeZone('Australia/Melbourne'));
        $document = $this->decode($content);

        $this->hydrator->hydrate($document->getResource(), $record = new stdClass());
        $this->assertEquals($published, $record->published_at);
        $this->assertEquals($exact, $record->exact);
        $this->assertObjectHasAttribute('empty', $record);
        $this->assertNull($record->empty);
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
        $this->hydrator->hydrateRelationship('user', $document->getRelationship(), $record);
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
        $this->setExpectedException(RuntimeException::class);
        $this->hydrator->hydrateRelationship('foo', $document->getRelationship(), new stdClass());
    }

    /**
     * Test related (second step) hydration.
     *
     * - E.g. a nested record within the `author` attribute.
     * - E.g. a has-many relationship.
     */
    public function testHydrateRelated()
    {
        $this->hydrator->attributes = ['title', 'content'];

        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "attributes": {
            "title": "My First Post",
            "content": "Here is some content...",
            "author": {
                "first-name": "John",
                "surname": "Doe"
            }
        },
        "relationships": {
            "user": {
                "data": {
                    "type": "users",
                    "id": "123"
                }
            },
            "linked-posts": {
                "data": [
                    { "type": "posts", "id": "98" },
                    { "type": "posts", "id": "99" }
                ]
            }
        }
    }
}
JSON_API;

        $document = $this->decode($content);
        $author = (object) ['first_name' => "John", "surname" => "Doe"];
        $post1 = (object) ['type' => 'posts', 'id' => '98', 'title' => 'Post 98'];
        $post2 = (object) ['type' => 'posts', 'id' => '99', 'title' => 'Post 99'];
        $record = new stdClass();

        $this->assertEquals([
            $author,
            $post1,
            $post2
        ], $this->hydrator->hydrateRelated($document->getResource(), $record));
    }
}
