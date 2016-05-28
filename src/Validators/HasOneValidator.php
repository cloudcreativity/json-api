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

class HasOneValidator extends AbstractRelationshipValidator
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

        if (!$this->validateHasOne($relationship, $key)) {
            return false;
        }

        $identifier = $relationship->hasOne();

        /** If there's an identifier, it must be a valid identifier object. */
        if ($identifier && !$this->validateIdentifier($identifier, $key)) {
            return false;
        }

        /** Check that empty is allowed. */
        if (!$this->validateEmpty($identifier, $key)) {
            return false;
        }

        /** If an identifier has been provided, the resource it references must exist. */
        if ($identifier && !$this->validateExists($identifier, $key)) {
            return false;
        }

        /** If an identifier has been provided, is it acceptable for the relationship? */
        if ($identifier && !$this->validateAcceptable($identifier, $key, $resource)) {
            return false;
        }

        return true;
    }

    /**
     * @param RelationshipInterface $relationship
     * @param string|null $key
     * @return bool
     */
    protected function validateHasOne(RelationshipInterface $relationship, $key = null)
    {
        if (!$relationship->isHasOne()) {
            $this->addError($this->errorFactory->relationshipHasOneExpected($key));
            return false;
        }

        return true;
    }

    /**
     * @param ResourceIdentifierInterface|null $identifier
     * @param string|null $key
     * @return bool
     */
    protected function validateEmpty(ResourceIdentifierInterface $identifier = null, $key = null)
    {
        if (!$this->isEmptyAllowed() && !$identifier) {
            $this->addError($this->errorFactory->relationshipEmptyNotAllowed($key));
            return false;
        }

        return true;
    }
}
