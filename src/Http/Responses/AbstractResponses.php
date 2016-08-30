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

use CloudCreativity\JsonApi\Contracts\Http\ApiInterface;
use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterface;
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
     * @var ApiInterface
     */
    private $api;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * AbstractResponses constructor.
     * @param ApiInterface $api
     * @param RequestInterface $request
     */
    public function __construct(ApiInterface $api, RequestInterface $request)
    {
        $this->api = $api;
        $this->request = $request;
    }

    /**
     * @inheritdoc
     */
    protected function getEncoder()
    {
        return $this->api->getEncoder();
    }

    /**
     * @inheritdoc
     */
    protected function getUrlPrefix()
    {
        return $this->api->getUrlPrefix();
    }

    /**
     * @inheritdoc
     */
    protected function getEncodingParameters()
    {
        return $this->request->getParameters();
    }

    /**
     * @inheritdoc
     */
    protected function getSchemaContainer()
    {
        return $this->api->getSchemas();
    }

    /**
     * @inheritdoc
     */
    protected function getSupportedExtensions()
    {
        return $this->api->getSupportedExts();
    }

    /**
     * @inheritdoc
     */
    protected function getMediaType()
    {
        $type = $this
            ->api
            ->getCodecMatcher()
            ->getEncoderRegisteredMatchedType();

        if (!$type instanceof MediaTypeInterface) {
            throw new RuntimeException('No matching media type for encoded JSON-API response.');
        }

        return $type;
    }

}
