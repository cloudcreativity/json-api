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

namespace CloudCreativity\JsonApi\Object\Resource;

use CloudCreativity\JsonApi\Contracts\Object\Relationships\RelationshipsInterface;
use CloudCreativity\JsonApi\Contracts\Object\Resource\ResourceInterface;
use CloudCreativity\JsonApi\Contracts\Object\ResourceIdentifier\ResourceIdentifierInterface;
use CloudCreativity\JsonApi\Contracts\Object\StandardObjectInterface;
use CloudCreativity\JsonApi\Exceptions\DocumentException;
use CloudCreativity\JsonApi\Object\Meta\MetaMemberTrait;
use CloudCreativity\JsonApi\Object\Relationships\Relationships;
use CloudCreativity\JsonApi\Object\ResourceIdentifier\IdentifiableTrait;
use CloudCreativity\JsonApi\Object\ResourceIdentifier\ResourceIdentifier;
use CloudCreativity\JsonApi\Object\StandardObject;

/**
 * Class Resource
 * @package CloudCreativity\JsonApi
 */
class Resource extends StandardObject implements ResourceInterface
{

    use IdentifiableTrait,
        MetaMemberTrait;

    /**
     * @return ResourceIdentifierInterface
     * @deprecated use `identifier()`
     */
    public function getIdentifier()
    {
        return $this->identifier();
    }

    /**
     * @return StandardObjectInterface
     * @deprecated use `attributes()`
     */
    public function getAttributes()
    {
        return $this->attributes();
    }

    /**
     * @return bool
     */
    public function hasAttributes()
    {
        return $this->has(self::ATTRIBUTES);
    }

    /**
     * @return RelationshipsInterface
     * @deprecated use `relationships()`
     */
    public function getRelationships()
    {
        return $this->relationships();
    }

    /**
     * @return bool
     */
    public function hasRelationships()
    {
        return $this->has(self::RELATIONSHIPS);
    }

    /**
     * Get the type and id members as a resource identifier object.
     *
     * @return ResourceIdentifierInterface
     * @throws DocumentException
     *      if the type and/or id members are not valid.
     */
    public function identifier()
    {
        return ResourceIdentifier::create($this->type(), $this->id());
    }

    /**
     * @return StandardObjectInterface
     * @throws DocumentException
     *      if the attributes member is present and is not an object.
     */
    public function attributes()
    {
        $attributes = $this->get(self::ATTRIBUTES);

        if ($this->has(self::ATTRIBUTES) && !is_object($attributes)) {
            throw new DocumentException('Attributes member is not an object.');
        }

        return new StandardObject($attributes);
    }

    /**
     * @return RelationshipsInterface
     * @throws DocumentException
     *      if the relationships member is present and is not an object.
     */
    public function relationships()
    {
        $relationships = $this->get(self::RELATIONSHIPS);

        if ($this->has(self::RELATIONSHIPS) && !is_object($relationships)) {
            throw new DocumentException('Relationships member is not an object.');
        }

        return new Relationships($relationships);
    }

}
