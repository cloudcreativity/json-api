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

use CloudCreativity\JsonApi\Contracts\Config\DecodersRepositoryInterface;
use CloudCreativity\JsonApi\Contracts\Stdlib\ConfigurableInterface;
use Neomerx\JsonApi\Contracts\Decoder\DecoderInterface;

/**
 * Class DecodersRepository
 * @package CloudCreativity\JsonApi
 */
class DecodersRepository implements DecodersRepositoryInterface
{

    use RepositoryTrait {
        configure as traitConfigure;
    }

    public function __construct(array $config = [])
    {
        $this->configure($config);
    }

    public function getDecoder($name = null)
    {
        $name = ($name) ?: static::DEFAULTS;
        $merge = (static::DEFAULTS === $name) ? [$name] : [static::DEFAULTS, $name];
        $config = $this->modify($this->merge($merge), $name);

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

    /**
     * @param array $config
     * @return $this
     */
    public function configure(array $config)
    {
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

}
