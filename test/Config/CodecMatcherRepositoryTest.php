<?php

/**
 * Copyright 2015 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Config;

use CloudCreativity\JsonApi\Contracts\Config\EncodersRepositoryInterface;
use CloudCreativity\JsonApi\Contracts\Config\DecodersRepositoryInterface;
use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Contracts\Parameters\Headers\MediaTypeInterface;
use Neomerx\JsonApi\Decoders\ArrayDecoder;
use Neomerx\JsonApi\Decoders\ObjectDecoder;
use Neomerx\JsonApi\Encoder\Encoder;
use Neomerx\JsonApi\Encoder\EncoderOptions;
use Neomerx\JsonApi\Parameters\Headers\AcceptHeader;
use Neomerx\JsonApi\Parameters\Headers\AcceptMediaType;
use Neomerx\JsonApi\Parameters\Headers\Header;
use Neomerx\JsonApi\Parameters\Headers\MediaType;

/**
 * Class CodecMatcherRepositoryTest
 * @package CloudCreativity\JsonApi
 */
class CodecMatcherRepositoryTest extends \PHPUnit_Framework_TestCase
{

    const TEXT_MEDIA_TYPE = 'text/plain';
    const PARAM_MEDIA_TYPE = 'application/vnd.api+json;charset=utf-8';

    const VARIANT = 'foo';
    const TEXT_OPTIONS = 'text-options';
    const PARAM_SCHEMAS = 'param-schemas';
    const TEXT_DECODER = 'text-decoder';

    private $config = [
		CodecMatcherRepository::DEFAULTS => [
			CodecMatcherRepository::ENCODERS => [
				MediaTypeInterface::JSON_API_MEDIA_TYPE,
                self::PARAM_MEDIA_TYPE => [
                    CodecMatcherRepository::ENCODER_SCHEMAS => self::PARAM_SCHEMAS,
                ],
			],
			CodecMatcherRepository::DECODERS => [
                MediaTypeInterface::JSON_API_MEDIA_TYPE,
			],
		],
		self::VARIANT => [
			CodecMatcherRepository::ENCODERS => [
				self::TEXT_MEDIA_TYPE => [
					CodecMatcherRepository::ENCODER_OPTIONS => self::TEXT_OPTIONS,
				],
			],
            CodecMatcherRepository::DECODERS => [
                self::TEXT_MEDIA_TYPE => self::TEXT_DECODER,
            ],
		],
	];

    private $defaultEncoder;
    private $paramEncoder;
    private $textEncoder;

    private $defaultDecoder;
    private $paramDecoder;
    private $textDecoder;

    /**
     * @var CodecMatcherRepository
     */
    private $repository;

    protected function setUp()
    {
        $defaultSchemas = ['foo' => 'bar'];
        $paramSchemas = ['baz' => 'bar'];

        $defaultOptions = new EncoderOptions(JSON_BIGINT_AS_STRING, 'http://www.example.tld');
        $textOptions = new EncoderOptions(JSON_PRETTY_PRINT, 'http://www.foobar.tld');

        $this->defaultEncoder = Encoder::instance($defaultSchemas, $defaultOptions);
        $this->paramEncoder = Encoder::instance($paramSchemas, $defaultOptions);
        $this->textEncoder = Encoder::instance($defaultSchemas, $textOptions);

        $this->defaultDecoder = new ObjectDecoder();
        $this->paramEncoder = $this->defaultDecoder;
        $this->textDecoder = new ArrayDecoder();

        /** @var EncodersRepositoryInterface $encoders */
        $encoders = $this->getMock(EncodersRepositoryInterface::class);
        $encoders->method('getEncoder')
            ->will($this->returnValueMap([
                [null, null, $this->defaultEncoder],
                [self::PARAM_SCHEMAS, null, $this->paramEncoder],
                [null, self::TEXT_OPTIONS, $this->textEncoder],
            ]));

        /** @var DecodersRepositoryInterface $decoders */
        $decoders = $this->getMock(DecodersRepositoryInterface::class);
        $decoders->method('getDecoder')
            ->will($this->returnValueMap([
                [null, $this->defaultDecoder],
                [static::TEXT_DECODER, $this->textDecoder],
            ]));

        $this->repository = new CodecMatcherRepository($encoders, $decoders, $this->config);
    }

    public function testDefault()
    {
        $codecMatcher = $this->repository->getCodecMatcher();

        $this->assertInstanceOf(CodecMatcherInterface::class, $codecMatcher);

        $this->match($codecMatcher, MediaTypeInterface::JSON_API_MEDIA_TYPE);

        $this->assertEquals($this->defaultEncoder, $codecMatcher->getEncoder());
        $this->assertEquals($this->defaultDecoder, $codecMatcher->getDecoder());

        $codecMatcher->matchEncoder($this->acceptHeader(static::TEXT_MEDIA_TYPE));
        $codecMatcher->findDecoder($this->contentTypeHeader(static::TEXT_MEDIA_TYPE));

        $this->assertNull($codecMatcher->getEncoder());
        $this->assertNull($codecMatcher->getDecoder());
    }

    /**
     * @depends testDefault
     */
    public function testVariant()
    {
        $codecMatcher = $this->repository->getCodecMatcher(static::VARIANT);

        $this->match($codecMatcher, static::TEXT_MEDIA_TYPE);

        $this->assertEquals($this->textEncoder, $codecMatcher->getEncoder());
        $this->assertEquals($this->textDecoder, $codecMatcher->getDecoder());
    }

    /**
     * @depends testDefault
     */
    public function testWithParam()
    {
        $codecMatcher = $this->repository->getCodecMatcher();

        $this->match($codecMatcher, static::PARAM_MEDIA_TYPE);

        $this->assertEquals($this->paramEncoder, $codecMatcher->getEncoder());
        $this->assertEquals($this->paramDecoder, $codecMatcher->getDecoder());
    }

    /**
     * @param CodecMatcherInterface $codecMatcher
     * @param $mediaType
     */
    private function match(CodecMatcherInterface $codecMatcher, $mediaType)
    {
        $acceptHeaders = $this->acceptHeader($mediaType);
        $contentHeader = $this->contentTypeHeader($mediaType);

        $codecMatcher->matchEncoder($acceptHeaders);
        $codecMatcher->findDecoder($contentHeader);
    }

    /**
     * @param $mediaType
     * @return AcceptHeader
     */
    private function acceptHeader($mediaType)
    {
        return new AcceptHeader([
            AcceptMediaType::parse(0, $mediaType),
        ]);
    }

    /**
     * @param $mediaType
     * @return Header
     */
    private function contentTypeHeader($mediaType)
    {
        return new Header('Content-Type', [MediaType::parse(0, $mediaType)]);
    }

}
