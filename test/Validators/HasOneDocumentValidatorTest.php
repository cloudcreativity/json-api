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

use CloudCreativity\JsonApi\Contracts\Object\ResourceIdentifierInterface;
use CloudCreativity\JsonApi\Contracts\Validators\DocumentValidatorInterface;
use CloudCreativity\JsonApi\Object\Document;
use CloudCreativity\JsonApi\Validators\ValidatorErrorFactory as Keys;
use Neomerx\JsonApi\Contracts\Document\DocumentInterface;

/**
 * Class HasOneDocumentValidatorTest
 * @package CloudCreativity\JsonApi
 */
final class HasOneDocumentValidatorTest extends TestCase
{

    public function testValid()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "users",
        "id": "99"
    }
}
JSON_API;

        $document = $this->decode($content);
        $validator = $this->hasOne();

        $this->assertTrue($validator->isValid($document));
    }

    public function testValidPolymorph()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "id": "123"
    }
}
JSON_API;

        $document = $this->decode($content);
        $validator = $this->hasOne(true, true, null, ['users', 'posts']);

        $this->assertTrue($validator->isValid($document));
    }

    public function testValidEmpty()
    {
        $content = '{"data": null}';
        $document = $this->decode($content);
        $validator = $this->hasOne();

        $this->assertTrue($validator->isValid($document));
    }

    public function testDataRequired()
    {
        $content = '{}';
        $document = $this->decode($content);
        $validator = $this->hasOne();

        $this->assertFalse($validator->isValid($document));
        $this->assertErrorAt($validator->errors(), '/data', Keys::MEMBER_REQUIRED);
        $this->assertDetailContains($validator->errors(), '/data', DocumentInterface::KEYWORD_DATA);
    }

    public function testDataNotRelationship()
    {
        $content = '{"data": "foo"}';
        $document = $this->decode($content);
        $validator = $this->hasOne();

        $this->assertFalse($validator->isValid($document));
        $this->assertErrorAt($validator->errors(), '/data', Keys::MEMBER_RELATIONSHIP_EXPECTED);
        $this->assertDetailContains($validator->errors(), '/data', DocumentInterface::KEYWORD_DATA);
    }


    public function testDataTypeRequired()
    {
        $content = <<<JSON_API
{
    "data": {
        "id": "99"
    }
}
JSON_API;

        $document = $this->decode($content);
        $validator = $this->hasOne();

        $this->assertFalse($validator->isValid($document));
        $this->assertErrorAt($validator->errors(), '/data', Keys::MEMBER_REQUIRED);
        $this->assertDetailContains($validator->errors(), '/data', DocumentInterface::KEYWORD_TYPE);
    }

    public function testDataTypeNotSupported()
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
        $validator = $this->hasOne();

        $this->assertFalse($validator->isValid($document));
        $this->assertErrorAt($validator->errors(), '/data/type', Keys::RELATIONSHIP_UNSUPPORTED_TYPE);
        $this->assertDetailContains($validator->errors(), '/data/type', 'users');
        $this->assertDetailContains($validator->errors(), '/data/type', 'posts');
    }

    public function testDataIdRequired()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "users"
    }
}
JSON_API;

        $document = $this->decode($content);
        $validator = $this->hasOne();

        $this->assertFalse($validator->isValid($document));
        $this->assertErrorAt($validator->errors(), '/data', Keys::MEMBER_REQUIRED);
        $this->assertDetailContains($validator->errors(), '/data', DocumentInterface::KEYWORD_ID);
    }

    public function testDataEmptyNotAllowed()
    {
        $content = '{"data": null}';
        $document = $this->decode($content);
        $validator = $this->hasOne(false);

        $this->assertFalse($validator->isValid($document));
        $this->assertErrorAt($validator->errors(), '/data', Keys::RELATIONSHIP_EMPTY_NOT_ALLOWED);
        $this->assertDetailContains($validator->errors(), '/data', 'empty');
    }

    public function testDataDoesNotExist()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "users",
        "id": "99"
    }
}
JSON_API;

        $document = $this->decode($content);
        $validator = $this->hasOne(false, false);

        $this->assertFalse($validator->isValid($document));
        $this->assertErrorAt(
            $validator->errors(),
            '/data',
            Keys::RELATIONSHIP_DOES_NOT_EXIST,
            Keys::STATUS_RELATED_RESOURCE_DOES_NOT_EXIST
        );
        $this->assertDetailContains($validator->errors(), '/data', 'exist');
    }

    public function testDataAcceptable()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "users",
        "id": "99"
    }
}
JSON_API;

        $document = $this->decode($content);
        $validator = $this->hasOne(false, true, function (ResourceIdentifierInterface $identifier) {
            $this->assertEquals("users", $identifier->type());
            $this->assertEquals("99", $identifier->id());
            return true;
        });

        $this->assertTrue($validator->isValid($document));

        return $document;
    }

    /**
     * @param Document $document
     * @depends testDataAcceptable
     */
    public function testDataNotAcceptable(Document $document)
    {
        $validator = $this->hasOne(false, true, function () { return false; });

        $this->assertFalse($validator->isValid($document));
        $this->assertErrorAt($validator->errors(), '/data', Keys::RELATIONSHIP_NOT_ACCEPTABLE);
        $this->assertDetailContains($validator->errors(), '/data', 'acceptable');
    }

    public function testDataHasMany()
    {
        $content = '{"data": []}';
        $document = $this->decode($content);
        $validator = $this->hasOne();

        $this->assertFalse($validator->isValid($document));
        $this->assertErrorAt($validator->errors(), '/data', Keys::RELATIONSHIP_HAS_ONE_EXPECTED);
        $this->assertDetailContains($validator->errors(), '/data', 'has-one');
    }

    /**
     * @param bool $allowEmpty
     * @param bool $exists
     * @param callable|null $acceptable
     * @param string|string[] $expectedType
     * @return DocumentValidatorInterface
     */
    private function hasOne(
        $allowEmpty = true,
        $exists = true,
        callable $acceptable = null,
        $expectedType = 'users'
    ) {
        $this->store->method('exists')->willReturn($exists);
        $validator = $this->factory->hasOne($expectedType, $allowEmpty, $acceptable);

        return $this->factory->relationshipDocument($validator);
    }
}
