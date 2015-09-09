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

use CloudCreativity\JsonApi\Contracts\Config\EncoderOptionsRepositoryInterface;
use Neomerx\JsonApi\Encoder\EncoderOptions;

/**
 * Class EncoderOptionsRepository
 * @package CloudCreativity\JsonApi
 *
 * Example provided array:
 *
 * ````
 * [
 *      'defaults' => [
 *           'version' => true,
 *           'version-meta' => [
 *              'version' => '1.0',
 *          ],
 *      ],
 *      'humanized' => [
 *          'options' => JSON_PRETTY_PRINT,
 *      ],
 * ]
 * ````
 *
 * If the `humanized` encoder options are requested, 'humanized' will be recursively merged into 'defaults' and then
 * used to generate an EncoderOptions instance.
 *
 */
class EncoderOptionsRepository implements EncoderOptionsRepositoryInterface
{

    use RepositoryTrait;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->configure($config);
    }

    /**
     * @param string $name
     * @param array $extras
     * @return EncoderOptions
     */
    public function getEncoderOptions($name = null, array $extras = [])
    {
        $name = ($name) ?: static::DEFAULTS;
        $merge = (static::DEFAULTS === $name) ? [$name] : [static::DEFAULTS, $name];
        $config = $this->modify($this->merge($merge, true), $name);

        return new EncoderOptions(
            $config->get(static::OPTIONS, static::OPTIONS_DEFAULT),
            $config->get(static::URL_PREFIX, static::URL_PREFIX_DEFAULT),
            $config->get(static::IS_SHOW_VERSION_INFO, static::IS_SHOW_VERSION_INFO_DEFAULT),
            $config->get(static::VERSION_META, static::VERSION_META_DEFAULT),
            $config->get(static::DEPTH, static::DEPTH_DEFAULT)
        );
    }
}
