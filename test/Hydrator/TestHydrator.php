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

use CloudCreativity\JsonApi\Contracts\Hydrator\HydratesRelatedInterface;
use CloudCreativity\JsonApi\Contracts\Object\RelationshipInterface;
use CloudCreativity\JsonApi\Contracts\Object\ResourceObjectInterface;
use CloudCreativity\JsonApi\Object\StandardObject;
use CloudCreativity\JsonApi\Utils\Str;

/**
 * Class TestHydrator
 *
 * @package CloudCreativity\JsonApi
 */
class TestHydrator extends AbstractHydrator implements HydratesRelatedInterface
{

    use HydratesAttributesTrait, RelatedHydratorTrait;

    /**
     * The attributes that can be hydrated
     *
     * @var array|null
     */
    public $attributes;

    /**
     * Attributes to cast as dates
     *
     * @var array|null
     */
    public $dates;

    /**
     * @inheritDoc
     */
    protected function hydrateAttribute($record, $attrKey, $value)
    {
        $record->{$attrKey} = $value;
    }

    /**
     * @inheritDoc
     * @todo an equivalent method for this needs to be on the `RelatedHydratorTrait`
     * @see https://github.com/cloudcreativity/json-api/issues/28
     */
    public function hydrateRelated(ResourceObjectInterface $resource, $record)
    {
        $results = [];
        $attributes = $resource->getAttributes();

        /**
         * Have to iterate over keys because the standard object iteration has a bug.
         *
         * @see https://github.com/cloudcreativity/json-api/issues/30
         * @todo change this when that issue is fixed.
         */
        foreach ($attributes->keys() as $key) {
            $results[] = $this->callHydrateRelatedAttribute($key, $attributes->get($key), $record);
        }

        /** @var RelationshipInterface $relationship */
        foreach ($resource->getRelationships()->getAll() as $key => $relationship) {
            $result = $this->callHydrateRelated($key, $relationship, $record);

            if (is_array($result)) {
                $results = array_merge($results, $result);
            }
        }

        return array_values(array_filter($results));
    }

    /**
     * @param RelationshipInterface $relationship
     * @param $record
     */
    protected function hydrateUserRelationship(RelationshipInterface $relationship, $record)
    {
        $record->user_id = $relationship->getIdentifier()->getId();
    }

    /**
     * @param RelationshipInterface $relationship
     * @param $record
     */
    protected function hydrateLatestTagsRelationship(RelationshipInterface $relationship, $record)
    {
        $record->tag_ids = $relationship->getIdentifiers()->getIds();
    }

    /**
     * @param RelationshipInterface $relationship
     * @return array
     */
    protected function hydrateRelatedLinkedPosts(RelationshipInterface $relationship)
    {
        $arr = [];

        foreach ($relationship->getData()->getAll() as $identifier) {
            $arr[] = (object) [
                'type' => $identifier->getType(),
                'id' => $identifier->getId(),
                'title' => sprintf('Post %d', $identifier->getId()),
            ];
        }

        return $arr;
    }

    /**
     * @param StandardObject $object
     * @param $record
     * @return object
     */
    protected function hydrateRelatedAuthor(StandardObject $object, $record)
    {
        return $object->transformKeys(function ($key) {
            return Str::underscore($key);
        })->getProxy();
    }
}
