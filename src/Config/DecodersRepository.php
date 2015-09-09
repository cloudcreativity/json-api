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

use CloudCreativity\JsonApi\Contracts\Config\ConfigInterface;
use CloudCreativity\JsonApi\Contracts\Config\DecodersRepositoryInterface;
use CloudCreativity\JsonApi\Contracts\Stdlib\ConfigurableInterface;
use Neomerx\JsonApi\Contracts\Decoder\DecoderInterface;

/**
 * Class DecodersRepository
 * @package CloudCreativity\JsonApi
 *
 * Example config:
 *
 * ````
 * [
 *      'defaults' => ObjectDecoder::class,
 *      'array' => [
 *          'type' => ArrayDecoder::class,
 *          'options' => [],
 *      ],
 * ]
 * ````
 *
 * I.e. either a string for class name, or an array can be provided. Although no decoders accept 'options' at the
 * moment, it is reserved for future use.
 *
 * This repository also accepts non-namespaced config. I.e. if the provided config array does not have the 'defaults'
 * key, it will be loaded as the default configuration.
 */
class DecodersRepository implements DecodersRepositoryInterface
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
     * @param string|null $name
     * @return DecoderInterface
     */
    public function getDecoder($name = null)
    {
        $name = ($name) ?: static::DEFAULTS;

        if (static::DEFAULTS !== $name && !$this->namespaced) {
            throw new \RuntimeException(sprintf('Decoder configuration is not namespaced, so cannot get "%s".', $name));
        }

        $merge = (static::DEFAULTS === $name) ? [$name] : [static::DEFAULTS, $name];
        $config = $this->modify($this->merge($merge), $name);

        return $this->make($config);
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

        $this->traitConfigure($this->parseConfig($config));

        return $this;
    }

    private function parseConfig(array $config)
    {
        $decoders = [];
        $defaults = [
            static::TYPE => null,
            static::OPTIONS => null,
        ];

        foreach ($config as $decoderName => $decoderOptions) {

            if (is_string($decoderOptions)) {
                $decoderOptions = [
                    static::TYPE => $decoderOptions,
                ];
            }

            $decoders[$decoderName] = array_merge($defaults, (array) $decoderOptions);
        }

        return $decoders;
    }

    /**
     * @param ConfigInterface $config
     * @return DecoderInterface
     */
    private function make(ConfigInterface $config)
    {
        $class = $config->get(static::TYPE);

        if (!class_exists($class)) {
            throw new \RuntimeException(sprintf('Not a fully qualified class name for decoder "%s".', $name));
        }

        $decoder = new $class();

        if (!$decoder instanceof DecoderInterface) {
            throw new \RuntimeException(sprintf('Invalid decoder type for decoder "%s".', $name));
        }

        if ($decoder instanceof ConfigurableInterface) {
            $decoder->configure((array) $config->get(static::OPTIONS, []));
        }

        return $decoder;
    }

}
