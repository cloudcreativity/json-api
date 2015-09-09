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

/**
 * Class SchemasRepositoryTest
 * @package CloudCreativity\JsonApi
 */
class SchemasRepositoryTest extends \PHPUnit_Framework_TestCase
{

    const VARIANT = 'foo';

    private $config = [
        SchemasRepository::DEFAULTS => [
            'Author' => 'AuthorSchema',
        ],
        self::VARIANT => [
            'Post' => 'PostSchema',
        ],
    ];

    /**
     * @var SchemasRepository
     */
    private $repository;

    protected function setUp()
    {
        $this->repository = new SchemasRepository($this->config);
    }

    public function testGetDefaults()
    {
        $defaults = $this->config[SchemasRepository::DEFAULTS];
        $this->assertEquals($defaults,  $this->repository->getSchemas());
        $this->assertEquals($defaults, $this->repository->getSchemas(SchemasRepository::DEFAULTS));
    }

    public function testGetVariant()
    {
        $expected = array_merge($this->config[SchemasRepository::DEFAULTS], $this->config[static::VARIANT]);
        $this->assertEquals($expected, $this->repository->getSchemas(static::VARIANT));

        return $expected;
    }

    /**
     * @depends testGetVariant
     */
    public function testModifier($variant)
    {
        $add = [
            'Comment' => 'CommentSchema',
        ];

        $expected = array_merge($variant, $add);

        $callback = function (MutableConfigInterface $config, $name) use ($variant, $add) {
            $this->assertEquals($variant, $config->toArray());
            $this->assertEquals(static::VARIANT, $name);
            $config->merge($add);
        };

        $this->assertSame($this->repository, $this->repository->addModifier($callback));
        $this->assertEquals($expected, $this->repository->getSchemas(static::VARIANT));
    }
}
