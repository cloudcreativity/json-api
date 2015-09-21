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

namespace CloudCreativity\JsonApi\Integration;

use CloudCreativity\JsonApi\Contracts\Integration\EnvironmentInterface;
use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Contracts\Decoder\DecoderInterface;
use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Contracts\Integration\CurrentRequestInterface;
use Neomerx\JsonApi\Contracts\Integration\ExceptionThrowerInterface;
use Neomerx\JsonApi\Contracts\Parameters\Headers\MediaTypeInterface;
use Neomerx\JsonApi\Contracts\Parameters\ParametersFactoryInterface;
use Neomerx\JsonApi\Contracts\Parameters\ParametersInterface;
use Neomerx\JsonApi\Contracts\Parameters\SupportedExtensionsInterface;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface;
use RuntimeException;

class EnvironmentService implements EnvironmentInterface
{

    /**
     * @var ParametersFactoryInterface
     */
    private $factory;

    /**
     * @var CurrentRequestInterface
     */
    private $currentRequest;

    /**
     * @var ExceptionThrowerInterface
     */
    private $exceptionThrower;

    /**
     * @var string
     */
    private $urlPrefix;

    /**
     * @var ContainerInterface|null
     */
    private $schemas;

    /**
     * @var EncoderInterface|null
     */
    private $encoder;

    /**
     * @var MediaTypeInterface|null
     */
    private $encoderMediaType;

    /**
     * @var DecoderInterface|null
     */
    private $decoder;

    /**
     * @var MediaTypeInterface|null
     */
    private $decoderMediaType;

    /**
     * @var ParametersInterface|null
     */
    private $parameters;

    /**
     * @var SupportedExtensionsInterface|null
     */
    private $supportedExtensions;

    /**
     * @param ParametersFactoryInterface $factory
     * @param CurrentRequestInterface $currentRequest
     * @param ExceptionThrowerInterface $exceptionThrower
     */
    public function __construct(
        ParametersFactoryInterface $factory,
        CurrentRequestInterface $currentRequest,
        ExceptionThrowerInterface $exceptionThrower
    ) {
        $this->factory = $factory;
        $this->currentRequest = $currentRequest;
        $this->exceptionThrower = $exceptionThrower;
    }

    /**
     * @param $urlPrefix
     * @return $this
     */
    public function registerUrlPrefix($urlPrefix)
    {
        $this->urlPrefix = $urlPrefix ?: null;

        return $this;
    }

    /**
     * Get the url prefix for links.
     *
     * @return string|null
     */
    public function getUrlPrefix()
    {
        return $this->urlPrefix;
    }

    /**
     * @param ContainerInterface $schemas
     * @return $this
     */
    public function registerSchemas(ContainerInterface $schemas)
    {
        $this->schemas = $schemas;

        return $this;
    }

    /**
     * Get the schemas for the current request.
     *
     * @return ContainerInterface
     */
    public function getSchemas()
    {
        if (!$this->schemas instanceof ContainerInterface) {
            throw new RuntimeException('No schemas registered.');
        }

        return $this->schemas;
    }

    /**
     * @return bool
     */
    public function hasSchemas()
    {
        return $this->schemas instanceof ContainerInterface;
    }

    /**
     * @param CodecMatcherInterface $codecMatcher
     * @return $this
     */
    public function registerCodecMatcher(CodecMatcherInterface $codecMatcher)
    {
        $this->parameters = $this->factory
            ->createParametersParser()
            ->parse($this->currentRequest, $this->exceptionThrower);

        $this->factory
            ->createHeadersChecker($this->exceptionThrower, $codecMatcher)
            ->checkHeaders($this->parameters);

        $this->encoder = $codecMatcher->getEncoder();
        $this->encoderMediaType = $codecMatcher->getEncoderRegisteredMatchedType();

        $this->decoder = $codecMatcher->getDecoder();
        $this->decoderMediaType = $codecMatcher->getDecoderHeaderMatchedType();

        if ($this->decoder && !$this->decoderMediaType) {
            $this->decoderMediaType = $codecMatcher->getDecoderRegisteredMatchedType();
        }

        return $this;
    }

    /**
     * @return EncoderInterface
     */
    public function getEncoder()
    {
        if (!$this->encoder instanceof EncoderInterface) {
            throw new RuntimeException('No encoder registered. Has a codec matcher been registered?');
        }

        return $this->encoder;
    }

    /**
     * @return bool
     */
    public function hasEncoder()
    {
        return $this->encoder instanceof EncoderInterface;
    }

    /**
     * @return MediaTypeInterface
     */
    public function getEncoderMediaType()
    {
        if (!$this->hasEncoder()) {
            throw new RuntimeException('No encoder registered. Has a codec matcher been registered?');
        }

        return $this->encoderMediaType;
    }

    /**
     * @return DecoderInterface
     */
    public function getDecoder()
    {
        if (!$this->decoder instanceof DecoderInterface) {
            throw new RuntimeException('No decoder registered. Has a codec matcher been registered?');
        }

        return $this->decoder;
    }

    /**
     * @return bool
     */
    public function hasDecoder()
    {
        return $this->decoder instanceof DecoderInterface;
    }

    /**
     * @return MediaTypeInterface
     */
    public function getDecoderMediaType()
    {
        if (!$this->hasDecoder()) {
            throw new RuntimeException('No decoder registered. Has a codec matcher been registered?');
        }

        return $this->decoderMediaType;
    }

    /**
     * @return ParametersInterface
     */
    public function getParameters()
    {
        if (!$this->parameters instanceof ParametersInterface) {
            throw new RuntimeException('No parameters registered. Has JSON API support been initialised for the current request?');
        }

        return $this->parameters;
    }

    /**
     * @return bool
     */
    public function hasParameters()
    {
        return $this->parameters instanceof ParametersInterface;
    }

    /**
     * @param SupportedExtensionsInterface $extensions
     * @return $this
     */
    public function registerSupportedExtensions(SupportedExtensionsInterface $extensions)
    {
        $this->supportedExtensions = $extensions;

        return $this;
    }

    /**
     * @return SupportedExtensionsInterface|null
     */
    public function getSupportedExtensions()
    {
        return $this->supportedExtensions;
    }

}
