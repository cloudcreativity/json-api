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

use CloudCreativity\JsonApi\Contracts\Config\MutableConfigInterface;
use Neomerx\JsonApi\Encoder\EncoderOptions;

/**
 * Class EncoderOptionsRepositoryTest
 * @package CloudCreativity\JsonApi
 */
class EncoderOptionsRepositoryTest extends \PHPUnit_Framework_TestCase
{

    const VARIANT = 'foo';

    /**
     * @var array
     */
    private $config = [
        EncoderOptionsRepository::DEFAULTS => [
            EncoderOptionsRepository::IS_SHOW_VERSION_INFO => true,
            EncoderOptionsRepository::VERSION_META => [
                'version' => '1.0',
            ],
        ],
        self::VARIANT => [
            EncoderOptionsRepository::OPTIONS => JSON_PRETTY_PRINT,
            EncoderOptionsRepository::VERSION_META => [
                'encoder' => self::VARIANT,
            ],
        ],
    ];

    /**
     * @var EncoderOptionsRepository
     */
    private $repository;

    protected function setUp()
    {
        $this->repository = new EncoderOptionsRepository($this->config);
    }

    public function testGetDefault()
    {
        $defaults = $this->config[EncoderOptionsRepository::DEFAULTS];
        $expected = new EncoderOptions(
            0,
            null,
            $defaults[EncoderOptionsRepository::IS_SHOW_VERSION_INFO],
            $defaults[EncoderOptionsRepository::VERSION_META]
        );

        $this->assertEquals($expected, $this->repository->getEncoderOptions());
        $this->assertEquals($expected, $this->repository->getEncoderOptions(EncoderOptionsRepository::DEFAULTS));

        return $expected;
    }

    public function testGetVariant()
    {
        $settings = array_merge_recursive($this->config[EncoderOptionsRepository::DEFAULTS], $this->config[static::VARIANT]);
        $expected = new EncoderOptions(
            $settings[EncoderOptionsRepository::OPTIONS],
            null,
            $settings[EncoderOptionsRepository::IS_SHOW_VERSION_INFO],
            $settings[EncoderOptionsRepository::VERSION_META]
        );

        $actual = $this->repository->getEncoderOptions(static::VARIANT);

        $this->assertEquals($expected, $actual);

        return [$expected, $settings];
    }

    /**
     * @depends testGetDefault
     * @param EncoderOptions $expected
     */
    public function testRootConfig(EncoderOptions $expected)
    {
        $repository = new EncoderOptionsRepository($this->config[EncoderOptionsRepository::DEFAULTS]);

        $this->assertEquals($expected, $repository->getEncoderOptions());

        $this->setExpectedException(\RuntimeException::class);
        $repository->getEncoderOptions('foo');
    }

    /**
     * @param array $input
     * @depends testGetVariant
     */
    public function testModifier(array $input)
    {
        /** @var EncoderOptions $options */
        list($options, $settings) = $input;
        $url = 'http://www.example.tld';

        $expected = new EncoderOptions(
            $options->getOptions(),
            $url,
            $options->isShowVersionInfo(),
            $options->getVersionMeta(),
            $options->getDepth()
        );

        $callback = function (MutableConfigInterface $config, $name) use ($settings, $url) {
            $this->assertEquals($settings, $config->toArray());
            $this->assertEquals(static::VARIANT, $name);
            $config->set(EncoderOptionsRepository::URL_PREFIX, $url);
        };

        $this->assertSame($this->repository, $this->repository->addModifier($callback));
        $this->assertEquals($expected, $this->repository->getEncoderOptions(static::VARIANT));
    }
}
