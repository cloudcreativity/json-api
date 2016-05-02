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

namespace CloudCreativity\JsonApi\Object\Document;

use CloudCreativity\JsonApi\Contracts\Object\Document\DocumentInterface;
use CloudCreativity\JsonApi\Contracts\Object\Relationships\RelationshipInterface;
use CloudCreativity\JsonApi\Contracts\Object\Resource\ResourceInterface;
use CloudCreativity\JsonApi\Contracts\Object\StandardObjectInterface;
use CloudCreativity\JsonApi\Exceptions\DocumentException;
use CloudCreativity\JsonApi\Object\Meta\MetaMemberTrait;
use CloudCreativity\JsonApi\Object\Relationships\Relationship;
use CloudCreativity\JsonApi\Object\Resource\Resource;
use CloudCreativity\JsonApi\Object\StandardObject;

/**
 * Class Document
 * @package CloudCreativity\JsonApi
 */
class Document extends StandardObject implements DocumentInterface
{

    use MetaMemberTrait;

    /**
     * @return StandardObjectInterface
     * @deprecated use `data()`
     */
    public function getData()
    {
        return $this->data();
    }

    /**
     * @return ResourceInterface
     * @deprecated use `resource()`
     */
    public function getResourceObject()
    {
        return $this->resource();
    }

    /**
     * @return Relationship
     * @deprecated use `relationship()`
     */
    public function getRelationship()
    {
        return $this->relationship();
    }

    /**
     * @return StandardObjectInterface
     */
    public function data()
    {
        if (!$this->has(self::DATA)) {
            throw new DocumentException('Data member is not present.');
        }

        $data = $this->get(self::DATA);

        if (!is_object($data)) {
            throw new DocumentException('Data member is not an object or null.');
        }

        return new StandardObject($data);
    }

    /**
     * Get the data member as a resource object.
     *
     * @return ResourceInterface
     * @throws DocumentException
     *      if the data member is not an object or is not present.
     */
    public function resource()
    {
        /** @var StandardObject $data */
        $data = $this->data();

        return new Resource($data->getProxy());
    }

    /**
     * Get the data member as a relationship.
     *
     * @return RelationshipInterface
     * @throws DocumentException
     *      if the data member is not an object or null, or is not present.
     */
    public function relationship()
    {
        if (!$this->has(self::DATA)) {
            throw new DocumentException('Data member is not present.');
        }

        $data = $this->get(self::DATA);

        if (!is_object($data) && !is_null($data)) {
            throw new DocumentException('Data member is not an object or null.');
        }

        return new Relationship($data);
    }
}
