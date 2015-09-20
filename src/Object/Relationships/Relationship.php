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
use CloudCreativity\JsonApi\Object\ResourceIdentifier\ResourceIdentifier;
use CloudCreativity\JsonApi\Object\ResourceIdentifier\ResourceIdentifierCollection;
use CloudCreativity\JsonApi\Object\StandardObject;

/**
 * Class Relationship
 * @package CloudCreativity\JsonApi
 */
class Relationship extends StandardObject implements RelationshipInterface
{

    const DATA = 'data';
    const META = 'meta';

    /**
     * @return ResourceIdentifier|ResourceIdentifierCollection|null
     */
    public function getData()
    {
        $data = $this->get(static::DATA);

        if (is_null($data)) {
            return null;
        } elseif (is_object($data)) {
            return new ResourceIdentifier($data);
        } elseif (is_array($data)) {
            return ResourceIdentifierCollection::create($data);
        }

        throw new \RuntimeException('Invalid data value on Relationship.');
    }

    /**
     * @inheritdoc
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
        if (!$this->has(static::DATA)) {
            return false;
        }

        $data = $this->get(static::DATA);

        return is_null($data) || is_object($data);
    }

    /**
     * @return bool
     */
    public function isHasMany()
    {
        return is_array($this->get(static::DATA));
    }

    /**
     * @return StandardObject
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
