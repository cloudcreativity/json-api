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

namespace CloudCreativity\JsonApi\Error\Renderer;

use CloudCreativity\JsonApi\Codec\CodecMatcherAwareTrait;
use CloudCreativity\JsonApi\Contracts\Error\ErrorCollectionInterface;
use CloudCreativity\JsonApi\Contracts\Error\Renderer\HttpErrorStatusRendererInterface;
use CloudCreativity\JsonApi\Responses\ResponsesAwareTrait;
use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use Neomerx\JsonApi\Contracts\Parameters\SupportedExtensionsInterface;
use Neomerx\JsonApi\Contracts\Responses\ResponsesInterface;

/**
 * Class AbstractErrorRenderer
 * @package CloudCreativity\JsonApi
 */
abstract class AbstractErrorRenderer implements HttpErrorStatusRendererInterface
{

    use CodecMatcherAwareTrait,
        ResponsesAwareTrait;

    /**
     * @var SupportedExtensionsInterface|null
     */
    protected $_extensions;

    /**
     * @var int|string
     */
    protected $_statusCode;

    /**
     * @param CodecMatcherInterface $matcher
     * @param ResponsesInterface $responses
     * @param SupportedExtensionsInterface|null $extensions
     */
    public function __construct(CodecMatcherInterface $matcher, ResponsesInterface $responses, SupportedExtensionsInterface $extensions = null)
    {
        $this->setCodecMatcher($matcher)
            ->setResponses($responses);

        if ($extensions) {
            $this->setSupportedExtensions($extensions);
        }
    }

    /**
     * @param \Exception $e
     * @return ErrorCollectionInterface|ErrorInterface
     */
    abstract public function parse(\Exception $e);

    /**
     * @param int|string $status
     * @return $this
     */
    public function setStatusCode($status)
    {
        $this->_statusCode = $status;

        return $this;
    }

    /**
     * @return int|string
     */
    public function getStatusCode()
    {
        return $this->hasStatusCode() ? $this->_statusCode : 500;
    }

    /**
     * @return bool
     */
    public function hasStatusCode()
    {
        $status = (int) $this->_statusCode;

        return 500 <= $status && 600 > $status;
    }

    /**
     * @param SupportedExtensionsInterface $extensions
     * @return $this
     */
    public function setSupportedExtensions(SupportedExtensionsInterface $extensions)
    {
        $this->_extensions = $extensions;

        return $this;
    }

    /**
     * @return SupportedExtensionsInterface|null
     */
    public function getSupportedExtensions()
    {
        return $this->_extensions;
    }

    /**
     * @param \Exception $e
     * @return mixed
     */
    public function render(\Exception $e)
    {
        $matcher = $this->getCodecMatcher();
        $encoder = $matcher->getEncoder();
        $outputMediaType = $matcher->getEncoderRegisteredMatchedType();
        $parsed = $this->parse($e);
        $errors = ($parsed instanceof ErrorCollectionInterface) ? $parsed->getAll() : [$parsed];
        $statusCode = $this->hasStatusCode() ? $this->getStatusCode() : $parsed->getStatus();
        $content = $encoder->encodeErrors($errors);

        // just in case $parsed has not returned anything for `getStatus`
        if (!$statusCode) {
            $statusCode = $this->getStatusCode();
        }

        return $this
            ->getResponses()
            ->getResponse($statusCode, $outputMediaType, $content, $this->getSupportedExtensions());
    }
}
