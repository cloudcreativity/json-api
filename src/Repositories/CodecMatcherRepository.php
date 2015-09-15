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
 *               // the media types mapped to the named encoder options to use, or null to use the default
 *               'media-types' => [
 *                  'application/vnd.api+json' => null,
 *                  'text/plain' => 'humanized',
 *               ],
 *               // the schema set that should be used for these encoders, or null to use the default set.
 *               'schemas' => null,
 *           ],
 *          'decoders' => [
 *               // the media types mapped to the named decoder
 *               'media-types' => [
 *                  'application/vnd.api+json' => null,
 *           ],
 *       ],
 *       // The codec matcher named 'extended'. Will have default settings plus these below.
 *       'extended' => [
 *            'encoders' => [
 *               // add an extra media type that uses the default encoder options...
 *               'media-types' => [
 *                  'application/json' => null,
 *               ],
 *              // all encoders to use a different set of schemas...
 *              'schemas' => 'extra-schemas',
 *           ],
 *           'decoders' => [
 *               // add an extra media type that uses a specific decoder...
 *               'media-types' => [
 *                  'application/json' => 'json',
 *               ],
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
        $config = $this->merge($merge, true);
        $matcher = new CodecMatcher();

        $this->registerEncoders($matcher, (array) $config->get(static::ENCODERS))
            ->registerDecoders($matcher, (array) $config->get(static::DECODERS));

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

        $this->traitConfigure($config);

        return $this;
    }

    /**
     * @param CodecMatcherInterface $codecMatcher
     * @param array $config
     * @return $this
     */
    private function registerEncoders(CodecMatcherInterface $codecMatcher, array $config)
    {
        $schemasName = isset($config[static::SCHEMAS]) ? $config[static::SCHEMAS] : null;

        // because of recursive merging...
        if (is_array($schemasName)) {
            $schemasName = array_pop($schemasName);
        }

        $mediaTypes = isset($config[static::MEDIA_TYPES]) ? (array) $config[static::MEDIA_TYPES] : [];

        foreach ($mediaTypes as $key => $optionsName) {
            $mediaType = $this->toMediaType($key);

            // because of recursive merging...
            if (is_array($optionsName)) {
                $optionsName = array_pop($optionsName);
            }

            $codecMatcher->registerEncoder($mediaType, function () use ($schemasName, $optionsName) {
                return $this->encoders->getEncoder($schemasName, $optionsName);
            });
        }

        return $this;
    }

    /**
     * @param CodecMatcherInterface $codecMatcher
     * @param array $config
     * @return $this
     */
    private function registerDecoders(CodecMatcherInterface $codecMatcher, array $config)
    {
        $mediaTypes = isset($config[static::MEDIA_TYPES]) ? (array) $config[static::MEDIA_TYPES] : [];

        foreach ($mediaTypes as $key => $decoderName) {
            $mediaType = $this->toMediaType($key);

            // because of recursive merging...
            if (is_array($decoderName)) {
                $decoderName = array_pop($decoderName);
            }

            $codecMatcher->registerDecoder($mediaType, function () use ($decoderName) {
                return $this->decoders->getDecoder($decoderName);
            });
        }

        return $this;
    }

    /**
     * @param $string
     * @return MediaType
     */
    private function toMediaType($string)
    {
        return MediaType::parse(0, $string);
    }
}
