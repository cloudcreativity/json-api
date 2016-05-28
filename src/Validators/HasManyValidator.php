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

namespace CloudCreativity\JsonApi\Validators;

use CloudCreativity\JsonApi\Contracts\Object\RelationshipInterface;
use CloudCreativity\JsonApi\Contracts\Object\ResourceIdentifierCollectionInterface;
use CloudCreativity\JsonApi\Contracts\Object\ResourceIdentifierInterface;
use CloudCreativity\JsonApi\Contracts\Object\ResourceInterface;
use CloudCreativity\JsonApi\Validators\ValidationKeys as Keys;

class HasManyValidator extends AbstractRelationshipValidator
{

    /**
     * Is the provided relationship valid?
     *
     * @param RelationshipInterface $relationship
     * @param ResourceInterface|null $resource
     *      if a full resource is being validated, the resource for context.
     * @return bool
     */
    public function isValid(RelationshipInterface $relationship, ResourceInterface $resource = null)
    {
        $this->reset();

        if (!$this->validateRelationship($relationship)) {
            return false;
        }

        if (!$this->validateHasMany($relationship)) {
            return false;
        }

        $identifiers = $relationship->hasMany();

        if (!$this->validateEmpty($identifiers)) {
            return false;
        }

        if (!$this->validateIdentifiers($identifiers, $resource)) {
            return false;
        }

        return true;
    }

    /**
     * @param RelationshipInterface $relationship
     * @return bool
     */
    protected function validateHasMany(RelationshipInterface $relationship)
    {
        if (!$relationship->isHasMany()) {
            $this->addDataError(Keys::RELATIONSHIP_INVALID, [
                ':relationship' => 'has-many',
            ]);
            return false;
        }

        return true;
    }

    /**
     * @param ResourceIdentifierCollectionInterface $identifiers
     * @return bool
     */
    protected function validateEmpty(ResourceIdentifierCollectionInterface $identifiers)
    {
        if (!$this->isEmptyAllowed() && $identifiers->isEmpty()) {
            $this->addDataError(Keys::RELATIONSHIP_EMPTY_NOT_ALLOWED);
            return false;
        }

        return true;
    }

    /**
     * @param ResourceIdentifierCollectionInterface $identifiers
     * @param ResourceInterface $resource
     * @return bool
     */
    protected function validateIdentifiers(
        ResourceIdentifierCollectionInterface $identifiers,
        ResourceInterface $resource = null
    ) {
        /** @var ResourceIdentifierInterface $identifier */
        foreach ($identifiers as $identifier) {

            if (!$this->validateIdentifier($identifier) || !$this->validateExists($identifier)) {
                return false;
            }
        }

        /** @var ResourceIdentifierInterface $identifier */
        foreach ($identifiers as $identifier) {

            if (!$this->validateAcceptable($identifier, $resource)) {
                return false;
            }
        }

        return true;
    }

}
