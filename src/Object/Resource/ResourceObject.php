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
use CloudCreativity\JsonApi\Contracts\Object\Resource\ResourceObjectInterface;
use CloudCreativity\JsonApi\Contracts\Object\ResourceIdentifier\ResourceIdentifierInterface;
use CloudCreativity\JsonApi\Contracts\Object\StandardObjectInterface;
use CloudCreativity\JsonApi\Object\Relationships\Relationships;
use CloudCreativity\JsonApi\Object\ResourceIdentifier\ResourceIdentifier;
use CloudCreativity\JsonApi\Object\StandardObject;

/**
 * Class Resource
 * @package CloudCreativity\JsonApi
 */
class ResourceObject extends StandardObject implements ResourceObjectInterface
{

    const TYPE = 'type';
    const ID = 'id';
    const ATTRIBUTES = 'attributes';
    const RELATIONSHIPS = 'relationships';
    const META = 'meta';

    /**
     * @return string
     */
    public function getType()
    {
        $type = $this->get(static::TYPE);

        if (!is_string($type) || empty($type)) {
            throw new \RuntimeException('No resource object type set.');
        }

        return $type;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        if (!$this->hasId()) {
            throw new \RuntimeException('No resource object id set.');
        }

        return $this->get(static::ID);
    }

    /**
     * @return bool
     */
    public function hasId()
    {
        return $this->has(static::ID);
    }

    /**
     * @return ResourceIdentifierInterface
     */
    public function getIdentifier()
    {
        $identifier = new ResourceIdentifier();
        $identifier->setType($this->getType());

        if ($this->hasId()) {
            $identifier->setId($this->getId());
        }

        return $identifier;
    }

    /**
     * @return StandardObjectInterface
     */
    public function getAttributes()
    {
        return new StandardObject($this->get(static::ATTRIBUTES));
    }

    /**
     * @return bool
     */
    public function hasAttributes()
    {
        return $this->has(static::ATTRIBUTES);
    }

    /**
     * @return RelationshipsInterface
     */
    public function getRelationships()
    {
        return new Relationships($this->get(static::RELATIONSHIPS));
    }

    /**
     * @return bool
     */
    public function hasRelationships()
    {
        return $this->has(static::RELATIONSHIPS);
    }

    /**
     * @return StandardObjectInterface
     */
    public function getMeta()
    {
        return new StandardObject($this->get(static::META));
    }

    /**
     * @return bool
     */
    public function hasMeta()
    {
        return $this->has(static::META);
    }
}
