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

use CloudCreativity\JsonApi\Contracts\Repositories\CodecMatcherRepositoryInterface;
use CloudCreativity\JsonApi\Contracts\Repositories\DecodersRepositoryInterface;
use CloudCreativity\JsonApi\Contracts\Repositories\EncodersRepositoryInterface;
use CloudCreativity\JsonApi\Contracts\Stdlib\ConfigInterface;
use CloudCreativity\JsonApi\Stdlib\Config;
use Neomerx\JsonApi\Codec\CodecMatcher;
use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Parameters\Headers\MediaType;

/**
 * Class CodecMatcherRepository
 * @package CloudCreativity\JsonApi
 *
 * Example config:
 *
 * ````
 * [
 *       // The default codec matcher
 *       'defaults' => [
 *           'encoders' => [
 *               // creates encoder with default schemas and default options
 *               'application/vnd.api+json',
 *               // same
 *               'application/vnd.api+json;charset=utf-8'
 *           ],
 *       'decoders' => [
 *               'application/vnd.api+json',
 *               'application/vnd.api+json;charset=utf-8'
 *           ],
 *       ],
 *       // The codec matcher named 'extended'. Will have default settings plus these below.
 *       'extended' => [
 *            'encoders' => [
 *               // use the encoder options named 'json', with the default schemas.
 *               'application/json' => [
 *                   'options' => 'json',
 *               ],
 *               // use the encoder options named 'humanized', with the schemas named 'foo'.
 *               'text/plain' => [
 *                   'schemas' => 'foo',
 *                   'options' => 'humanized',
 *               ],
 *           ],
 *           // use the decoder named 'array'. Note that 'text/plain' will not have a decoder as none is listed.
 *           'decoders' => [
 *               'application/json' => 'array'
 *           ],
 *       ],
 * ]
 * ```
 *
 * This repository also accepts non-namespaced config: i.e. the provided config array will be loaded as the default if
 * it does not contain the `defaults` key.
 *
 */
class CodecMatcherRepository implements CodecMatcherRepositoryInterface
{

    use RepositoryTrait {
        configure as traitConfigure;
    }

    /**
     * @var EncodersRepositoryInterface
     */
    private $encoders;

    /**
     * @var DecodersRepositoryInterface
     */
    private $decoders;

    /**
     * @var bool
     */
    private $namespaced = false;

    /**
     * @param EncodersRepositoryInterface $encoders
     * @param DecodersRepositoryInterface $decoders
     * @param array $config
     */
    public function __construct(
        EncodersRepositoryInterface $encoders,
        DecodersRepositoryInterface $decoders,
        array $config = []
    ) {
        $this->encoders = $encoders;
        $this->decoders = $decoders;
        $this->configure($config);
    }

    /**
     * @param string|null $name
     *      the codec matcher name or empty to get the default codec matcher
     * @return CodecMatcher
     */
    public function getCodecMatcher($name = null)
    {
        $name = ($name) ?: static::DEFAULTS;

        if (static::DEFAULTS !== $name && !$this->namespaced) {
            throw new \RuntimeException(sprintf('Codec Matcher configuration is not namespaced, so cannot get "%s".', $name));
        }

        $merge = (static::DEFAULTS === $name) ? [$name] : [static::DEFAULTS, $name];
        $config = $this->merge($merge);

        $matcher = new CodecMatcher();

        $this->registerEncoders($matcher, $config->get(static::ENCODERS))
            ->registerDecoders($matcher, $config->get(static::DECODERS));

        return $matcher;
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

    /**
     * @param array $config
     * @return array
     */
    private function parseConfig(array $config)
    {
        $parsed = [];

        foreach ($config as $codecMatcherName => $codecMatcherConfig) {

            $encoders = isset($codecMatcherConfig[static::ENCODERS]) ? $codecMatcherConfig[static::ENCODERS] : [];
            $decoders = isset($codecMatcherConfig[static::DECODERS]) ? $codecMatcherConfig[static::DECODERS] : [];

            $parsed[$codecMatcherName] = [
                static::ENCODERS => new Config($this->parseEncodersConfig((array) $encoders)),
                static::DECODERS => new Config($this->parseDecodersConfig((array) $decoders)),
            ];
        }

        return $parsed;
    }

    /**
     * @param array $config
     * @return array
     */
    private function parseEncodersConfig(array $config)
    {
        $encoders = [];
        $defaults = [
            static::ENCODER_SCHEMAS => null,
            static::ENCODER_OPTIONS => null,
        ];

        foreach ($config as $mediaType => $value) {

            if (is_numeric($mediaType)) {
                $encoders[$value] = $defaults;
                continue;
            } elseif (!is_array($value)) {
                throw new \InvalidArgumentException('Encoder value must be an array if provided.');
            }

            $encoders[$mediaType] = array_merge($defaults, $value);
        }

        return $encoders;
    }

    /**
     * @param array $config
     * @return array
     */
    private function parseDecodersConfig(array $config)
    {
        $decoders = [];

        foreach ($config as $mediaType => $value) {

            if (is_numeric($mediaType)) {
                $mediaType = $value;
                $value = null;
            }

            $decoders[$mediaType] = $value;
        }

        return $decoders;
    }

    /**
     * @param CodecMatcherInterface $codecMatcher
     * @param ConfigInterface $config
     * @return $this
     */
    private function registerEncoders(CodecMatcherInterface $codecMatcher, ConfigInterface $config)
    {
        if ($config->isEmpty()) {
            throw new \RuntimeException('No encoders in configuration.');
        }

        foreach ($config as $mediaType => $encoderConfig) {

            $mediaType = MediaType::parse(0, $mediaType);
            $schemas = $encoderConfig[static::ENCODER_SCHEMAS];
            $options = $encoderConfig[static::ENCODER_OPTIONS];

            $codecMatcher->registerEncoder($mediaType, function () use ($schemas, $options) {
                return $this->encoders->getEncoder($schemas, $options);
            });
        }

        return $this;
    }

    /**
     * @param CodecMatcherInterface $codecMatcher
     * @param ConfigInterface $config
     * @return $this
     */
    private function registerDecoders(CodecMatcherInterface $codecMatcher, ConfigInterface $config)
    {
        foreach ($config as $mediaType => $decoderName) {

            $mediaType = MediaType::parse(0, $mediaType);

            $codecMatcher->registerDecoder($mediaType, function () use ($decoderName) {
                return $this->decoders->getDecoder($decoderName);
            });
        }

        return $this;
    }
}
