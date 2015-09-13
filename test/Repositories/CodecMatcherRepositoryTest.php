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

namespace CloudCreativity\JsonApi\Repositories;

use CloudCreativity\JsonApi\Contracts\Repositories\EncodersRepositoryInterface;
use CloudCreativity\JsonApi\Contracts\Repositories\DecodersRepositoryInterface;
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
use CloudCreativity\JsonApi\Contracts\Repositories\CodecMatcherRepositoryInterface as Codec;

/**
 * Class CodecMatcherRepositoryTest
 * @package CloudCreativity\JsonApi
 */
class CodecMatcherRepositoryTest extends \PHPUnit_Framework_TestCase
{

    const TEXT_MEDIA_TYPE = 'text/plain';
    const TEXT_OPTIONS = 'humanized';
    const TEXT_DECODER = 'text-decoder';

    const VARIANT = 'foo';
    const VARIANT_SCHEMAS = 'foo-schemas';
    const VARIANT_EXTRA_MEDIA_TYPE = 'application/json';

    private $config = [
		Codec::DEFAULTS => [
			Codec::ENCODERS => [
                Codec::MEDIA_TYPES => [
                    MediaTypeInterface::JSON_API_MEDIA_TYPE => null,
                    self::TEXT_MEDIA_TYPE => self::TEXT_OPTIONS,
                ],
                Codec::SCHEMAS => null,
            ],
            Codec::DECODERS => [
                Codec::MEDIA_TYPES => [
                    MediaTypeInterface::JSON_API_MEDIA_TYPE => null,
                ],
            ],
        ],
        self::VARIANT => [
            Codec::ENCODERS => [
                Codec::MEDIA_TYPES => [
                    self::VARIANT_EXTRA_MEDIA_TYPE => null,
                ],
                Codec::SCHEMAS => self::VARIANT_SCHEMAS,
            ],
            Codec::DECODERS => [
                Codec::MEDIA_TYPES => [
                    self::TEXT_MEDIA_TYPE => self::TEXT_DECODER,
                ],
            ],
        ],
	];

    private $defaultEncoder;
    private $defaultDecoder;
    private $textEncoder;

    private $variantEncoder;
    private $variantTextEncoder;
    private $variantExtraEncoder;
    private $variantTextDecoder;

    /**
     * @var CodecMatcherRepository
     */
    private $repository;

    private $encoders;
    private $decoders;

    protected function setUp()
    {
        $defaultSchemas = ['foo' => 'bar'];
        $variantSchemas = ['baz' => 'bar'];

        $defaultOptions = new EncoderOptions(JSON_BIGINT_AS_STRING, 'http://www.example.tld');
        $textOptions = new EncoderOptions(JSON_PRETTY_PRINT, 'http://www.foobar.tld');

        $this->defaultEncoder = Encoder::instance($defaultSchemas, $defaultOptions);
        $this->textEncoder = Encoder::instance($defaultSchemas, $textOptions);
        $this->variantEncoder = Encoder::instance($variantSchemas, $defaultOptions);
        $this->variantTextEncoder = Encoder::instance($variantSchemas, $defaultOptions);
        $this->variantExtraEncoder = $this->variantEncoder;

        $this->defaultDecoder = new ObjectDecoder();
        $this->variantTextDecoder = new ArrayDecoder();

        /** @var EncodersRepositoryInterface $encoders */
        $encoders = $this->getMock(EncodersRepositoryInterface::class);
        $encoders->method('getEncoder')
            ->will($this->returnValueMap([
                [null, null, $this->defaultEncoder],
                [null, static::TEXT_OPTIONS, $this->textEncoder],
                [static::VARIANT_SCHEMAS, null, $this->variantEncoder],
                [static::VARIANT_SCHEMAS, static::TEXT_OPTIONS, $this->variantTextEncoder],
                [static::VARIANT_SCHEMAS, null, $this->variantExtraEncoder],
            ]));

        /** @var DecodersRepositoryInterface $decoders */
        $decoders = $this->getMock(DecodersRepositoryInterface::class);
        $decoders->method('getDecoder')
            ->will($this->returnValueMap([
                [null, $this->defaultDecoder],
                [static::TEXT_DECODER, $this->variantTextDecoder],
            ]));

        $this->repository = new CodecMatcherRepository($encoders, $decoders, $this->config);
        $this->encoders = $encoders;
        $this->decoders = $decoders;
    }

    public function testDefault()
    {
        $codecMatcher = $this->repository->getCodecMatcher();

        $this->assertInstanceOf(CodecMatcherInterface::class, $codecMatcher);

        $this->match($codecMatcher, MediaTypeInterface::JSON_API_MEDIA_TYPE);

        $this->assertEquals($this->defaultEncoder, $codecMatcher->getEncoder());
        $this->assertEquals($this->defaultDecoder, $codecMatcher->getDecoder());

        $this->match($codecMatcher, self::TEXT_MEDIA_TYPE);

        $this->assertEquals($this->textEncoder, $codecMatcher->getEncoder());
        $this->assertNull($codecMatcher->getDecoder());
    }

    /**
     * @depends testDefault
     */
    public function testVariant()
    {
        $codecMatcher = $this->repository->getCodecMatcher(static::VARIANT);

        $this->match($codecMatcher, MediaTypeInterface::JSON_API_MEDIA_TYPE);

        $this->assertEquals($this->variantEncoder, $codecMatcher->getEncoder());
        $this->assertEquals($this->defaultDecoder, $codecMatcher->getDecoder());

        $this->match($codecMatcher, self::TEXT_MEDIA_TYPE);

        $this->assertEquals($this->variantTextEncoder, $codecMatcher->getEncoder());
        $this->assertEquals($this->variantTextDecoder, $codecMatcher->getDecoder());

        $this->match($codecMatcher, self::VARIANT_EXTRA_MEDIA_TYPE);

        $this->assertEquals($this->variantExtraEncoder, $codecMatcher->getEncoder());
        $this->assertNull($codecMatcher->getDecoder());
    }

    /**
     * @depends testDefault
     */
    public function testRootConfig()
    {
        $config = $this->config[CodecMatcherRepository::DEFAULTS];

        $repository = new CodecMatcherRepository($this->encoders, $this->decoders, $config);

        $codecMatcher = $repository->getCodecMatcher();

        $this->match($codecMatcher, MediaTypeInterface::JSON_API_MEDIA_TYPE);

        $this->assertEquals($this->defaultEncoder, $codecMatcher->getEncoder());
        $this->assertEquals($this->defaultDecoder, $codecMatcher->getDecoder());

        $this->setExpectedException(\RuntimeException::class);
        $repository->getCodecMatcher('foo');
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
