<?php

namespace CloudCreativity\JsonApi\Validators;

use CloudCreativity\JsonApi\Contracts\Validators\AttributesValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\DocumentValidatorInterface;
use CloudCreativity\JsonApi\Validators\ValidationKeys as Keys;
use Neomerx\JsonApi\Contracts\Document\DocumentInterface;
use Neomerx\JsonApi\Exceptions\ErrorCollection;

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
        $validator = $this->validator();

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
        $this->assertErrorAt($validator->errors(), '/data', Keys::MEMBER_REQUIRED);
        $this->assertDetailContains($validator->errors(), '/data', DocumentInterface::KEYWORD_DATA);
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
        $this->assertErrorAt($validator->errors(), '/data', Keys::MEMBER_MUST_BE_OBJECT);
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
        $this->assertErrorAt($validator->errors(), '/data/type', Keys::MEMBER_REQUIRED);
        $this->assertDetailContains($validator->errors(), '/data/type', DocumentInterface::KEYWORD_TYPE);
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
        $this->assertErrorAt($validator->errors(), '/data/type', Keys::RESOURCE_UNSUPPORTED_TYPE);
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
        $this->assertErrorAt($validator->errors(), '/data/id', Keys::MEMBER_REQUIRED);
        $this->assertDetailContains($validator->errors(), '/data/id', DocumentInterface::KEYWORD_ID);
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
        $this->assertErrorAt($validator->errors(), '/data/id', Keys::RESOURCE_UNSUPPORTED_ID);
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
        $this->assertErrorAt($validator->errors(), '/data/attributes', Keys::MEMBER_MUST_BE_OBJECT);
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
        $this->assertErrorAt($validator->errors(), '/data/attributes', Keys::RESOURCE_ATTRIBUTES_INVALID);
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
        $this->assertErrorAt($validator->errors(), '/data/relationships', Keys::MEMBER_MUST_BE_OBJECT);
        $this->assertDetailContains($validator->errors(), '/data/relationships', DocumentInterface::KEYWORD_RELATIONSHIPS);
    }

    /**
     * @param $id
     * @param AttributesValidatorInterface|null $attributes
     * @return DocumentValidatorInterface
     */
    private function validator($id = null, AttributesValidatorInterface $attributes = null)
    {
        $resource = $this->factory->resource('posts', $id, $attributes);
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
}
