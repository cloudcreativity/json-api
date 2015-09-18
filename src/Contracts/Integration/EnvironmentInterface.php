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

namespace CloudCreativity\JsonApi\Contracts\Integration;

use Neomerx\JsonApi\Contracts\Decoder\DecoderInterface;
use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Contracts\Parameters\Headers\MediaTypeInterface;
use Neomerx\JsonApi\Contracts\Parameters\ParametersInterface;
use Neomerx\JsonApi\Contracts\Parameters\SupportedExtensionsInterface;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface;

/**
 * Interface EnvironmentServiceInterface
 * @package CloudCreativity\JsonApi
 */
interface EnvironmentInterface
{

    /**
     * Get the url prefix for links.
     *
     * @return string|null
     */
    public function getUrlPrefix();

    /**
     * Get the schemas for the current request.
     *
     * @return ContainerInterface
     */
    public function getSchemas();

    /**
     * @return bool
     */
    public function hasSchemas();

    /**
     * @return EncoderInterface
     */
    public function getEncoder();

    /**
     * @return bool
     */
    public function hasEncoder();

    /**
     * @return MediaTypeInterface
     */
    public function getEncoderMediaType();

    /**
     * @return DecoderInterface
     */
    public function getDecoder();

    /**
     * @return bool
     */
    public function hasDecoder();

    /**
     * @return MediaTypeInterface
     */
    public function getDecoderMediaType();

    /**
     * @return ParametersInterface
     */
    public function getParameters();

    /**
     * @return bool
     */
    public function hasParameters();

    /**
     * @return SupportedExtensionsInterface|null
     */
    public function getSupportedExtensions();
}
