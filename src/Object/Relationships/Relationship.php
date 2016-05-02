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

namespace CloudCreativity\JsonApi\Object\Relationships;

use CloudCreativity\JsonApi\Contracts\Object\Relationships\RelationshipInterface;
use CloudCreativity\JsonApi\Contracts\Object\ResourceIdentifier\ResourceIdentifierCollectionInterface;
use CloudCreativity\JsonApi\Contracts\Object\ResourceIdentifier\ResourceIdentifierInterface;
use CloudCreativity\JsonApi\Exceptions\DocumentException;
use CloudCreativity\JsonApi\Object\Meta\MetaMemberTrait;
use CloudCreativity\JsonApi\Object\ResourceIdentifier\ResourceIdentifier;
use CloudCreativity\JsonApi\Object\ResourceIdentifier\ResourceIdentifierCollection;
use CloudCreativity\JsonApi\Object\StandardObject;

/**
 * Class Relationship
 * @package CloudCreativity\JsonApi
 */
class Relationship extends StandardObject implements RelationshipInterface
{

    use MetaMemberTrait;

    /**
     * @return ResourceIdentifierCollectionInterface|ResourceIdentifierInterface|null
     * @deprecated use `data()`
     */
    public function getData()
    {
        return $this->data();
    }

    /**
     * @return bool
     * @deprecated use `isHasOne()`
     */
    public function isBelongsTo()
    {
        return $this->isHasOne();
    }

    /**
     * @return bool
     */
    public function isHasOne()
    {
        if (!$this->has(self::DATA)) {
            return false;
        }

        $data = $this->get(self::DATA);

        return is_null($data) || is_object($data);
    }

    /**
     * @return bool
     */
    public function isHasMany()
    {
        return is_array($this->get(self::DATA));
    }

    /**
     * Get the data member as a correctly casted object.
     *
     * If this is a has-one relationship, a ResourceIdentifierInterface object or null will be returned. If it is
     * a has-many relationship, a ResourceIdentifierCollectionInterface will be returned.
     *
     * @return ResourceIdentifierInterface|ResourceIdentifierCollectionInterface|null
     * @throws DocumentException
     *      if the value for the data member is not a valid relationship value.
     */
    public function data()
    {
        if ($this->isHasOne()) {
            return $this->hasOne();
        } elseif ($this->isHasMany()) {
            return $this->hasMany();
        }

        throw new DocumentException('No data member or data member is not a valid relationship.');
    }

    /**
     * Get the data member as a has-one relationship.
     *
     * @return ResourceIdentifierInterface|null
     * @throws DocumentException
     *      if the data member is not a resource identifier or null.
     */
    public function hasOne()
    {
        if (!$this->isHasOne()) {
            throw new DocumentException('No data member or data member is not a valid has-one relationship.');
        }

        $data = $this->get(self::DATA);

        return ($data) ? new ResourceIdentifier($data) : null;
    }

    /**
     * Get the data member as a has-many relationship.
     *
     * @return ResourceIdentifierCollectionInterface
     * @throws DocumentException
     *      if the data member is not an array.
     */
    public function hasMany()
    {
        if (!$this->isHasMany()) {
            throw new DocumentException('No data member of data member is not a valid has-many relationship.');
        }

        return ResourceIdentifierCollection::create($this->get(self::DATA));
    }

}
