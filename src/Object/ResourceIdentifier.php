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

namespace CloudCreativity\JsonApi\Object;

use CloudCreativity\JsonApi\Contracts\Object\ResourceIdentifierInterface;
use CloudCreativity\JsonApi\Exceptions\DocumentException;
use CloudCreativity\JsonApi\Object\Helpers\IdentifiableTrait;
use CloudCreativity\JsonApi\Object\Helpers\MetaMemberTrait;

/**
 * Class ResourceIdentifier
 * @package CloudCreativity\JsonApi
 */
class ResourceIdentifier extends StandardObject implements ResourceIdentifierInterface
{

    use IdentifiableTrait,
        MetaMemberTrait;

    /**
     * @param $type
     * @param $id
     * @return ResourceIdentifier
     */
    public static function create($type, $id)
    {
        $identifier = new self();

        $identifier->set(self::TYPE, $type)
            ->set(self::ID, $id);

        return $identifier;
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
        $type = $this->type();

        if (array_key_exists($type, $map)) {
            return $map[$type];
        }

        throw new DocumentException(sprintf('Type "%s" is not in the supplied map.', $type));
    }

    /**
     * @return bool
     */
    public function isComplete()
    {
        return $this->hasType() && $this->hasId();
    }

}
