<?php

/**
 * Copyright 2017 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Hydrator;

use CloudCreativity\JsonApi\Contracts\Object\RelationshipInterface;
use CloudCreativity\JsonApi\Utils\Str;

/**
 * Class RelatedHydratorTrait
 *
 * @package CloudCreativity\JsonApi
 */
trait RelatedHydratorTrait
{

    /**
     * @param $attributeKey
     * @param $attributeValue
     * @param $record
     * @return mixed
     */
    protected function callHydrateRelatedAttribute($attributeKey, $attributeValue, $record)
    {
        $method = $this->methodForRelated($attributeKey);

        if (!$method || !method_exists($this, $method)) {
            return null;
        }

        return call_user_func([$this, $method], $attributeValue, $record);
    }

    /**
     * Hydrate a relationship by invoking a method on this hydrator.
     *
     * @param $relationshipKey
     * @param RelationshipInterface $relationship
     * @param $record
     * @return object[]|null|false
     *      false if no method was invoked, otherwise the return result of the method.
     */
    protected function callHydrateRelatedRelationship($relationshipKey, RelationshipInterface $relationship, $record)
    {
        $method = $this->methodForRelated($relationshipKey);

        if (empty($method) || !method_exists($this, $method)) {
            return false;
        }

        return (array) call_user_func([$this, $method], $relationship, $record);
    }

    /**
     * Return the method name to call for hydrating a related member.
     *
     * If this method returns an empty value, or a value that is not callable, hydration
     * of the the relationship will be skipped.
     *
     * @param $key
     * @return string|null
     */
    protected function methodForRelated($key)
    {
        return sprintf('hydrateRelated%s', Str::classify($key));
    }
}
