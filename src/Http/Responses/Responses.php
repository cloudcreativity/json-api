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

use Illuminate\Http\Response;
use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\MediaTypeInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\SupportedExtensionsInterface;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface;
use Neomerx\JsonApi\Http\Responses;
use CloudCreativity\JsonApi\Exceptions\RuntimeException;

/**
 * Class Responses
 * @package CloudCreativity\JsonApi
 */
abstract class AbstractResponses extends Responses
{

    private $api;

    private $request;

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
        return $this->request->getEncodingParameters();
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
