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
use CloudCreativity\JsonApi\Contracts\Repositories\CodecMatcherRepositoryInterface;
use Neomerx\JsonApi\Contracts\Decoder\DecoderInterface;
use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Contracts\Integration\CurrentRequestInterface;
use Neomerx\JsonApi\Contracts\Integration\ExceptionThrowerInterface;
use Neomerx\JsonApi\Contracts\Parameters\Headers\MediaTypeInterface;
use Neomerx\JsonApi\Contracts\Parameters\ParametersFactoryInterface;
use Neomerx\JsonApi\Contracts\Parameters\ParametersInterface;
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
     * Initialise JSON API support for the current request.
     *
     * An application should initialise support on the routes that are JSON API endpoints. Alternatively, if the whole
     * application is a JSON API, then this can be initialised on every request.
     *
     * The initialisation process configures the JSON API environment based on the supplied codec matcher repository.
     * It will check request headers and ensure that an encoder is matched to the accept header, and if there is a
     * content-type header, that a decoder matches this as well.
     *
     * @param CodecMatcherRepositoryInterface $repository
     * @param $inclusionResourceType
     * @return $this
     */
    public function init(CodecMatcherRepositoryInterface $repository, $inclusionResourceType)
    {
        $codecMatcher = $repository->getCodecMatcher();
        $this->urlPrefix = $repository->getUrlPrefix();
        $this->schemas = $repository->getSchemas();

        $this->parameters = $this->factory
            ->createParametersParser()
            ->parse($inclusionResourceType, $this->currentRequest, $this->exceptionThrower);

        $this->factory
            ->createHeadersChecker($this->exceptionThrower, $codecMatcher)
            ->checkHeaders($this->parameters);

        $this->encoder = $codecMatcher->getEncoder();
        $this->encoderMediaType = $codecMatcher->getEncoderHeaderMatchedType();

        if (!$this->encoderMediaType) {
            $this->encoderMediaType = $codecMatcher->getEncoderRegisteredMatchedType();
        }

        $this->decoder = $codecMatcher->getDecoder();
        $this->decoderMediaType = $codecMatcher->getDecoderHeaderMatchedType();

        if ($this->decoder && !$this->decoderMediaType) {
            $this->decoderMediaType = $codecMatcher->getDecoderRegisteredMatchedType();
        }

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
     * Get the schemas for the current request.
     *
     * @return ContainerInterface
     */
    public function getSchemas()
    {
        if (!$this->schemas instanceof ContainerInterface) {
            throw new RuntimeException('No schemas registered. Has JSON API support been initialised for the current request?');
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
     * @return EncoderInterface
     */
    public function getEncoder()
    {
        if (!$this->encoder instanceof EncoderInterface) {
            throw new RuntimeException('No encoder registered. Has JSON API support been initialised for the current request?');
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
            throw new RuntimeException('No encoder registered. Has JSON API support been initialised for the current request?');
        }

        return $this->encoderMediaType;
    }

    /**
     * @return DecoderInterface
     */
    public function getDecoder()
    {
        if (!$this->decoder instanceof DecoderInterface) {
            throw new RuntimeException('No decoder registered. Has JSON API support been initialised for the current request, and has the current request supplied content?');
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
            throw new RuntimeException('No decoder registered. Has JSON API support been initialised for the current request, and has the current request supplied content?');
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

}
