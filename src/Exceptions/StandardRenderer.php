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

use CloudCreativity\JsonApi\Contracts\Encoder\EncoderAwareInterface;
use CloudCreativity\JsonApi\Contracts\Error\ErrorObjectInterface;
use CloudCreativity\JsonApi\Contracts\Stdlib\ConfigurableInterface;
use CloudCreativity\JsonApi\Encoder\EncoderAwareTrait;
use CloudCreativity\JsonApi\Error\ErrorObject;
use Exception;
use Neomerx\JsonApi\Exceptions\BaseRenderer;

/**
 * Class StandardRenderer
 * @package CloudCreativity\JsonApi
 *
 * The configuration array has two keys:
 *
 * StandardRenderer::DEFAULT_STATUS
 *      a default HTTP status to use if one is not set on the renderer or the exception class is not in the map.
 *
 * StandardRenderer::MAP
 *      a key => value array map of exception classes to either a HTTP status code, or an array template for an error
 *      object.
 *
 * Example config:
 *
 * [
 *   StandardRenderer::DEFAULT_STATUS => 501,
 *   StandardRenderer::MAP => [
 *      'FooException' => 404,
 *      'BarException' => [
 *          StandardRenderer::TITLE => 'Teapot',
 *          StandardRenderer::DETAIL => 'This is not a server, it is a teapot.',
 *          StandardRenderer::STATUS => 418,
 *      ]
 *   ]
 * ]
 */
class StandardRenderer extends BaseRenderer implements EncoderAwareInterface, ConfigurableInterface
{

    use EncoderAwareTrait;

    /** Configuration array keys */
    const DEFAULT_STATUS = 'defaultStatus';
    const MAP = 'map';

    /** Error template array keys. */
    const ID = ErrorObjectInterface::ID;
    const LINKS = ErrorObjectInterface::LINKS;
    const STATUS = ErrorObjectInterface::STATUS;
    const CODE = ErrorObjectInterface::CODE;
    const TITLE = ErrorObjectInterface::TITLE;
    const DETAIL = ErrorObjectInterface::DETAIL;
    const SOURCE = ErrorObjectInterface::SOURCE;
    const META = ErrorObjectInterface::META;

    /**
     * @var int
     */
    private $defaultStatus = 500;

    /**
     * @var array
     */
    private $map = [];

    /**
     * @param Exception $exception
     * @return null|string
     */
    public function getContent(Exception $exception)
    {
        $template = $this->getTemplate($exception);

        if (!is_array($template) || !$this->hasEncoder()) {
            return null;
        }

        $error = ErrorObject::create($template);

        return $this
            ->getEncoder()
            ->encodeError($error);
    }

    /**
     * @param array $config
     * @return $this
     */
    public function configure(array $config)
    {
        if (isset($config[static::DEFAULT_STATUS])) {
            $this->defaultStatus = $config[static::DEFAULT_STATUS];
        }

        if (isset($config[static::MAP])) {
            $this->map = (array) $config[static::MAP];
        }

        return $this;
    }

    /**
     * @param Exception $e
     * @return mixed
     */
    public function render(Exception $e)
    {
        if (!$this->getStatusCode()) {
            $this->setStatusCode($e);
        }

        return parent::render($e);
    }

    /**
     * @param Exception $e
     * @return null
     */
    private function getTemplate(Exception $e)
    {
        $exceptionClass = get_class($e);

        return array_key_exists($exceptionClass, $this->map) ? $this->map[$exceptionClass] : null;
    }

    /**
     * @param Exception $e
     * @return $this
     */
    private function setStatusCode(Exception $e)
    {
        $template = $this->getTemplate($e);
        $status = is_array($template) ? ErrorObject::create($template)->getStatus() : $template;

        if ($this->isStatusCode($status)) {
            $this->withStatusCode($status);
        } else {
            $this->withStatusCode($this->defaultStatus);
        }

        return $this;
    }

    /**
     * @param $value
     * @return bool
     */
    private function isStatusCode($value)
    {
        if (!is_scalar($value)) {
            return false;
        }

        return (100 <= $value && 600 > $value);
    }
}
