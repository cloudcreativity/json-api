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

namespace CloudCreativity\JsonApi\Hydrator;

use CloudCreativity\JsonApi\Contracts\Object\RelationshipInterface;
use CloudCreativity\JsonApi\Contracts\Object\StandardObjectInterface;

final class TestHydrator extends AbstractHydrator
{

    /**
     * @param StandardObjectInterface $attributes
     * @param $record
     * @return void
     */
    protected function hydrateAttributes(StandardObjectInterface $attributes, $record)
    {
        foreach ($attributes as $key => $value) {
            $record->{$key} = $value;
        }
    }

    /**
     * Return the method name to call for hydrating the specific relationship.
     *
     * If this method returns an empty value, or a value that is not callable, hydration
     * of the the relationship will be skipped.
     *
     * @param $key
     * @return string|null
     */
    protected function methodForRelationship($key)
    {
        return sprintf('hydrate%sRelationship', ucfirst($key));
    }

    /**
     * @param RelationshipInterface $relationship
     * @param $record
     */
    protected function hydrateUserRelationship(RelationshipInterface $relationship, $record)
    {
        $record->user_id = $relationship->hasOne()->id();
    }

    /**
     * @param RelationshipInterface $relationship
     * @param $record
     */
    protected function hydrateTagsRelationship(RelationshipInterface $relationship, $record)
    {
        $record->tag_ids = $relationship->hasMany()->ids();
    }

}
