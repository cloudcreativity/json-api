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

use Neomerx\JsonApi\Contracts\Parameters\SupportedExtensionsInterface;

/**
 * Class ExceptionRendererTrait
 * @package CloudCreativity\JsonApi
 */
trait ExceptionRendererTrait
{

    /**
     * @var int|null
     */
    protected $_statusCode;

    /**
     * @var array|null
     */
    protected $_headers;

    /**
     * @var SupportedExtensionsInterface|null
     */
    protected $_extensions;

    /**
     * @param $statusCode
     * @return $this
     */
    public function withStatusCode($statusCode)
    {
        $this->_statusCode = (int) $statusCode;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
         return $this->hasStatusCode() ? (int) $this->_statusCode : 500;
    }

    /**
     * @return bool
     */
    public function hasStatusCode()
    {
        return $this->isStatusCode($this->_statusCode);
    }

    /**
     * @param $statusCode
     * @return bool
     */
    public function isStatusCode($statusCode)
    {
        return (400 <= $statusCode && 600 > $statusCode);
    }

    /**
     * @param array $headers
     * @return $this
     */
    public function withHeaders(array $headers)
    {
        $this->_headers = $headers;

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return (array) $this->_headers;
    }

    /**
     * @param SupportedExtensionsInterface $extensions
     * @return $this
     */
    public function withSupportedExtensions(SupportedExtensionsInterface $extensions)
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
}
