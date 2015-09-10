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

use CloudCreativity\JsonApi\Contracts\Repositories\SchemasRepositoryInterface;
use CloudCreativity\JsonApi\Contracts\Repositories\EncoderOptionsRepositoryInterface;
use Neomerx\JsonApi\Encoder\Encoder;
use Neomerx\JsonApi\Encoder\EncoderOptions;

/**
 * Class EncodersRepositoryTest
 * @package CloudCreativity\JsonApi
 */
class EncodersRepositoryTest extends \PHPUnit_Framework_TestCase
{


    const SCHEMAS = 'default-schemas';
    const OPTIONS = 'default-options';

    /**
     * @var array
     */
    private $schemas = [
        'Author' => 'AuthorSchema',
    ];

    private $options;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockSchemasRepository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockOptionsRepository;

    /**
     * @var EncodersRepository
     */
    private $repository;

    protected function setUp()
    {
        /** @var SchemasRepositoryInterface $schemas */
        $schemas = $this->getMock(SchemasRepositoryInterface::class);
        /** @var EncoderOptionsRepositoryInterface $options */
        $options = $this->getMock(EncoderOptionsRepositoryInterface::class);

        $this->repository = new EncodersRepository($schemas, $options);

        $this->mockSchemasRepository = $schemas;
        $this->mockOptionsRepository = $options;
        $this->options = new EncoderOptions(JSON_PRETTY_PRINT, 'http://www.example.tld');
    }

    public function testDefaults()
    {
        $this->encoderSchemas([
            [null, $this->schemas],
        ])->encoderOptions([
            [null, $this->options],
        ]);

        $expected = Encoder::instance($this->schemas, $this->options);

        $this->assertEquals($expected, $this->repository->getEncoder());
    }

    public function testSchemas()
    {
        $this->encoderSchemas([
            [static::SCHEMAS, $this->schemas],
        ])->encoderOptions([
            [null, $this->options],
        ]);

        $expected = Encoder::instance($this->schemas, $this->options);

        $this->assertEquals($expected, $this->repository->getEncoder(static::SCHEMAS));
    }

    public function testOptions()
    {
        $this->encoderSchemas([
            [null, $this->schemas],
            [static::SCHEMAS, $this->schemas],
        ])->encoderOptions([
            [static::OPTIONS, $this->options],
        ]);

        $expected = Encoder::instance($this->schemas, $this->options);

        $this->assertEquals($expected, $this->repository->getEncoder(null, static::OPTIONS));
        $this->assertEquals($expected, $this->repository->getEncoder(static::SCHEMAS, static::OPTIONS));
    }

    public function testConfiguration()
    {
        $schemas = ['schemas' => 'config'];
        $options = ['encoder' => 'options'];

        $config = [
            EncodersRepository::SCHEMAS => $schemas,
            EncodersRepository::OPTIONS => $options,
        ];

        $this->mockSchemasRepository
            ->expects($this->exactly(2))
            ->method('configure')
            ->with($schemas);

        $this->mockOptionsRepository
            ->expects($this->exactly(2))
            ->method('configure')
            ->with($options);

        $repository = new EncodersRepository($this->mockSchemasRepository, $this->mockOptionsRepository, $config);

        $this->assertSame($repository, $repository->configure($config));
    }

    /**
     * @param array $returnValues
     * @return $this
     */
    private function encoderSchemas(array $returnValues)
    {
        $this->mockSchemasRepository
            ->method('getSchemas')
            ->will($this->returnValueMap($returnValues));

        return $this;
    }

    /**
     * @param array $returnValues
     * @return $this
     */
    private function encoderOptions(array $returnValues)
    {
        $this->mockOptionsRepository
            ->method('getEncoderOptions')
            ->will($this->returnValueMap($returnValues));

        return $this;
    }
}
