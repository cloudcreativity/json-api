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
use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterpreterInterface;
use CloudCreativity\JsonApi\Contracts\Store\StoreInterface;
use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\SupportedExtensionsInterface;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface as SchemaContainerInterface;
use CloudCreativity\JsonApi\Contracts\Pagination\PagingStrategyInterface;
use CloudCreativity\JsonApi\Pagination\PagingStrategy;

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
     * @var RequestInterpreterInterface
     */
    private $interpreter;

    /**
     * @var CodecMatcherInterface
     */
    private $codecMatcher;

    /**
     * @var SchemaContainerInterface
     */
    private $schemas;

    /**
     * @param StoreInterface
     */
    private $store;

    /**
     * @var null|string
     */
    private $urlPrefix;

    /**
     * @var SupportedExtensionsInterface|null
     */
    private $supportedExtensions;

    /**
     * @var PagingStrategyInterface
     */
    private $pagingStrategy;

    /**
     * @var array
     */
    private $options;

    /**
     * ApiContainer constructor.
     * @param string $namespace
     * @param RequestInterpreterInterface $interpreter
     * @param CodecMatcherInterface $codecMatcher
     * @param SchemaContainerInterface $schemaContainer
     * @param StoreInterface $store
     * @param string|null $urlPrefix
     * @param SupportedExtensionsInterface|null $supportedExtensions
     * @param array $options
     */
    public function __construct(
        $namespace,
        RequestInterpreterInterface $interpreter,
        CodecMatcherInterface $codecMatcher,
        SchemaContainerInterface $schemaContainer,
        StoreInterface $store,
        $urlPrefix = null,
        SupportedExtensionsInterface $supportedExtensions = null,
        PagingStrategyInterface $pagingStrategy = null,
        array $options = []
    ) {
        $this->namespace = $namespace;
        $this->interpreter = $interpreter;
        $this->codecMatcher = $codecMatcher;
        $this->schemas = $schemaContainer;
        $this->store = $store;
        $this->urlPrefix = $urlPrefix;
        $this->supportedExtensions = $supportedExtensions;
        $this->pagingStrategy = $pagingStrategy ?: new PagingStrategy();
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return RequestInterpreterInterface
     */
    public function getRequestInterpreter()
    {
        return $this->interpreter;
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
     * @return StoreInterface
     */
    public function getStore()
    {
        return $this->store;
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

    /**
     * @inheritdoc
     */
    public function getPagingStrategy()
    {
        return $this->pagingStrategy;
    }

    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        return $this->options;
    }
}
