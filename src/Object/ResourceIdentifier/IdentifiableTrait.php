<?php

/**
 * Copyright 2016 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Object\ResourceIdentifier;

use CloudCreativity\JsonApi\Exceptions\DocumentException;
use Neomerx\JsonApi\Contracts\Document\DocumentInterface;

trait IdentifiableTrait
{

    /**
     * @param $key
     * @param $default
     * @return mixed
     */
    abstract public function get($key, $default = null);

    /**
     * @param $key
     * @return bool
     */
    abstract public function has($key);

    /**
     * @return string
     * @throws DocumentException
     *      if the type member is not present, or is not a string, or is an empty string.
     */
    public function type()
    {
        if (!$this->has(DocumentInterface::KEYWORD_TYPE)) {
            throw new DocumentException('Type member not present.');
        }

        $type = $this->get(DocumentInterface::KEYWORD_TYPE);

        if (!is_string($type) || empty($type)) {
            throw new DocumentException('Type member is not a string, or is empty.');
        }

        return $type;
    }

    /**
     * @return bool
     */
    public function hasType()
    {
        return $this->has(DocumentInterface::KEYWORD_TYPE);
    }

    /**
     * @return string|int
     * @throws DocumentException
     *      if the id member is not present, or is not a string/int, or is an empty string.
     */
    public function id()
    {
        if (!$this->has(DocumentInterface::KEYWORD_ID)) {
            throw new DocumentException('Id member not present.');
        }

        $id = $this->get(DocumentInterface::KEYWORD_ID);

        if (!is_string($id) && !is_int($id)) {
            throw new DocumentException('Id member is not a string or integer.');
        }

        if (is_string($id) && empty($id)) {
            throw new DocumentException('Id member is an empty string.');
        }

        return $id;
    }

    /**
     * @return bool
     */
    public function hasId()
    {
        return $this->has(DocumentInterface::KEYWORD_ID);
    }

    /**
     * @return string
     * @deprecated use `type()`
     */
    public function getType()
    {
        return $this->type();
    }

    /**
     * @return int|string
     * @deprecated use `id()`
     */
    public function getId()
    {
        return $this->id();
    }
}
