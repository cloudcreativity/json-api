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

use CloudCreativity\JsonApi\Contracts\Repositories\EncoderOptionsRepositoryInterface;
use CloudCreativity\JsonApi\Contracts\Repositories\EncodersRepositoryInterface;
use CloudCreativity\JsonApi\Contracts\Repositories\SchemasRepositoryInterface;
use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Encoder\Encoder;

/**
 * Class EncodersRepository
 * @package CloudCreativity\JsonApi
 *
 * If a config array is provided to this class, it passes the config on
 * to the schema repository and/or encoder options repository.
 */
class EncodersRepository implements EncodersRepositoryInterface
{

    /**
     * @var SchemasRepositoryInterface
     */
    private $schemas;

    /**
     * @var EncoderOptionsRepositoryInterface
     */
    private $encoderOptions;

    /**
     * @param SchemasRepositoryInterface $schemas
     * @param EncoderOptionsRepositoryInterface $encoderOptions
     * @param array|null $config
     */
    public function __construct(
        SchemasRepositoryInterface $schemas,
        EncoderOptionsRepositoryInterface $encoderOptions,
        array $config = null
    ) {
        $this->schemas = $schemas;
        $this->encoderOptions = $encoderOptions;

        if (is_array($config)) {
            $this->configure($config);
        }
    }

    /**
     * @param string|null $schemas
     *      the named schema set to use, or empty to use the default schemas.
     * @param null $options
     *      the named encoder options to use, or empty to use the default options.
     * @return EncoderInterface
     */
    public function getEncoder($schemas = null, $options = null)
    {
        $schemas = $this->schemas->getSchemas($schemas);
        $options = $this->encoderOptions->getEncoderOptions($options);

        return Encoder::instance($schemas, $options);
    }

    /**
     * @param array $config
     * @return $this
     */
    public function configure(array $config)
    {
        if (isset($config[static::SCHEMAS]) && is_array($config[static::SCHEMAS])) {
            $this->schemas->configure($config[static::SCHEMAS]);
        }

        if (isset($config[static::OPTIONS]) && is_array($config[static::OPTIONS])) {
            $this->encoderOptions->configure($config[static::OPTIONS]);
        }

        return $this;
    }

}
