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

namespace CloudCreativity\JsonApi\Contracts\Validators;

use Neomerx\JsonApi\Contracts\Http\Query\QueryCheckerInterface;

/**
 * Interface ValidatorProviderInterface
 *
 * @package CloudCreativity\JsonApi
 */
interface ValidatorProviderInterface
{

    /**
     * Get a validator for a create resource request.
     *
     * @return DocumentValidatorInterface
     */
    public function createResource();

    /**
     * Get a validator for an update resource request.
     *
     * @param string $resourceId
     *      the JSON API resource id that is being updated
     * @param object $record
     *      the domain record that is being updated
     * @return DocumentValidatorInterface
     */
    public function updateResource($resourceId, $record);

    /**
     * Get a validator for modifying a relationship.
     *
     * @param string $resourceId
     *      the JSON API resource id that is being modified
     * @param string $relationshipName
     *      the resource's relationship name that is being modified
     * @param object $record
     *      the domain record that is being modified
     *
     * @return DocumentValidatorInterface
     */
    public function modifyRelationship($resourceId, $relationshipName, $record);

    /**
     * Get a query checker for the specified resource type.
     *
     * @return QueryCheckerInterface
     */
    public function queryChecker();

}
