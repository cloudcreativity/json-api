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

use CloudCreativity\JsonApi\Contracts\Error\SourceObjectInterface;
use CloudCreativity\JsonApi\Object\StandardObject;

/**
 * Class SourceObject
 * @package CloudCreativity\JsonApi
 */
class SourceObject extends StandardObject implements SourceObjectInterface
{

    /**
     * @param $pointer
     * @return $this
     */
    public function setPointer($pointer)
    {
        if ($pointer instanceof \Closure) {
            $pointer = $pointer($this->getPointer());
        }

        $this->set(static::POINTER, $pointer);

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPointer()
    {
        return $this->get(static::POINTER, null);
    }

    /**
     * @param $parameter
     * @return $this
     */
    public function setParameter($parameter)
    {
        $this->set(static::PARAMETER, $parameter);

        return $this;
    }

    /**
     * @return null|string
     */
    public function getParameter()
    {
        return $this->get(static::PARAMETER, null);
    }

    /**
     * @return object
     */
    public function jsonSerialize()
    {
        return $this->getProxy();
    }
}
