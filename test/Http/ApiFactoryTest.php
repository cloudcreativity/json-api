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

namespace CloudCreativity\JsonApi\Http;

use CloudCreativity\JsonApi\TestCase;
use CloudCreativity\JsonApi\Store\Store;
use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterpreterInterface;
use CloudCreativity\JsonApi\Repositories\CodecMatcherRepository;
use CloudCreativity\JsonApi\Repositories\SchemasRepository;

final class ApiFactoryTest extends TestCase
{

    private $codecMatchers;

    private $schemas;

    private $store;

    private $requestInterpreter;

    private $factory;

    protected function setUp()
    {
        $this->codecMatchers = new CodecMatcherRepository();
        $this->schemas = new SchemasRepository();
        $this->store = new Store();
        $this->requestInterpreter = $this->getMock(RequestInterpreterInterface::class);
        $this->factory = new ApiFactory($this->codecMatchers, $this->schemas, $this->store, $this->requestInterpreter);

        $this->schemas->configure([
            'defaults' => [
                'Person' => 'Api\People\Schema',
                'Post' => 'Api\Posts\Schema',
                'Comment' => 'Api\Comments\Schema'
            ],
            'v1' => [
            ],
            'v2' => [
                'Person' => 'Api\People\V2\Schema'
            ],
        ]);
    }

    public function testApiV1()
    {
        $api = $this->factory->createApi('v1', []);
        $this->assertSame('v1', $api->getNamespace());
        $this->assertSame($this->store, $api->getStore());
        $this->assertSame($this->requestInterpreter, $api->getRequestInterpreter());
    }
}
