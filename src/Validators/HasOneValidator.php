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
use CloudCreativity\JsonApi\Contracts\Object\ResourceIdentifierInterface;
use CloudCreativity\JsonApi\Contracts\Object\ResourceInterface;
use CloudCreativity\JsonApi\Validators\ValidationKeys as Keys;

class HasOneValidator extends AbstractRelationshipValidator
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

        if (!$this->validateHasOne($relationship)) {
            return false;
        }

        $identifier = $relationship->hasOne();

        /** If there's an identifier, it must be a valid identifier object. */
        if ($identifier && !$this->validateIdentifier($identifier)) {
            return false;
        }

        /** Check that empty is allowed. */
        if (!$this->validateEmpty($identifier)) {
            return false;
        }

        /** If an identifier has been provided, the resource it references must exist. */
        if ($identifier && !$this->validateExists($identifier)) {
            return false;
        }

        /** If an identifier has been provided, is it acceptable for the relationship? */
        if ($identifier && !$this->validateAcceptable($identifier, $resource)) {
            return false;
        }

        return true;
    }

    /**
     * @param RelationshipInterface $relationship
     * @return bool
     */
    protected function validateHasOne(RelationshipInterface $relationship)
    {
        if (!$relationship->isHasOne()) {
            $this->addDataError(Keys::RELATIONSHIP_INVALID, [
                ':relationship' => 'has-one',
            ]);
            return false;
        }

        return true;
    }

    /**
     * @param ResourceIdentifierInterface|null $identifier
     * @return bool
     */
    protected function validateEmpty(ResourceIdentifierInterface $identifier = null)
    {
        if (!$this->isEmptyAllowed() && !$identifier) {
            $this->addDataError(Keys::RELATIONSHIP_EMPTY_NOT_ALLOWED);
            return false;
        }

        return true;
    }
}
