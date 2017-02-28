<?php

namespace CloudCreativity\JsonApi\Http\Middleware;

use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterface;
use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterpreterInterface;
use CloudCreativity\JsonApi\Contracts\Validators\DocumentValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\ValidatorProviderInterface;
use CloudCreativity\JsonApi\Exceptions\ValidationException;
use Neomerx\JsonApi\Exceptions\JsonApiException;

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
        $checker = $validators->queryChecker($request->getResourceType());
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
