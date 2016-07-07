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
use Neomerx\JsonApi\Exceptions\ErrorCollection;

/**
 * Class AbstractAuthorizer
 * @package CloudCreativity\JsonApi
 */
abstract class AbstractAuthorizer implements AuthorizerInterface
{

    /**
     * @inheritdoc
     */
    public function canReadRelatedResource(
        $relationshipKey,
        $record,
        EncodingParametersInterface $parameters,
        ErrorCollection $errors
    ) {
        return $this->canRead($record, $parameters, $errors);
    }

    /**
     * @inheritdoc
     */
    public function canReadRelationship(
        $relationshipKey,
        $record,
        EncodingParametersInterface $parameters,
        ErrorCollection $errors
    ) {
        return $this->canReadRelatedResource($relationshipKey, $record, $parameters, $errors);
    }

    /**
     * @inheritdoc
     */
    public function canModifyRelationship(
        $relationshipKey,
        $record,
        EncodingParametersInterface $parameters,
        ErrorCollection $errors
    ) {
        return $this->canUpdate($record, $parameters, $errors);
    }

}
