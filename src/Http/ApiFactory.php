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
use CloudCreativity\JsonApi\Contracts\Http\ApiInterface;
use CloudCreativity\JsonApi\Contracts\Repositories\CodecMatcherRepositoryInterface;
use CloudCreativity\JsonApi\Contracts\Repositories\SchemasRepositoryInterface;
use CloudCreativity\JsonApi\Contracts\Store\StoreInterface;
use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterpreterInterface;
use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\SupportedExtensionsInterface;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface as SchemaContainerInterface;
use Neomerx\JsonApi\Http\Headers\SupportedExtensions;

/**
 * Class ApiFactory
 * @package CloudCreativity\JsonApi
 */
class ApiFactory implements ApiFactoryInterface
{

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
     * ApiFactory constructor.
     * @param CodecMatcherRepositoryInterface $codecMatcherRespository
     * @param SchemasRepositoryInterface $schemasRepository
     * @param StoreInterface $store
     * @todo support a store on a per-api basis.
     */
    public function __construct(
        CodecMatcherRepositoryInterface $codecMatcherRespository,
        SchemasRepositoryInterface $schemasRepository,
        StoreInterface $store,
        RequestInterpreterInterface $interpreter
    ) {
        $this->codecMatcherRepository = $codecMatcherRespository;
        $this->schemasRepository = $schemasRepository;
        $this->store = $store;
        $this->requestInterpreter = $interpreter;
    }

    /**
     * @param $namespace
     * @param array $config
     * @return ApiInterface
     */
    public function createApi(
        $namespace,
        array $config
    ) {
        $config = $this->normalizeConfig($config);
        $urlPrefix = $config[self::CONFIG_URL_PREFIX] ?: null;
        $schemas = $this->createSchemas($namespace);
        $codecMatcher = $this->createCodecMatcher($schemas, $urlPrefix);
        $supportedExt = $this->createSupportedExt($config[self::CONFIG_SUPPORTED_EXT]);

        return new Api(
            $namespace,
            $this->requestInterpreter,
            $codecMatcher,
            $schemas,
            $this->store,
            $urlPrefix,
            $supportedExt
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
     * @return array
     */
    protected function normalizeConfig(array $config)
    {
        return array_replace([
            self::CONFIG_URL_PREFIX => null,
            self::CONFIG_SUPPORTED_EXT => null,
        ], $config);
    }

}
