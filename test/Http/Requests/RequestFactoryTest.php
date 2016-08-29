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

namespace CloudCreativity\JsonApi\Http\Requests;

use CloudCreativity\JsonApi\Http\Requests\AbstractRequestInterpreter;
use CloudCreativity\JsonApi\Http\Api;
use CloudCreativity\JsonApi\Store\Store;
use CloudCreativity\JsonApi\Validators\ValidatorProvider;
use CloudCreativity\JsonApi\TestCase;
use Neomerx\JsonApi\Factories\Factory;
use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Psr7\ServerRequest;
use Neomerx\JsonApi\Http\Headers\MediaType;
use Neomerx\JsonApi\Encoder\Encoder;
use CloudCreativity\JsonApi\Decoders\DocumentDecoder;
use CloudCreativity\JsonApi\Object\Document;
use CloudCreativity\JsonApi\Contracts\Store\AdapterInterface;
use CloudCreativity\JsonApi\Object\ResourceIdentifier;
use Neomerx\JsonApi\Exceptions\JsonApiException;

final class RequestFactoryTest extends TestCase
{

    private $api;

    private $interpreter;

    private $codecMatcher;

    private $serverRequest;

    private $adapter;

    private $requestFactory;

    private $expectedUri;

    private $expectedDocument;

    private $expectedRecord;

    protected function setUp()
    {
        $store = new Store();
        $factory = new Factory();

        $this->codecMatcher = $factory->createCodecMatcher();
        $this->interpreter = $this->getMockForAbstractClass(AbstractRequestInterpreter::class);
        $this->adapter = $this->getMock(AdapterInterface::class);
        $this->adapter->method('recognises', 'posts')->willReturn(true);
        $store->register($this->adapter);
        $this->api = new Api('v1', $this->interpreter, $this->codecMatcher, $factory->createContainer(), $store);
        $this->requestFactory = new RequestFactory();
        $this->withMediaType();
    }

    public function testIndex()
    {
        $this->withRequest()
            ->doBuild();
    }

    public function testCreateResource()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "attributes": {
            "title": "My first post",
            "content": "..."
        }
    }
}
JSON_API;

        $this->withRequest('POST', null, null, $content)
            ->doBuild();
    }

    public function testReadResource()
    {
        $this->withRequest('GET', '123')
            ->withRecord('123')
            ->doBuild();
    }

    public function testUpdateResource()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "id": "123",
        "attributes": {
            "title": "My first post",
            "content": "..."
        }
    }
}
JSON_API;

        $this->withRequest('PATCH', '123', null, $content)
            ->withRecord('123')
            ->doBuild();
    }

    public function testDeleteResource()
    {
        $this->withRequest('DELETE', '123')
            ->withRecord('123')
            ->doBuild();
    }

    public function testModifyRelationship()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "people",
        "id": "99"
    }
}
JSON_API;

        $this->withRequest('GET', '123', 'author')
            ->withRecord('123')
            ->doBuild();
    }

    public function testNotAcceptable()
    {
        $headers = ['Accept' => 'text/plain'];

        $this->withRequest('GET', null, null, null, $headers)
            ->doFailure(406);
    }

    public function testUnsupportedMediaType()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "attributes": {
            "title": "My first post",
            "content": "..."
        }
    }
}
JSON_API;

        $headers = ['Content-Type' => 'application/json'];

        $this->withRequest('POST', null, null, $content, $headers)
            ->doFailure(415);
    }

    public function testNotFound()
    {
        $this->withRequest('GET', '123')
            ->doFailure(404);
    }

    public function testInvalidJson()
    {
        $content = '{"data": {"type": "posts"}';

        $this->withRequest('POST', null, null, $content)
            ->doFailure(400);
    }

    public function testInvalidJsonApiContent()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "",
        "attributes": {
            "title": "My first post",
            "content": "..."
        }
    }
}
JSON_API;

        $this->withRequest('POST')
            ->doFailure(400);
    }

    public function testNoContent()
    {
        $this->withRequest('POST')
            ->doFailure(400);
    }

    private function doBuild()
    {
        $request = $this->requestFactory->build($this->api, $this->serverRequest);

        if (!$request instanceof Request) {
            $this->fail('No request built.');
        }

        list ($resourceId, $relationshipName) = $this->expectedUri;
        $this->assertEquals('posts', $request->getResourceType());
        $this->assertEquals($resourceId, $request->getResourceId());
        $this->assertEquals($relationshipName, $request->getRelationshipName());
        $this->assertEquals($this->expectedDocument, $request->getDocument());
        $this->assertEquals($this->expectedRecord, $request->getRecord());

        return $request;
    }

    private function doFailure($expectedStatus)
    {
        try {
            $this->doBuild();
            $this->fail('No exception thrown');
        } catch (JsonApiException $ex) {
            $this->assertEquals($expectedStatus, $ex->getHttpCode());
        }

        return $this;
    }

    private function withRequest(
        $method = 'GET',
        $resourceId = null,
        $relationship = null,
        $content = null,
        array $headers = [],
        array $params = []
    ) {
        $headers = $this->normalizeHeaders($headers);

        $this->interpreter->method('isMethod')->willReturnMap([[strtolower($method), true]]);
        $this->interpreter->method('getResourceType')->willReturn('posts');
        $this->interpreter->method('getResourceId')->willReturn($resourceId);
        $this->interpreter->method('getRelationshipName')->willReturn($relationship);
        $this->interpreter->method('isRelationshipData')->willReturn(!is_null($relationship));

        $uri = $this->normalizeUri('posts', $resourceId, $relationship);
        $this->serverRequest = $this->httpRequest($method, $uri, $params, $headers, $content);

        $this->expectedUri = [$resourceId, $relationship];
        $this->expectedDocument = $content ? new Document(json_decode($content)) : null;

        return $this;
    }

    private function withMediaType($mediaType = 'application/vnd.api+json')
    {
        $mediaType = MediaType::parse(0, $mediaType);

        $this->codecMatcher->registerEncoder($mediaType, function () {
            return Encoder::instance();
        });

        $this->codecMatcher->registerDecoder($mediaType, function () {
            return new DocumentDecoder();
        });

        return $this;
    }

    private function withRecord($resourceId)
    {
        $this->expectedRecord = new \stdClass();
        $identifier = ResourceIdentifier::create('posts', $resourceId);
        $this->adapter->method('exists')->with($identifier)->willReturn(true);
        $this->adapter->method('find')->with($identifier)->willReturn($this->expectedRecord);

        return $this;
    }

    private function normalizeHeaders(array $headers, $content = null)
    {
        $defaults = ['Accept' => 'application/vnd.api+json'];

        if ($content) {
            $defaults['Content-Type'] = 'application/vnd.api+json';
        }

        return array_replace($defaults, $headers);
    }

    /**
     * @return string
     */
    private function normalizeUri($resourceType, $resourceId = null, $relationship = null, $isRelated = false)
    {
        if ($relationship && !$isRelated) {
            return sprintf('%s/%s/relationships/%s', $resourceType, $resourceId, $relationship);
        } elseif ($relationship) {
            return sprintf('%s/%s/%s', $resourceType, $resourceId, $relationship);
        } elseif ($resourceId) {
            return sprintf('%s/%s', $resourceType, $resourceId);
        }

        return $resourceType;
    }

    private function httpRequest($method, $uri, array $params = [], array $headers = [], $content = null)
    {
        if ($params) {
            $uri .= '?' . http_build_query($params);
        }

        $this->normalizeHeaders($headers, $content);

        return new ServerRequest($method, $uri, $headers, $content);
    }
}
