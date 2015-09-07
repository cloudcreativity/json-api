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

namespace CloudCreativity\JsonApi\Error;

use CloudCreativity\JsonApi\Contracts\Exceptions\Renderer\ErrorRendererInterface;
use CloudCreativity\JsonApi\Contracts\Exceptions\Renderer\HttpErrorStatusRendererInterface;
use Neomerx\JsonApi\Contracts\Exceptions\RenderContainerInterface;

/**
 * Class RenderContainer
 * @package CloudCreativity\JsonApi
 */
class RenderContainer implements RenderContainerInterface
{

    /**
     * @var array
     */
    protected $_renderers = [];

    /**
     * @var ErrorRendererInterface|null
     */
    protected $_defaultRenderer;

    /**
     * @var HttpErrorStatusRendererInterface|null
     */
    protected $_httpStatusRenderer;

    /**
     * @var ErrorRendererInterface|null
     */
    protected $_jsonApiErrorRenderer;

    /**
     * @param ErrorRendererInterface $renderer
     * @return $this
     */
    public function setDefaultRenderer(ErrorRendererInterface $renderer)
    {
        $this->_defaultRenderer = $renderer;

        return $this;
    }

    /**
     * @return ErrorRendererInterface
     */
    public function getDefaultRenderer()
    {
        if (!$this->_defaultRenderer instanceof ErrorRendererInterface) {
            throw new \RuntimeException(sprintf('%s expects to be injected with a %s instance.', static::class, ErrorRendererInterface::class));
        }

        return $this->_defaultRenderer;
    }

    /**
     * @param HttpErrorStatusRendererInterface $renderer
     * @return $this
     */
    public function setHttpStatusRenderer(HttpErrorStatusRendererInterface $renderer)
    {
        $this->_httpStatusRenderer = $renderer;

        return $this;
    }

    /**
     * @return HttpErrorStatusRendererInterface
     */
    public function getHttpStatusRenderer()
    {
        if (!$this->_httpStatusRenderer instanceof HttpErrorStatusRendererInterface) {
            throw new \RuntimeException(sprintf('%s expects to be injected with a %s instance.', static::class, HttpErrorStatusRendererInterface::class));
        }

        return $this->_httpStatusRenderer;
    }

    /**
     * @param ErrorRendererInterface $renderer
     * @return $this
     */
    public function setJsonApiErrorRenderer(ErrorRendererInterface $renderer)
    {
        $this->_jsonApiErrorRenderer = $renderer;

        return $this;
    }

    /**
     * @return ErrorRendererInterface
     */
    public function getJsonApiErrorRenderer()
    {
        if (!$this->_jsonApiErrorRenderer instanceof ErrorRendererInterface) {
            throw new \RuntimeException(sprintf('%s expects to be injected with a %s instance.', static::class, ErrorRendererInterface::class));
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
        foreach ($exceptionMapping as $exceptionClass => $statusCode) {
            $this->registerRender($exceptionClass, $this->getHttpCodeRenderer($statusCode));
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
        foreach ($exceptionMapping as $exceptionClass) {
            $this->registerRender($exceptionClass, $this->getErrorsRenderer());
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

        return function (\Exception $e) {
            return $this
                ->getDefaultRenderer()
                ->render($e);
        };
    }

    /**
     * @param $statusCode
     * @return \Closure
     */
    protected function getHttpCodeRenderer($statusCode)
    {
        return function (\Exception $e) use ($statusCode) {

            return $this
                ->getHttpStatusRenderer()
                ->setStatusCode($statusCode)
                ->render($e);
        };
    }

    /**
     * @return \Closure
     */
    protected function getErrorsRenderer()
    {
        return function (\Exception $e) {
            return $this->getJsonApiErrorRenderer()
                ->render($e);
        };
    }

}
