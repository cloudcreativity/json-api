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
 * Class ErrorsTesterTest
 *
 * @package CloudCreativity\JsonApi
 */
final class ErrorsTesterTest extends TestCase
{

    public function testNoErrors()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "id": "1"
    }
}
JSON_API;

        $document = DocumentTester::create($content);

        $this->willFail(function () use ($document) {
            $document->assertErrors();
        });
    }

    public function testEmptyErrors()
    {
        $content = '{"errors": []}';
        $errors = DocumentTester::create($content)
            ->assertErrors()
            ->assertEmpty();

        $this->willFail(function () use ($errors) {
            $errors->assertNotEmpty();
        });
    }

    public function testNotEmptyErrors()
    {
        $content = <<<JSON_API
{
    "errors": [
        {
            "status": "500"
        }
    ]
}
JSON_API;

        $errors = DocumentTester::create($content)
            ->assertErrors()
            ->assertNotEmpty();

        $this->willFail(function () use ($errors) {
            $errors->assertEmpty();
        });
    }

    public function testAssertCodes()
    {
        $content = <<<JSON_API
{
    "errors": [
        {"code": "foo"},
        {"code": "bar"},
        {"detail": "This error has no code"},
        {"code": "baz"}
    ]
}
JSON_API;

        $errors = DocumentTester::create($content)
            ->assertErrors()
            ->assertCodes(['foo', 'bar'])
            ->assertCodes('baz');

        $this->willFail(function () use ($errors) {
            $errors->assertCodes('bat');
        });
    }

    public function testAssertStatuses()
    {
        $content = <<<JSON_API
{
    "errors": [
        {"status": "422"},
        {"status": "404"},
        {"detail": "This error has no status"},
        {"status": "500"}
    ]
}
JSON_API;

        $errors = DocumentTester::create($content)
            ->assertErrors()
            ->assertStatuses(['422', '500'])
            ->assertStatuses('404');

        $this->willFail(function () use ($errors) {
            $errors->assertCodes('503');
        });
    }

    public function testAssertPointers()
    {
        $content = <<<JSON_API
{
    "errors": [
        {"source": {"pointer": "/data/attributes/foo"}},
        {"source": {"pointer": "/data/attributes/bar"}},
        {"source": {}},
        {"source": {"pointer": "/data/attributes/baz"}},
        {"detail": "This errors has no pointer"}
    ]
}
JSON_API;

        $errors = DocumentTester::create($content)
            ->assertErrors()
            ->assertPointers(['/data/attributes/foo', '/data/attributes/bar'])
            ->assertPointers('/data/attributes/baz');

        $this->willFail(function () use ($errors) {
            $errors->assertPointers('/data/attributes/bat');
        });
    }

    public function testAssertParameters()
    {
        $content = <<<JSON_API
{
    "errors": [
        {"source": {"parameter": "filter.foo"}},
        {"source": {"parameter": "filter.bar"}},
        {"source": {}},
        {"source": {"parameter": "filter.baz"}},
        {"detail": "This errors has no parameter"}
    ]
}
JSON_API;

        $errors = DocumentTester::create($content)
            ->assertErrors()
            ->assertParameters(['filter.foo', 'filter.bar'])
            ->assertParameters('filter.baz');

        $this->willFail(function () use ($errors) {
            $errors->assertParameters('filter.bat');
        });
    }

    public function testNoError()
    {
        $content = '{"errors": []}';
        $errors = DocumentTester::create($content)->assertErrors();

        $this->willFail(function () use ($errors) {
            $errors->assertOne();
        });
    }

    public function testErrorCode()
    {
        $content = <<<JSON_API
{
    "errors": [
        {"code": "foo"}
    ]
}
JSON_API;

        $error = DocumentTester::create($content)
            ->assertErrors()
            ->assertOne()
            ->assertCode('foo');

        $this->willFail(function () use ($error) {
            $error->assertCode('bar');
        });
    }

    public function testErrorStatus()
    {
        $content = <<<JSON_API
{
    "errors": [
        {"status": "422"}
    ]
}
JSON_API;

        $error = DocumentTester::create($content)
            ->assertErrors()
            ->assertOne()
            ->assertStatus(422);

        $this->willFail(function () use ($error) {
            $error->assertStatus(418);
        });
    }

    public function testErrorPointer()
    {
        $content = <<<JSON_API
{
    "errors": [
        {"source": {"pointer": "/data/relationships/foo"}}
    ]
}
JSON_API;

        $error = DocumentTester::create($content)
            ->assertErrors()
            ->assertOne()
            ->assertPointer('/data/relationships/foo');

        $this->willFail(function () use ($error) {
            $error->assertPointer('/data/relationships/bar');
        });
    }

    public function testErrorParameter()
    {
        $content = <<<JSON_API
{
    "errors": [
        {"source": {"parameter": "filter.foo"}}
    ]
}
JSON_API;

        $error = DocumentTester::create($content)
            ->assertErrors()
            ->assertOne()
            ->assertParameter('filter.foo');

        $this->willFail(function () use ($error) {
            $error->assertParameter('filter.bar');
        });
    }
}
