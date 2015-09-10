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

use CloudCreativity\JsonApi\Contracts\Stdlib\MutableConfigInterface;
use Neomerx\JsonApi\Decoders\ArrayDecoder;
use Neomerx\JsonApi\Decoders\ObjectDecoder;

/**
 * Class DecodersRepositoryTest
 * @package CloudCreativity\JsonApi
 */
class DecodersRepositoryTest extends \PHPUnit_Framework_TestCase
{

    const VARIANT = 'foo';

    private $defaultDecoder;
    private $variantDecoder;

    /**
     * @var DecodersRepository
     */
    private $repository;

    /**
     * @var array
     */
    private $config;

    protected function setUp()
    {
        $this->defaultDecoder = new ObjectDecoder();
        $this->variantDecoder = new ArrayDecoder();

        $config = [
            DecodersRepository::DEFAULTS => get_class($this->defaultDecoder),
            static::VARIANT => [
                DecodersRepository::TYPE => get_class($this->variantDecoder),
            ],
        ];

        $this->config = $config;
        $this->repository = new DecodersRepository($config);
    }

    public function testDefault()
    {
        $this->assertEquals($this->defaultDecoder, $this->repository->getDecoder(DecodersRepository::DEFAULTS));
    }

    public function testVariant()
    {
        $actual = $this->repository->getDecoder(static::VARIANT);
        $this->assertEquals($this->variantDecoder, $actual);
    }

    /**
     * @depends testDefault
     */
    public function testRootConfig()
    {
        $repository = new DecodersRepository($this->config[static::VARIANT]);

        $this->assertEquals($this->variantDecoder, $repository->getDecoder());

        $this->setExpectedException(\RuntimeException::class);
        $repository->getDecoder('foo');
    }

    public function testModifier()
    {
        $oldType = get_class($this->defaultDecoder);
        $newType = get_class($this->variantDecoder);

        $callback = function (MutableConfigInterface $config, $name) use ($oldType, $newType) {
            $this->assertEquals($oldType, $config->get(DecodersRepository::TYPE));
            $this->assertEquals(DecodersRepository::DEFAULTS, $name);
            $config->set(DecodersRepository::TYPE, $newType);
        };

        $this->assertSame($this->repository, $this->repository->addModifier($callback));

        $actual = $this->repository->getDecoder();

        $this->assertEquals($this->variantDecoder, $actual);
    }
}
