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
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Neomerx\JsonApi\Document\Error;
use Neomerx\JsonApi\Exceptions\JsonApiException;

/**
 * Class AbstractAuthorizer
 * @package CloudCreativity\JsonApi
 */
abstract class AbstractAuthorizer implements AuthorizerInterface
{

    /**
     * Can the client read the related resource?
     *
     * @param $relationshipKey
     * @param $record
     * @param EncodingParametersInterface $parameters
     * @return bool
     */
    public function canReadRelatedResource($relationshipKey, $record, EncodingParametersInterface $parameters)
    {
        return $this->canRead($record, $parameters);
    }

    /**
     * Can the client read the specified resource relationship?
     *
     * @param string $relationshipKey
     *      the relationship that the client is trying to read.
     * @param object $record
     *      the record to which the relationship relates.
     * @param EncodingParametersInterface $parameters
     *      the parameters provided by the client
     * @return bool
     */
    public function canReadRelationship($relationshipKey, $record, EncodingParametersInterface $parameters)
    {
        return $this->canReadRelatedResource($relationshipKey, $record, $parameters);
    }

    /**
     * Can the client modified the specified resource relationship?
     *
     * @param string $relationshipKey
     * @param object $record
     * @param EncodingParametersInterface $parameters
     *      the parameters provided by the client
     * @return bool
     * @see http://jsonapi.org/format/#crud-updating-relationships
     */
    public function canModifyRelationship($relationshipKey, $record, EncodingParametersInterface $parameters)
    {
        return $this->canUpdate($record, $parameters);
    }

    /**
     * Get the JSON API error that should be used if a request is denied.
     *
     * Child classes can override this to provide a more customised error message if needed.
     *
     * @return Error
     */
    public function denied()
    {
        return new Error(null, null, JsonApiException::HTTP_CODE_FORBIDDEN);
    }

}
