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

class HasManyValidator extends AbstractRelationshipValidator
{

    /**
     * Is the provided relationship valid?
     *
     * @param RelationshipInterface $relationship
     * @param string|null $key
     *      if a full resource is being validated, the key of the relationship.
     * @param ResourceInterface|null $resource
     *      if a full resource is being validated, the resource for context.
     * @return bool
     */
    public function isValid(
        RelationshipInterface $relationship,
        $key = null,
        ResourceInterface $resource = null
    ) {
        $this->reset();

        if (!$this->validateRelationship($relationship, $key)) {
            return false;
        }

        if (!$this->validateHasMany($relationship, $key)) {
            return false;
        }

        $identifiers = $relationship->hasMany();

        if (!$this->validateEmpty($identifiers, $key)) {
            return false;
        }

        if (!$this->validateIdentifiers($identifiers, $key, $resource)) {
            return false;
        }

        return true;
    }

    /**
     * @param RelationshipInterface $relationship
     * @param string|null $key
     * @return bool
     */
    protected function validateHasMany(RelationshipInterface $relationship, $key = null)
    {
        if (!$relationship->isHasMany()) {
            $this->addError($this->errorFactory->relationshipHasManyExpected($key));
            return false;
        }

        return true;
    }

    /**
     * @param ResourceIdentifierCollectionInterface $identifiers
     * @param string|null $key
     * @return bool
     */
    protected function validateEmpty(ResourceIdentifierCollectionInterface $identifiers, $key = null)
    {
        if (!$this->isEmptyAllowed() && $identifiers->isEmpty()) {
            $this->addError($this->errorFactory->relationshipEmptyNotAllowed($key));
            return false;
        }

        return true;
    }

    /**
     * @param ResourceIdentifierCollectionInterface $identifiers
     * @param string|null $key
     * @param ResourceInterface $resource
     * @return bool
     */
    protected function validateIdentifiers(
        ResourceIdentifierCollectionInterface $identifiers,
        $key = null,
        ResourceInterface $resource = null
    ) {
        /** @var ResourceIdentifierInterface $identifier */
        foreach ($identifiers as $identifier) {

            if (!$this->validateIdentifier($identifier, $key) || !$this->validateExists($identifier, $key)) {
                return false;
            }
        }

        /** @var ResourceIdentifierInterface $identifier */
        foreach ($identifiers as $identifier) {

            if (!$this->validateAcceptable($identifier, $key, $resource)) {
                return false;
            }
        }

        return true;
    }

}
