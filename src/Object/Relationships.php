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

use CloudCreativity\JsonApi\Contracts\Object\RelationshipInterface;
use CloudCreativity\JsonApi\Contracts\Object\RelationshipsInterface;
use CloudCreativity\JsonApi\Exceptions\DocumentException;
use Generator;

/**
 * Class Relationships
 * @package CloudCreativity\JsonApi
 */
class Relationships extends StandardObject implements RelationshipsInterface
{

    /**
     * @return Generator
     */
    public function all()
    {
        foreach ($this->keys() as $key) {
            yield $key => $this->rel($key);
        }
    }

    /**
     * Shorthand for `relationship()`
     *
     * @param $key
     * @return RelationshipInterface
     * @throws DocumentException
     */
    public function rel($key)
    {
        return $this->relationship($key);
    }

    /**
     * @param $key
     * @return RelationshipInterface
     * @throws DocumentException
     *      if the key is not present, or is not an object.
     */
    public function relationship($key)
    {
        if (!$this->has($key)) {
            throw new DocumentException("Relationship member '$key' is not present.");
        }

        $value = parent::get($key);

        if (!is_object($value)) {
            throw new DocumentException("Relationship member '$key' is not an object.'");
        }

        return new Relationship($value);
    }

}
