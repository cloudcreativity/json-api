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

namespace CloudCreativity\JsonApi\Authorizer;

use CloudCreativity\JsonApi\Contracts\Authorizer\AuthorizerInterface;

abstract class AbstractAuthorizer implements AuthorizerInterface
{

    /**
     * Can the client read the specified resource relationship?
     *
     * @param string $relationshipKey
     *      the relationship that the client is trying to read.
     * @param object $record
     *      the record to which the relationship relates.
     * @return bool
     */
    public function canReadRelationship($relationshipKey, $record)
    {
        return $this->canRead($record);
    }

    /**
     * Can the client replace the specified resource relationship?
     *
     * A replace request is a PATCH request on a has-one or has-many relationship. For
     * has-many, this involves the client asking to replace the entire relationship. See:
     * http://jsonapi.org/format/#crud-updating-relationships
     *
     * @param string $relationshipKey
     * @param object $record
     * @return bool
     */
    public function canReplaceRelationship($relationshipKey, $record)
    {
        return $this->canUpdate($record);
    }

    /**
     * Can the client add members to the specified resource has-many relationship?
     *
     * A POST request to a has-many relationship endpoint is interpreted as the client asking to
     * add to the existing relationship. See
     * http://jsonapi.org/format/#crud-updating-relationships
     *
     * @param $relationshipKey
     * @param $record
     * @return bool
     */
    public function canAddToRelationship($relationshipKey, $record)
    {
        return $this->canUpdate($record);
    }

    /**
     * Can the client remove members from the specified resource has-many relationship?
     *
     * A DELETE request to a has-many relationship endpoint is interpreted as the client asking
     * to remove the specified resources from the relationship. See:
     * http://jsonapi.org/format/#crud-updating-relationships
     *
     * @param $relationshipKey
     * @param $record
     * @return bool
     */
    public function canRemoveFromRelationship($relationshipKey, $record)
    {
        return $this->canUpdate($record);
    }

}
