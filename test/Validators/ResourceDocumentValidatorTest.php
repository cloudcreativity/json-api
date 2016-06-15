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

use CloudCreativity\JsonApi\Contracts\Validators\AttributesValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\DocumentValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\RelationshipsValidatorInterface;
use CloudCreativity\JsonApi\Validators\ValidatorErrorFactory as Keys;
use Neomerx\JsonApi\Contracts\Document\DocumentInterface;
use Neomerx\JsonApi\Exceptions\ErrorCollection;

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

        $this->assertTrue($validator->isValid($document));
    }

    public function testDataRequired()
    {
        $content = '{}';
        $document = $this->decode($content);
        $validator = $this->validator();

        $this->assertFalse($validator->isValid($document));
        $this->assertErrorAt($validator->errors(), '/', Keys::MEMBER_REQUIRED);
        $this->assertDetailContains($validator->errors(), '/', DocumentInterface::KEYWORD_DATA);
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
        $this->assertErrorAt($validator->errors(), '/data', Keys::MEMBER_OBJECT_EXPECTED);
        $this->assertDetailContains($validator->errors(), '/data', DocumentInterface::KEYWORD_DATA);
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
        $this->assertErrorAt($validator->errors(), '/data', Keys::MEMBER_REQUIRED);
        $this->assertDetailContains($validator->errors(), '/data', DocumentInterface::KEYWORD_TYPE);
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
            $validator->errors(),
            '/data/type',
            Keys::RESOURCE_UNSUPPORTED_TYPE,
            Keys::STATUS_UNSUPPORTED_TYPE
        );
        $this->assertDetailContains($validator->errors(), '/data/type', 'people');
        $this->assertDetailContains($validator->errors(), '/data/type', 'posts');
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
        $this->assertErrorAt($validator->errors(), '/data', Keys::MEMBER_REQUIRED);
        $this->assertDetailContains($validator->errors(), '/data', DocumentInterface::KEYWORD_ID);
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
            $validator->errors(),
            '/data/id',
            Keys::RESOURCE_UNSUPPORTED_ID,
            Keys::STATUS_UNSUPPORTED_ID
        );
        $this->assertDetailContains($validator->errors(), '/data/id', '2');
        $this->assertDetailContains($validator->errors(), '/data/id', '1');
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
        $this->assertErrorAt($validator->errors(), '/data/attributes', Keys::MEMBER_OBJECT_EXPECTED);
        $this->assertDetailContains($validator->errors(), '/data/attributes', DocumentInterface::KEYWORD_ATTRIBUTES);
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
        $this->assertErrorAt($validator->errors(), '/data/attributes', Keys::RESOURCE_INVALID_ATTRIBUTES);
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
        $this->assertErrorAt($validator->errors(), '/data/relationships', Keys::MEMBER_OBJECT_EXPECTED);
        $this->assertDetailContains($validator->errors(), '/data/relationships', DocumentInterface::KEYWORD_RELATIONSHIPS);
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
        $validator = $this->validator(null, null, $this->relationships());

        $this->assertFalse($validator->isValid($document));
        $this->assertErrorAt($validator->errors(), '/data/relationships/user', Keys::MEMBER_OBJECT_EXPECTED);
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
        $relationships = $this->relationships(false)->hasOne('user', 'users');
        $validator = $this->validator(null, null, $relationships);

        $this->assertFalse($validator->isValid($document));
        $this->assertErrorAt($validator->errors(), '/data/relationships/user', Keys::RELATIONSHIP_DOES_NOT_EXIST);
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
        $this->assertErrorAt($validator->errors(), '/data/relationships', Keys::MEMBER_REQUIRED);
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
        $this->assertErrorAt($validator->errors(), '/data/relationships', Keys::MEMBER_REQUIRED);
    }

    /**
     * @param $id
     * @param AttributesValidatorInterface|null $attributes
     * @param RelationshipsValidatorInterface|null $relationships
     * @return DocumentValidatorInterface
     */
    private function validator(
        $id = null,
        AttributesValidatorInterface $attributes = null,
        RelationshipsValidatorInterface $relationships = null
    ) {
        $resource = $this->factory->resource('posts', $id, $attributes, $relationships);
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
        $this->store->method('exists')->willReturn($exists);
        return $this->factory->relationships();
    }
}
