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

use CloudCreativity\JsonApi\Contracts\Error\ErrorCollectionInterface;
use CloudCreativity\JsonApi\Contracts\Error\ErrorObjectInterface;
use CloudCreativity\JsonApi\Contracts\Error\ErrorsAwareInterface;
use CloudCreativity\JsonApi\Error\ErrorObject;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;

/**
 * Class StandardErrorRenderer
 * @package CloudCreativity\JsonApi
 */
class StandardExceptionRenderer extends AbstractExceptionRenderer
{

    /**
     * @var ErrorObjectInterface|null
     */
    protected $_prototype;

    /**
     * @param ErrorObjectInterface $error
     * @return $this
     */
    public function setPrototype(ErrorObjectInterface $error)
    {
        $this->_prototype = $error;

        return $this;
    }

    /**
     * @return ErrorObjectInterface
     */
    public function getPrototype()
    {
        if ($this->_prototype instanceof ErrorObjectInterface) {
            return clone $this->_prototype;
        }

        return new ErrorObject([
            ErrorObject::TITLE => 'Error',
        ]);
    }

    /**
     * @param \Exception $e
     * @return ErrorCollectionInterface|ErrorInterface
     */
    public function parse(\Exception $e)
    {
        if ($e instanceof ErrorsAwareInterface) {
            return $e->getErrors();
        } elseif ($e instanceof ErrorInterface) {
            return $e;
        }

        $error = clone $this->getPrototype();

        if (!$error->getStatus()) {
            $error->setStatus($this->getStatusCode());
        }

        return $error;
    }
}
