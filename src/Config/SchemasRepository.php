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

use CloudCreativity\JsonApi\Contracts\Config\SchemasRepositoryInterface;

/**
 * Class SchemasRepository
 * @package CloudCreativity\JsonApi
 *
 * Example provided config array:
 *
 * ````
 * [
 *      'defaults' => [
 *          'Author' => 'AuthorSchema',
 *          'Post' => 'PostSchema',
 *      ],
 *      'foo' => [
 *           'Comment' => 'CommentSchema',
 *      ],
 * ]
 * ````
 *
 * If the 'foo' schema is requested, the return array will have Author, Schema and Comment in it.
 *
 * This repository also accepts non-namespaced schemas. I.e. if the config array does not have a 'defaults' key, it
 * will be loaded as the default schemas.
 */
class SchemasRepository implements SchemasRepositoryInterface
{

    use RepositoryTrait {
        configure as traitConfigure;
    }

    /**
     * @var bool
     */
    private $namespaced = false;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->configure($config);
    }

    /**
     * @param string $name
     * @return array
     */
    public function getSchemas($name = null)
    {
        $name = ($name) ?: static::DEFAULTS;

        if (static::DEFAULTS !== $name && !$this->namespaced) {
            throw new \RuntimeException(sprintf('Schemas configuration is not namespaced, so cannot get "%s".', $name));
        }

        $merge = (static::DEFAULTS === $name) ? [$name] : [static::DEFAULTS, $name];
        $config = $this->merge($merge);

        return $this
            ->modify($config, $name)
            ->toArray();
    }

    /**
     * @param array $config
     * @return $this
     */
    public function configure(array $config)
    {
        if (!isset($config[static::DEFAULTS])) {
            $config = [static::DEFAULTS => $config];
            $this->namespaced = false;
        } else {
            $this->namespaced = true;
        }

        $this->traitConfigure($config);

        return $this;
    }

}
