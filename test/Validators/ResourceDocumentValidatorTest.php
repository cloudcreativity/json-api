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

namespace CloudCreativity\JsonApi\Validators;

use CloudCreativity\JsonApi\Contracts\Object\ResourceInterface;
use CloudCreativity\JsonApi\Contracts\Validators\AttributesValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\DocumentValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\RelationshipsValidatorInterface;
use CloudCreativity\JsonApi\Document\Error;
use CloudCreativity\JsonApi\Validators\ValidatorErrorFactory as Keys;
use Neomerx\JsonApi\Contracts\Document\DocumentInterface;
use Neomerx\JsonApi\Exceptions\ErrorCollection;
use stdClass;

/**
 * Class ResourceDocumentValidatorTest
 * @package CloudCreativity\JsonApi
 */
final class ResourceDocumentValidatorTest extends TestCase
{

    /**
     * Test a valid create resource document.
     */
    public function testCreate()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "attributes": {
            "title": "My First Blog",
            "content": "This is my first blog post..."
        },
        "relationships": {
            "author": {
                "data": {
                    "type": "people",
                    "id": "99"
                }
            },
            "tags": {
                "data": [
                    {
                        "type": "tags",
                        "id": "1"
                    },
                    {
                        "type": "tags",
                        "id": "2"
                    }
                ]
            }
        }
    }
}
JSON_API;

        $relationships = $this
            ->relationships()
            ->hasOne('author', 'people', true)
            ->hasMany('tags', null, false);

        $document = $this->decode($content);
        $validator = $this->validator(null, null, $relationships);

        $this->assertTrue($validator->isValid($document));
    }

    /**
     * Test a valid resource update document.
     */
    public function testUpdate()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "id": "1",
        "attributes": {
            "title": "My First Blog",
            "content": "This is my first blog post..."
        },
        "relationships": {
            "author": {
                "data": {
                    "type": "people",
                    "id": "99"
                }
            },
            "tags": {
                "data": [
                    {
                        "type": "tag",
                        "id": "1"
                    },
                    {
                        "type": "tag",
                        "id": "2"
                    }
                ]
            }
        }
    }
}
JSON_API;

        $document = $this->decode($content);
        $validator = $this->validator('1');

        $this->willExist()->assertTrue($validator->isValid($document));
    }

    public function testDataRequired()
    {
        $content = '{}';
        $document = $this->decode($content);
        $validator = $this->validator();

        $this->assertFalse($validator->isValid($document));
        $this->assertErrorAt($validator->getErrors(), '/', Keys::MEMBER_REQUIRED);
        $this->assertDetailContains($validator->getErrors(), '/', DocumentInterface::KEYWORD_DATA);
    }

    public function testDataNotObject()
    {
        $content = <<<JSON_API
{
    "data": "foo"
}
JSON_API;

        $document = $this->decode($content);
        $validator = $this->validator();

        $this->assertFalse($validator->isValid($document));
        $this->assertErrorAt($validator->getErrors(), '/data', Keys::MEMBER_OBJECT_EXPECTED);
        $this->assertDetailContains($validator->getErrors(), '/data', DocumentInterface::KEYWORD_DATA);
    }

    public function testDataTypeRequired()
    {
        $content = <<<JSON_API
{
    "data": {
        "attributes": {
            "title": "My First Blog"
        }
    }
}
JSON_API;

        $document = $this->decode($content);
        $validator = $this->validator();

        $this->assertFalse($validator->isValid($document));
        $this->assertErrorAt($validator->getErrors(), '/data', Keys::MEMBER_REQUIRED);
        $this->assertDetailContains($validator->getErrors(), '/data', DocumentInterface::KEYWORD_TYPE);
    }

    public function testDataTypeNotSupported()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "people",
        "attributes": {
            "name": "John Doe"
        }
    }
}
JSON_API;

        $document = $this->decode($content);
        $validator = $this->validator();

        $this->assertFalse($validator->isValid($document));
        $this->assertErrorAt(
            $validator->getErrors(),
            '/data/type',
            Keys::RESOURCE_UNSUPPORTED_TYPE,
            Keys::STATUS_UNSUPPORTED_TYPE
        );
        $this->assertDetailContains($validator->getErrors(), '/data/type', 'people');
        $this->assertDetailContains($validator->getErrors(), '/data/type', 'posts');
    }

    public function testDataIdRequired()
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

        $document = $this->decode($content);
        $validator = $this->validator('1');

        $this->assertFalse($validator->isValid($document));
        $this->assertErrorAt($validator->getErrors(), '/data', Keys::MEMBER_REQUIRED);
        $this->assertDetailContains($validator->getErrors(), '/data', DocumentInterface::KEYWORD_ID);
    }

    public function testDataIdNotSupported()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "id": "2",
        "attributes": {
            "title": "My First Post"
        }
    }
}
JSON_API;

        $document = $this->decode($content);
        $validator = $this->validator('1');

        $this->assertFalse($validator->isValid($document));
        $this->assertErrorAt(
            $validator->getErrors(),
            '/data/id',
            Keys::RESOURCE_UNSUPPORTED_ID,
            Keys::STATUS_UNSUPPORTED_ID
        );
        $this->assertDetailContains($validator->getErrors(), '/data/id', '2');
        $this->assertDetailContains($validator->getErrors(), '/data/id', '1');
    }

    public function testDataAttributesNotObject()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "id": "1",
        "attributes": []
    }
}
JSON_API;

        $document = $this->decode($content);
        $validator = $this->validator('1');

        $this->assertFalse($validator->isValid($document));
        $this->assertErrorAt($validator->getErrors(), '/data/attributes', Keys::MEMBER_OBJECT_EXPECTED);
        $this->assertDetailContains($validator->getErrors(), '/data/attributes', DocumentInterface::KEYWORD_ATTRIBUTES);
    }

    public function testDataAttributesInvalid()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "id": "1",
        "attributes": {
            "title": "My First Post"
        }
    }
}
JSON_API;

        $document = $this->decode($content);
        $validator = $this->validator('1', $this->attributes(false));

        $this->assertFalse($validator->isValid($document));
        $this->assertErrorAt($validator->getErrors(), '/data/attributes', Keys::RESOURCE_INVALID_ATTRIBUTES);
    }

    public function testDataRelationshipsNotObject()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "id": "1",
        "attributes": {
            "title": "My First Post"
        },
        "relationships": []
    }
}
JSON_API;

        $document = $this->decode($content);
        $validator = $this->validator('1');

        $this->assertFalse($validator->isValid($document));
        $this->assertErrorAt($validator->getErrors(), '/data/relationships', Keys::MEMBER_OBJECT_EXPECTED);
        $this->assertDetailContains($validator->getErrors(), '/data/relationships', DocumentInterface::KEYWORD_RELATIONSHIPS);
    }

    public function testDataRelationshipNotObject()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "attributes": {
            "title": "My first post"
        },
        "relationships": {
            "user": "foo"
        }
    }
}
JSON_API;

        $document = $this->decode($content);
        $validator = $this->validator();

        $this->assertFalse($validator->isValid($document));
        $this->assertErrorAt($validator->getErrors(), '/data/relationships/user', Keys::MEMBER_OBJECT_EXPECTED);
    }

    public function testDataRelationshipNoData()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "attributes": {
            "title": "My first post"
        },
        "relationships": {
            "user": {}
        }
    }
}
JSON_API;

        $document = $this->decode($content);
        $validator = $this->validator();

        $this->assertFalse($validator->isValid($document));
        $this->assertErrorAt($validator->getErrors(), '/data/relationships/user', Keys::MEMBER_REQUIRED);
        $this->assertDetailContains($validator->getErrors(), '/data/relationships/user', DocumentInterface::KEYWORD_DATA);
    }

    public function testDataRelationshipInvalidData()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "attributes": {
            "title": "My first post"
        },
        "relationships": {
            "user": {
                "data": false
            }
        }
    }
}
JSON_API;

        $document = $this->decode($content);
        $validator = $this->validator();

        $this->assertFalse($validator->isValid($document));
        $this->assertErrorAt($validator->getErrors(), '/data/relationships/user', Keys::MEMBER_RELATIONSHIP_EXPECTED);
        $this->assertDetailContains($validator->getErrors(), '/data/relationships/user', DocumentInterface::KEYWORD_DATA);
    }

    public function testDataNonExistingRelationship()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "attributes": {
            "title": "My first post"
        },
        "relationships": {
            "user": {
                "data": {
                    "type": "users",
                    "id": "1"
                }
            }
        }
    }
}
JSON_API;

        $document = $this->decode($content);
        $validator = $this->validator();

        $this->willNotExist();
        $this->assertFalse($validator->isValid($document));
        $this->assertErrorAt($validator->getErrors(), '/data/relationships/user', Keys::RELATIONSHIP_DOES_NOT_EXIST);
    }

    public function testDataRelationshipsHasOneRequired()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "attributes": {
            "title": "My first post"
        },
        "relationships": {
            "tags": {
                "data": []
            }
        }
    }
}
JSON_API;

        $document = $this->decode($content);
        $relationships = $this->relationships()->hasOne('user', 'users', true);
        $validator = $this->validator(null, null, $relationships);

        $this->assertFalse($validator->isValid($document));
        $this->assertErrorAt($validator->getErrors(), '/data/relationships', Keys::MEMBER_REQUIRED);
    }

    public function testDataRelationshipsHasManyRequired()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "attributes": {
            "title": "My first post"
        },
        "relationships": {
            "user": {
                "data": {
                    "type": "users",
                    "id": "1"
                }
            }
        }
    }
}
JSON_API;

        $document = $this->decode($content);
        $relationships = $this->relationships()->hasMany('tags', 'tags', true);
        $validator = $this->validator(null, null, $relationships);

        $this->assertFalse($validator->isValid($document));
        $this->assertErrorAt($validator->getErrors(), '/data/relationships', Keys::MEMBER_REQUIRED);
    }

    public function testContextValid()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "id": "123",
        "attributes": {
            "title": "My first post"
        },
        "relationships": {
            "user": {
                "data": {
                    "type": "users",
                    "id": "1"
                }
            }
        }
    }
}
JSON_API;

        $called = false;
        $record = new stdClass();

        $context = function (ResourceInterface $resource, $obj) use (&$called, $record) {
            $this->assertSame($record, $obj);
            $this->assertEquals('posts', $resource->getType());
            $this->assertEquals('123', $resource->getId());
            $this->assertEquals('My first post', $resource
                ->getAttributes()
                ->get('title')
            );
            $this->assertEquals('users', $resource
                ->getRelationships()
                ->getRelationship('user')
                ->getData()
                ->getType()
            );
            $called = true;
            return true;
        };

        $document = $this->decode($content);
        $validator = $this->validator("123", null, null, $context);

        $this->willExist()->assertTrue($validator->isValid($document, $record));

        if (!$called) {
            $this->fail('Context validator was not called.');
        }
    }

    public function testContextInvalid()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "attributes": {
            "title": "My first post"
        }
    }
}
JSON_API;

        $expected = Error::create([
            Error::TITLE => 'Context',
            Error::DETAIL => 'Context is invalid',
        ]);
        $expected->setSourcePointer('/data/foo');

        $context = function ($resource, $record, TestContextValidator $validator) use ($expected) {
            $validator->getErrors()->add($expected);
            return false;
        };

        $document = $this->decode($content);
        $validator = $this->validator(null, null, null, $context);

        $this->assertFalse($validator->isValid($document));
        $this->assertEquals($expected, $this->findErrorAt($validator->getErrors(), '/data/foo'));
    }

    /**
     * The context validator should not be called if any other part of the
     * resource is invalid.
     */
    public function testContextNotCalledIfInvalid()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "id": "99"
    }
}
JSON_API;

        $document = $this->decode($content);
        $validator = $this->validator('123', null, null, function () {
            $this->fail('Context validator should not be called.');
        });

        $this->assertFalse($validator->isValid($document));
    }

    /**
     * @param bool $exists
     * @return $this
     */
    private function willExist($exists = true)
    {
        $this->store->method('exists')->willReturn($exists);
        return $this;
    }

    /**
     * @return $this
     */
    private function willNotExist()
    {
        return $this->willExist(false);
    }

    /**
     * @param $id
     * @param AttributesValidatorInterface|null $attributes
     * @param RelationshipsValidatorInterface|null $relationships
     * @param callable|null $context
     * @return DocumentValidatorInterface
     */
    private function validator(
        $id = null,
        AttributesValidatorInterface $attributes = null,
        RelationshipsValidatorInterface $relationships = null,
        callable $context = null
    ) {
        $context = $context ? new TestContextValidator($context) : null;

        $resource = $this->factory->resource('posts', $id, $attributes, $relationships, $context);
        $validator = $this->factory->resourceDocument($resource);

        return $validator;
    }

    /**
     * @param $valid
     * @return AttributesValidatorInterface
     */
    private function attributes($valid)
    {
        $mock = $this->getMock(AttributesValidatorInterface::class);
        $mock->method('isValid')->willReturn($valid);
        $mock->method('errors')->willReturn(new ErrorCollection());

        return $mock;
    }

    /**
     * @param bool $exists
     * @return RelationshipsValidatorInterface
     */
    private function relationships($exists = true)
    {
        $this->willExist($exists);
        return $this->factory->relationships();
    }
}
