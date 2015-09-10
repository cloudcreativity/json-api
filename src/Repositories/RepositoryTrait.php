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

use CloudCreativity\JsonApi\Contracts\Stdlib\ConfigInterface;
use CloudCreativity\JsonApi\Contracts\Stdlib\MutableConfigInterface;
use CloudCreativity\JsonApi\Stdlib\Config;
use CloudCreativity\JsonApi\Stdlib\MutableConfig;

/**
 * Class RepositoryTrait
 * @package CloudCreativity\JsonApi
 */
trait RepositoryTrait
{

    /**
     * @var array
     */
    private $modifiers = [];

    /**
     * @var ConfigInterface|null
     */
    private $config;

    /**
     * @param \Closure $callback
     * @return $this
     */
    public function addModifier(\Closure $callback)
    {
        $this->modifiers[] = $callback;

        return $this;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function configure(array $config)
    {
        $this->config = new Config($config);

        return $this;
    }

    /**
     * @return ConfigInterface
     */
    protected function config()
    {
        if (!$this->config instanceof ConfigInterface) {
            $this->config = new Config();
        }

        return $this->config;
    }

    /**
     * @param null $key
     * @return MutableConfigInterface
     */
    protected function mutate($key = null)
    {
        $config = ($key) ? $this->config()->get($key, []) : $this->config()->toArray();

        return new MutableConfig((array) $config);
    }

    /**
     * @param array $keys
     * @param bool $recursive
     * @return MutableConfigInterface
     */
    protected function merge(array $keys, $recursive = false)
    {
        if (empty($keys)) {
            throw new \InvalidArgumentException('Expecting at least one key.');
        }

        $config = $this->mutate(array_shift($keys));

        foreach ($keys as $key) {

            $merge = $this
                ->config()
                ->get($key, []);

            $config->merge((array) $merge, $recursive);
        }

        return $config;
    }

    /**
     * @param MutableConfigInterface $config
     * @param $name
     * @return MutableConfigInterface
     */
    protected function modify(MutableConfigInterface $config, $name)
    {
        /** @var \Closure $modifier */
        foreach ($this->modifiers as $modifier) {
            $modifier($config, $name);
        }

        return $config;
    }
}
