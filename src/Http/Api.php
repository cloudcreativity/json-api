<?php

/**
 * Copyright 2016 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Http;

use CloudCreativity\JsonApi\Contracts\Http\ApiInterface;
use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\SupportedExtensionsInterface;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface as SchemaContainerInterface;

/**
 * Class Api
 * @package CloudCreativity\JsonApi
 */
class Api implements ApiInterface
{

    const CONFIG_URL_PREFIX = 'url-prefix';
    const CONFIG_ROUTE_PREFIX = 'route-prefix';
    const CONFIG_SUPPORTED_EXT = 'supported-ext';

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var CodecMatcherInterface
     */
    private $codecMatcher;

    /**
     * @var SchemaContainerInterface
     */
    private $schemas;

    /**
     * @var null|string
     */
    private $urlPrefix;

    /**
     * @var SupportedExtensionsInterface|null
     */
    private $supportedExtensions;

    /**
     * ApiContainer constructor.
     * @param string $namespace
     * @param CodecMatcherInterface $codecMatcher
     * @param SchemaContainerInterface $schemaContainer
     * @param string|null $urlPrefix
     * @param SupportedExtensionsInterface|null $supportedExtensions
     */
    public function __construct(
        $namespace,
        CodecMatcherInterface $codecMatcher,
        SchemaContainerInterface $schemaContainer,
        $urlPrefix = null,
        SupportedExtensionsInterface $supportedExtensions = null
    ) {
        $this->namespace = $namespace;
        $this->codecMatcher = $codecMatcher;
        $this->schemas = $schemaContainer;
        $this->urlPrefix = $urlPrefix;
        $this->supportedExtensions = $supportedExtensions;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return CodecMatcherInterface
     */
    public function getCodecMatcher()
    {
        return $this->codecMatcher;
    }

    /**
     * @return EncoderInterface|null
     */
    public function getEncoder()
    {
        return $this->getCodecMatcher()->getEncoder();
    }

    /**
     * @return bool
     */
    public function hasEncoder()
    {
        return $this->getEncoder() instanceof EncoderInterface;
    }

    /**
     * @return SchemaContainerInterface
     */
    public function getSchemas()
    {
        return $this->schemas;
    }

    /**
     * @return null|string
     */
    public function getUrlPrefix()
    {
        return $this->urlPrefix;
    }

    /**
     * @return SupportedExtensionsInterface|null
     */
    public function getSupportedExts()
    {
        return $this->supportedExtensions;
    }
}
