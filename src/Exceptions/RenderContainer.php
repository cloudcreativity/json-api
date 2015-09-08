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

namespace CloudCreativity\JsonApi\Exceptions;

use CloudCreativity\JsonApi\Contracts\Exceptions\Renderer\ExceptionRendererInterface;
use CloudCreativity\JsonApi\Contracts\Stdlib\ConfigurableInterface;
use Neomerx\JsonApi\Contracts\Exceptions\RenderContainerInterface;
use Neomerx\JsonApi\Contracts\Parameters\SupportedExtensionsInterface;

/**
 * Class RenderContainer
 * @package CloudCreativity\JsonApi
 */
class RenderContainer implements RenderContainerInterface, ConfigurableInterface
{

    // Config keys
    const HTTP_CODE_MAPPING = 'http-code-mapping';
    const JSON_API_ERROR_MAPPING = 'json-api-error-mapping';

    /**
     * @var array
     */
    protected $_renderers = [];

    /**
     * @var ExceptionRendererInterface|null
     */
    protected $_defaultRenderer;

    /**
     * @var ExceptionRendererInterface|null
     */
    protected $_httpStatusRenderer;

    /**
     * @var ExceptionRendererInterface|null
     */
    protected $_jsonApiErrorRenderer;

    /**
     * @param ExceptionRendererInterface $renderer
     * @return $this
     */
    public function setDefaultRenderer(ExceptionRendererInterface $renderer)
    {
        $this->_defaultRenderer = $renderer;

        return $this;
    }

    /**
     * @return ExceptionRendererInterface
     */
    public function getDefaultRenderer()
    {
        if (!$this->_defaultRenderer instanceof ExceptionRendererInterface) {
            throw new \RuntimeException(sprintf('%s expects to be injected with a %s instance.', static::class, ExceptionRendererInterface::class));
        }

        return $this->_defaultRenderer;
    }

    /**
     * @param ExceptionRendererInterface $renderer
     * @return $this
     */
    public function setHttpStatusRenderer(ExceptionRendererInterface $renderer)
    {
        $this->_httpStatusRenderer = $renderer;

        return $this;
    }

    /**
     * @return ExceptionRendererInterface
     */
    public function getHttpStatusRenderer()
    {
        if (!$this->_httpStatusRenderer instanceof ExceptionRendererInterface) {
            throw new \RuntimeException(sprintf('%s expects to be injected with a %s instance.', static::class, ExceptionRendererInterface::class));
        }

        return $this->_httpStatusRenderer;
    }

    /**
     * @param ExceptionRendererInterface $renderer
     * @return $this
     */
    public function setJsonApiErrorRenderer(ExceptionRendererInterface $renderer)
    {
        $this->_jsonApiErrorRenderer = $renderer;

        return $this;
    }

    /**
     * @return ExceptionRendererInterface
     */
    public function getJsonApiErrorRenderer()
    {
        if (!$this->_jsonApiErrorRenderer instanceof ExceptionRendererInterface) {
            throw new \RuntimeException(sprintf('%s expects to be injected with a %s instance.', static::class, ExceptionRendererInterface::class));
        }

        return $this->_jsonApiErrorRenderer;
    }

    /**
     * Register exception render
     *
     * @param string $exceptionClass
     * @param \Closure $render
     * @return $this
     */
    public function registerRender($exceptionClass, \Closure $render)
    {
        $this->_renderers[$exceptionClass] = $render;

        return $this;
    }

    /**
     * Register HTTP status code mapping for exceptions.
     *
     * @param array $exceptionMapping
     * @return $this
     */
    public function registerHttpCodeMapping(array $exceptionMapping)
    {
        $renderer = $this->getHttpStatusRenderer();

        foreach ($exceptionMapping as $exceptionClass => $statusCode) {
            $this->registerRender($exceptionClass, $this->renderer($renderer, (int) $statusCode));
        }

        return $this;
    }

    /**
     * Register JSON API Error object renders mapping for exceptions.
     *
     * @param array $exceptionMapping
     * @return $this
     */
    public function registerJsonApiErrorMapping(array $exceptionMapping)
    {
        $renderer = $this->getJsonApiErrorRenderer();

        foreach ($exceptionMapping as $exceptionClass) {
            $this->registerRender($exceptionClass, $this->renderer($renderer));
        }

        return $this;
    }

    /**
     * Get registered or default render for exception.
     *
     * @param \Exception $exception
     * @return \Closure
     */
    public function getRender(\Exception $exception)
    {
        $class = get_class($exception);

        if (isset($this->_renderers[$class])) {
            return $this->_renderers[$class];
        }

        return $this->renderer($this->getDefaultRenderer());
    }

    /**
     * @param array $config
     * @return $this
     */
    public function configure(array $config)
    {
        if (isset($config[static::HTTP_CODE_MAPPING]) && is_array($config[static::HTTP_CODE_MAPPING])) {
            $this->registerHttpCodeMapping($config[static::HTTP_CODE_MAPPING]);
        }

        if (isset($config[static::JSON_API_ERROR_MAPPING]) && is_array($config[static::JSON_API_ERROR_MAPPING])) {
            $this->registerJsonApiErrorMapping($config[static::JSON_API_ERROR_MAPPING]);
        }

        return $this;
    }

    /**
     * @param ExceptionRendererInterface $renderer
     * @param int|null $defaultStatusCode
     * @return \Closure
     */
    protected function renderer(ExceptionRendererInterface $renderer, $defaultStatusCode = null)
    {
        /**
         * @param \Exception $e
         * @param int|null $statusCode
         * @param array|null $headers
         * @param SupportedExtensionsInterface|null $extensions
         * @return mixed
         */
        return function (
            \Exception $e,
            $statusCode = null,
            array $headers = null,
            SupportedExtensionsInterface $extensions = null
        ) use ($renderer, $defaultStatusCode) {

            if ($statusCode) {
                $renderer->withStatusCode($statusCode);
            } elseif ($defaultStatusCode) {
                $renderer->withStatusCode($defaultStatusCode);
            }

            if (is_array($headers)) {
                $renderer->withHeaders($headers);
            }

            if ($extensions) {
                $renderer->withSupportedExtensions($extensions);
            }

            return $renderer->render($e);
        };
    }

}
