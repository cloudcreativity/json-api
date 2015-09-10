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

namespace CloudCreativity\JsonApi\Object\ResourceIdentifier;

use CloudCreativity\JsonApi\Contracts\Object\ResourceIdentifier\ResourceIdentifierInterface;
use CloudCreativity\JsonApi\Contracts\Object\StandardObjectInterface;
use CloudCreativity\JsonApi\Object\StandardObject;

/**
 * Class ResourceIdentifier
 * @package CloudCreativity\JsonApi
 */
class ResourceIdentifier extends StandardObject implements ResourceIdentifierInterface
{

    const TYPE = 'type';
    const ID = 'id';
    const META = 'meta';

    /**
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        $this->set(static::TYPE, $type);

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        if (!$this->hasType()) {
            throw new \RuntimeException('No type set.');
        }

        return $this->get(static::TYPE);
    }

    /**
     * @return bool
     */
    public function hasType()
    {
        return $this->has(static::TYPE);
    }

    /**
     * @param $typeOrTypes
     * @return bool
     */
    public function isType($typeOrTypes)
    {
        $types = is_array($typeOrTypes) ? $typeOrTypes : [$typeOrTypes];
        $type = $this->get(static::TYPE);

        foreach ($types as $check) {

            if ($type === $check) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $map
     * @return mixed
     */
    public function mapType(array $map)
    {
        $type = $this->getType();

        if (array_key_exists($type, $map)) {
            return $map[$type];
        }

        throw new \RuntimeException(sprintf('Type "%s" is not in the supplied map.', $type));
    }

    /**
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->set(static::ID, $id);

        return $this;
    }

    /**
     * @return string|int
     */
    public function getId()
    {
        if (!$this->hasId()) {
            throw new \RuntimeException('No id set.');
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
     * @return bool
     */
    public function isComplete()
    {
        return $this->hasType() && $this->hasId();
    }

    /**
     * @return StandardObjectInterface
     */
    public function getMeta()
    {
        return new StandardObject($this->get(static::META));
    }

}
