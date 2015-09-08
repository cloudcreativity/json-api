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

namespace CloudCreativity\JsonApi\Exceptions\Renderer;

use CloudCreativity\JsonApi\Codec\CodecMatcherAwareTrait;
use CloudCreativity\JsonApi\Contracts\Error\ErrorCollectionInterface;
use CloudCreativity\JsonApi\Contracts\Exceptions\Renderer\ExceptionRendererInterface;
use CloudCreativity\JsonApi\Responses\ResponsesAwareTrait;
use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Contracts\Parameters\Headers\MediaTypeInterface;
use Neomerx\JsonApi\Contracts\Responses\ResponsesInterface;
use Neomerx\JsonApi\Encoder\Encoder;
use Neomerx\JsonApi\Factories\Factory;

/**
 * Class AbstractErrorRenderer
 * @package CloudCreativity\JsonApi
 */
abstract class AbstractExceptionRenderer implements ExceptionRendererInterface
{

    use CodecMatcherAwareTrait,
        ResponsesAwareTrait,
        ExceptionRendererTrait;

    /**
     * @param CodecMatcherInterface $matcher
     * @param ResponsesInterface $responses
     */
    public function __construct(CodecMatcherInterface $matcher, ResponsesInterface $responses)
    {
        $this->setCodecMatcher($matcher)
            ->setResponses($responses);
    }

    /**
     * @param \Exception $e
     * @return ErrorCollectionInterface|ErrorInterface
     */
    abstract public function parse(\Exception $e);

    /**
     * @param \Exception $e
     * @return mixed
     */
    public function render(\Exception $e)
    {
        /** @var EncoderInterface $encoder */
        /** @var MediaTypeInterface $outputMediaType */
        list($encoder, $outputMediaType) = $this->encoder();

        $parsed = $this->parse($e);
        $errors = ($parsed instanceof ErrorCollectionInterface) ? $parsed->getAll() : [$parsed];
        $statusCode = (int) $parsed->getStatus();

        return $this
            ->getResponses()
            ->getResponse(
                $this->isStatusCode($statusCode) ? $statusCode : $this->getStatusCode(),
                $outputMediaType,
                $encoder->encodeErrors($errors),
                $this->getSupportedExtensions(),
                $this->getHeaders()
            );
    }

    /**
     * Ensures an encoder is always returned.
     *
     * As an error can be triggered by there not being a suitable encoder, this method ensures an encoder is always
     * returned so that an encoded response is always returned.
     *
     * @return array
     */
    protected function encoder()
    {
        $matcher = $this->getCodecMatcher();
        $encoder = $matcher->getEncoder();
        $outputMediaType = $matcher->getEncoderRegisteredMatchedType();

        if (!$encoder instanceof EncoderInterface) {
            $factory = new Factory();
            $encoder = new Encoder($factory, []);
            $outputMediaType = $factory->createMediaType(
                MediaTypeInterface::JSON_API_TYPE,
                MediaTypeInterface::JSON_API_SUB_TYPE
            );
        }

        return [$encoder, $outputMediaType];
    }
}
