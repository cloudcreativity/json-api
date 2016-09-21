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

namespace CloudCreativity\JsonApi\Http\Responses;

use CloudCreativity\JsonApi\Contracts\Http\HttpServiceInterface;
use CloudCreativity\JsonApi\Exceptions\RuntimeException;
use Neomerx\JsonApi\Contracts\Http\Headers\MediaTypeInterface;
use Neomerx\JsonApi\Http\Responses;

/**
 * Class Responses
 * @package CloudCreativity\JsonApi
 */
abstract class AbstractResponses extends Responses
{

    /**
     * @var HttpServiceInterface
     */
    private $service;

    /**
     * AbstractResponses constructor.
     * @param HttpServiceInterface $service
     */
    public function __construct(HttpServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * @inheritdoc
     */
    protected function getEncoder()
    {
        return $this->service->getApi()->getEncoder();
    }

    /**
     * @inheritdoc
     */
    protected function getUrlPrefix()
    {
        return $this->service->getApi()->getUrlPrefix();
    }

    /**
     * @inheritdoc
     */
    protected function getEncodingParameters()
    {
        if (!$this->service->hasRequest()) {
            return null;
        }

        return $this->service->getRequest()->getParameters();
    }

    /**
     * @inheritdoc
     */
    protected function getSchemaContainer()
    {
        return $this->service->getApi()->getSchemas();
    }

    /**
     * @inheritdoc
     */
    protected function getSupportedExtensions()
    {
        return $this->service->getApi()->getSupportedExts();
    }

    /**
     * @inheritdoc
     */
    protected function getMediaType()
    {
        $type = $this
            ->service
            ->getApi()
            ->getCodecMatcher()
            ->getEncoderRegisteredMatchedType();

        if (!$type instanceof MediaTypeInterface) {
            throw new RuntimeException('No matching media type for encoded JSON-API response.');
        }

        return $type;
    }

}
