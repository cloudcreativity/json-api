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
use CloudCreativity\JsonApi\Contracts\Object\ResourceObjectInterface;

/**
 * Class TestHydrator
 *
 * @package CloudCreativity\JsonApi
 */
class TestHydrator extends AbstractHydrator
{

    use HydratesAttributesTrait;

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
    public function delete($record)
    {
        $record->destroyed = true;

        return true;
    }

    /**
     * @inheritdoc
     */
    public function updateRelationship($relationshipKey, RelationshipInterface $relationship, $record)
    {
        $this->callMethodForField($relationshipKey, $relationship, $record);
    }

    /**
     * @inheritDoc
     */
    public function addToRelationship($relationshipKey, RelationshipInterface $relationship, $record)
    {
        $this->callMethodForAddToField($relationshipKey, $relationship, $record);
    }

    /**
     * @inheritDoc
     */
    public function removeFromRelationship($relationshipKey, RelationshipInterface $relationship, $record)
    {
        $this->callMethodForRemoveFromField($relationshipKey, $relationship, $record);
    }

    /**
     * @inheritDoc
     */
    protected function createRecord(ResourceObjectInterface $resource)
    {
        $id = $resource->getId();

        return (object) compact('id');
    }

    /**
     * @inheritDoc
     */
    protected function hydrateAttribute($record, $attrKey, $value)
    {
        $record->{$attrKey} = $value;
    }

    /**
     * @param $record
     * @param $value
     */
    protected function hydrateTitleField($record, $value)
    {
        $record->title = ucwords($value);
    }

    /**
     * @param RelationshipInterface $relationship
     * @param $record
     */
    protected function hydrateUserField(RelationshipInterface $relationship, $record)
    {
        $record->user_id = $relationship->getIdentifier()->getId();
    }

    /**
     * @param RelationshipInterface $relationship
     * @param $record
     */
    protected function hydrateLatestTagsField(RelationshipInterface $relationship, $record)
    {
        $record->tag_ids = $relationship->getIdentifiers()->getIds();
    }

    /**
     * @param RelationshipInterface $relationship
     * @param $record
     */
    protected function addToLatestTagsField(RelationshipInterface $relationship, $record)
    {
        $tags = $this->store()->findMany($relationship->getIdentifiers());
        $record->tags = $tags;
    }

    /**
     * @param RelationshipInterface $relationship
     * @param $record
     */
    protected function removeFromLatestTagsField(RelationshipInterface $relationship, $record)
    {
        $remove = $this->store()->findMany($relationship->getIdentifiers());

        $record->tags = array_values(array_filter($record->tags, function ($tag) use ($remove) {
            return !in_array($tag, $remove, true);
        }));
    }

    /**
     * @inheritDoc
     */
    protected function persist($record)
    {
        if (!isset($record->id)) {
            $record->id = 'new';
        }

        $record->saved = true;
    }

}
