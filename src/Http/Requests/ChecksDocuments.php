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

namespace CloudCreativity\JsonApi\Http\Requests;

use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterface;
use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterpreterInterface;
use CloudCreativity\JsonApi\Contracts\Validators\DocumentValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\ValidatorProviderInterface;
use CloudCreativity\JsonApi\Exceptions\ValidationException;
use Neomerx\JsonApi\Exceptions\JsonApiException;

/**
 * Class ChecksDocuments
 * @package CloudCreativity\JsonApi
 */
trait ChecksDocuments
{

    /**
     * @param ValidatorProviderInterface $validators
     * @param RequestInterpreterInterface $interpreter
     * @param RequestInterface $request
     * @throws JsonApiException
     */
    protected function checkDocumentIsAcceptable(
        ValidatorProviderInterface $validators,
        RequestInterpreterInterface $interpreter,
        RequestInterface $request
    ) {
        $document = $request->getDocument();

        if (!$document) {
            return;
        }

        $validator = $this->documentAcceptanceValidator($validators, $interpreter, $request);

        if ($validator && !$validator->isValid($document, $request->getRecord())) {
            throw new ValidationException($validator->getErrors());
        }
    }

    /**
     * @param ValidatorProviderInterface $validators
     * @param RequestInterpreterInterface $interpreter
     * @param RequestInterface $request
     * @return DocumentValidatorInterface|null
     */
    private function documentAcceptanceValidator(
        ValidatorProviderInterface $validators,
        RequestInterpreterInterface $interpreter,
        RequestInterface $request
    ) {
        $resourceType = $request->getResourceType();
        $resourceId = $interpreter->getResourceId();
        $relationshipName = $interpreter->getRelationshipName();
        $record = $request->getRecord();

        /** Create Resource */
        if ($interpreter->isCreateResource()) {
            return $validators->createResource($resourceType);
        } /** Update Resource */
        elseif ($interpreter->isUpdateResource()) {
            return $validators->updateResource($resourceType, $resourceId, $record);
        } /** Replace Relationship */
        elseif ($interpreter->isModifyRelationship()) {
            return $validators->modifyRelationship($resourceType, $resourceId, $relationshipName, $record);
        }

        return null;
    }

}
