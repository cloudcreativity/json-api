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

namespace CloudCreativity\JsonApi\Http\Middleware;

use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterface;
use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterpreterInterface;
use CloudCreativity\JsonApi\Contracts\Validators\DocumentValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\ValidatorProviderInterface;
use CloudCreativity\JsonApi\Exceptions\ValidationException;
use Neomerx\JsonApi\Exceptions\JsonApiException;

/**
 * Class ValidatesRequests
 *
 * @package CloudCreativity\JsonApi
 */
trait ValidatesRequests
{

    /**
     * @param RequestInterpreterInterface $interpreter
     * @param RequestInterface $request
     * @param ValidatorProviderInterface $validators
     */
    protected function validate(
        RequestInterpreterInterface $interpreter,
        RequestInterface $request,
        ValidatorProviderInterface $validators
    ) {
        /** Check request parameters are acceptable */
        $this->checkQueryParameters($request, $validators);

        /** Check the document content is acceptable */
        $this->checkDocumentIsAcceptable($interpreter, $request, $validators);
    }

    /**
     * @param RequestInterface $request
     * @param ValidatorProviderInterface $validators
     * @throws JsonApiException
     */
    protected function checkQueryParameters(RequestInterface $request, ValidatorProviderInterface $validators)
    {
        $checker = $validators->queryChecker();
        $checker->checkQuery($request->getParameters());
    }


    /**
     * @param RequestInterpreterInterface $interpreter
     * @param RequestInterface $request
     * @param ValidatorProviderInterface $validators
     * @throws JsonApiException
     */
    protected function checkDocumentIsAcceptable(
        RequestInterpreterInterface $interpreter,
        RequestInterface $request,
        ValidatorProviderInterface $validators
    ) {
        if (!$document = $request->getDocument()) {
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
    protected function documentAcceptanceValidator(
        ValidatorProviderInterface $validators,
        RequestInterpreterInterface $interpreter,
        RequestInterface $request
    ) {
        $resourceId = $interpreter->getResourceId();
        $relationshipName = $interpreter->getRelationshipName();
        $record = $request->getRecord();

        /** Create Resource */
        if ($interpreter->isCreateResource()) {
            return $validators->createResource();
        } /** Update Resource */
        elseif ($interpreter->isUpdateResource()) {
            return $validators->updateResource($resourceId, $record);
        } /** Replace Relationship */
        elseif ($interpreter->isModifyRelationship()) {
            return $validators->modifyRelationship($resourceId, $relationshipName, $record);
        }

        return null;
    }

}
