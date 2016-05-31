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
use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\HeaderParametersInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\HeaderParametersParserInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\HeadersCheckerInterface;
use Neomerx\JsonApi\Contracts\Http\HttpFactoryInterface;
use PHPUnit_Framework_MockObject_MockObject;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ContentNegotiatorTest
 * @package CloudCreativity\JsonApi
 */
final class ContentNegotiatorTest extends TestCase
{

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $factory;

    /**
     * @var ContentNegotiator
     */
    private $negotiator;

    /**
     * @return void
     */
    protected function setUp()
    {
        /** @var HttpFactoryInterface $factory */
        $factory = $this->getMockBuilder(HttpFactoryInterface::class)->getMock();

        $this->negotiator = new ContentNegotiator($factory);
        $this->factory = $factory;
    }

    /**
     * Test HTTP request headers are parsed and checked.
     */
    public function testContentNegotiation()
    {
        /** @var CodecMatcherInterface $codecMatcher */
        $codecMatcher = $this->getMockBuilder(CodecMatcherInterface::class)->getMock();;
        /** @var ServerRequestInterface $request */
        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $parser = $this->getMockBuilder(HeaderParametersParserInterface::class)->getMock();
        $checker = $this->getMockBuilder(HeadersCheckerInterface::class)->getMock();
        $parsed = $this->getMockBuilder(HeaderParametersInterface::class)->getMock();

        /** Set up factory */
        $this->factory->method('createHeaderParametersParser')->willReturn($parser);
        $this->factory->method('createHeadersChecker')->with($codecMatcher)->willReturn($checker);
        /** Ensure headers are parsed */
        $parser->expects($this->once())->method('parse')->with($request)->willReturn($parsed);
        /** Ensure parsed headers are checked */
        $checker->expects($this->once())->method('checkHeaders')->with($parsed);

        $this->negotiator->doContentNegotiation($codecMatcher, $request);
    }
}
