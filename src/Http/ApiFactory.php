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

use CloudCreativity\JsonApi\Contracts\Http\ApiFactoryInterface;
use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterpreterInterface;
use CloudCreativity\JsonApi\Contracts\Pagination\PagingStrategyInterface;
use CloudCreativity\JsonApi\Contracts\Repositories\CodecMatcherRepositoryInterface;
use CloudCreativity\JsonApi\Contracts\Repositories\SchemasRepositoryInterface;
use CloudCreativity\JsonApi\Contracts\Store\StoreInterface;
use CloudCreativity\JsonApi\Contracts\Utils\ConfigurableInterface;
use CloudCreativity\JsonApi\Exceptions\RuntimeException;
use CloudCreativity\JsonApi\Pagination\PagingStrategy;
use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\SupportedExtensionsInterface;
use Neomerx\JsonApi\Contracts\Http\HttpFactoryInterface;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface as SchemaContainerInterface;
use Neomerx\JsonApi\Factories\Factory;
use Neomerx\JsonApi\Http\Headers\SupportedExtensions;

/**
 * Class ApiFactory
 * @package CloudCreativity\JsonApi
 */
class ApiFactory implements ApiFactoryInterface, ConfigurableInterface
{

    const CONFIG_URL_PREFIX = 'url-prefix';
    const CONFIG_SUPPORTED_EXT = 'supported-ext';
    const CONFIG_PAGING = 'paging';

    /**
     * @var CodecMatcherRepositoryInterface
     */
    private $codecMatcherRepository;

    /**
     * @var SchemasRepositoryInterface
     */
    private $schemasRepository;

    /**
     * @var StoreInterface
     */
    private $store;

    /**
     * @var RequestInterpreterInterface
     */
    private $requestInterpreter;

    /**
     * @var HttpFactoryInterface
     */
    private $httpFactory;

    /**
     * @var array
     */
    private $config = [];

    /**
     * ApiFactory constructor.
     * @param CodecMatcherRepositoryInterface $codecMatcherRespository
     * @param SchemasRepositoryInterface $schemasRepository
     * @param StoreInterface $store
     * @param RequestInterpreterInterface $interpreter
     * @param HttpFactoryInterface $httpFactory
     * @todo support a store on a per-API basis.
     */
    public function __construct(
        CodecMatcherRepositoryInterface $codecMatcherRespository,
        SchemasRepositoryInterface $schemasRepository,
        StoreInterface $store,
        RequestInterpreterInterface $interpreter,
        HttpFactoryInterface $httpFactory = null
    ) {
        $this->codecMatcherRepository = $codecMatcherRespository;
        $this->schemasRepository = $schemasRepository;
        $this->store = $store;
        $this->requestInterpreter = $interpreter;
        $this->httpFactory = $httpFactory ?: new Factory();
    }

    /**
     * @inheritDoc
     */
    public function configure(array $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function createApi($namespace, $host = null)
    {
        $config = $this->configFor($namespace, $host);
        $urlPrefix = $config[self::CONFIG_URL_PREFIX] ?: null;
        $schemas = $this->createSchemas($namespace);

        return new Api(
            $namespace,
            $this->requestInterpreter,
            $this->createCodecMatcher($schemas, $urlPrefix),
            $schemas,
            $this->store,
            $this->createSupportedExt($config[self::CONFIG_SUPPORTED_EXT]),
            $this->createPagingStrategy((array) $config[self::CONFIG_PAGING]),
            $this->httpFactory,
            $urlPrefix,
            $this->createOptions($config)
        );
    }

    /**
     * @param $namespace
     * @return SchemaContainerInterface
     */
    protected function createSchemas($namespace)
    {
        return $this->schemasRepository->getSchemas($namespace);
    }

    /**
     * @param SchemaContainerInterface $schemas
     * @param null $urlPrefix
     * @return CodecMatcherInterface
     */
    protected function createCodecMatcher(SchemaContainerInterface $schemas, $urlPrefix = null)
    {
        return $this
            ->codecMatcherRepository
            ->registerSchemas($schemas)
            ->registerUrlPrefix($urlPrefix)
            ->getCodecMatcher();
    }

    /**
     * @param $supportedExt
     * @return SupportedExtensionsInterface|null
     */
    protected function createSupportedExt($supportedExt)
    {
        return !empty($supportedExt) ? new SupportedExtensions($supportedExt) : null;
    }

    /**
     * @param array $config
     * @return PagingStrategyInterface
     */
    protected function createPagingStrategy(array $config)
    {
        return new PagingStrategy($config);
    }

    /**
     * @param array $config
     * @return array
     */
    protected function createOptions(array $config)
    {
        unset(
            $config[self::CONFIG_URL_PREFIX],
            $config[self::CONFIG_SUPPORTED_EXT],
            $config[self::CONFIG_PAGING]
        );

        return $config;
    }

    /**
     * @param string $namespace
     * @param string|null $host
     * @return array
     */
    private function configFor($namespace, $host)
    {
        if (!isset($this->config[$namespace])) {
            throw new RuntimeException("Did not recognise JSON API namespace: $namespace");
        }

        $config = (array) $this->config[$namespace];

        $config = array_replace([
            self::CONFIG_URL_PREFIX => null,
            self::CONFIG_SUPPORTED_EXT => null,
            self::CONFIG_PAGING => null,
        ], $config);

        if ($host) {
            $host = rtrim($host, '/');
            $config[self::CONFIG_URL_PREFIX] = $host . $config[self::CONFIG_URL_PREFIX];
        }

        return $config;
    }

}
