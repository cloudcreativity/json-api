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
use CloudCreativity\JsonApi\Contracts\Store\StoreInterface;
use CloudCreativity\JsonApi\Contracts\Validators\AcceptRelatedResourceInterface;
use CloudCreativity\JsonApi\Contracts\Validators\RelationshipValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\ValidatorErrorFactoryInterface;

abstract class AbstractRelationshipValidator extends AbstractValidator implements RelationshipValidatorInterface
{

    /**
     * @var StoreInterface
     */
    private $store;

    /**
     * @var string[]
     */
    private $expectedTypes;

    /**
     * @var bool
     */
    private $allowEmpty;

    /**
     * @var AcceptRelatedResourceInterface|null
     */
    private $acceptable;

    /**
     * HasOneValidator constructor.
     * @param ValidatorErrorFactoryInterface $errorFactory
     * @param StoreInterface $store;
     * @param $expectedType
     * @param bool $allowEmpty
     * @param AcceptRelatedResourceInterface|null $acceptable
     */
    public function __construct(
        ValidatorErrorFactoryInterface $errorFactory,
        StoreInterface $store,
        $expectedType,
        $allowEmpty = false,
        AcceptRelatedResourceInterface $acceptable = null
    ) {
        parent::__construct($errorFactory);
        $this->store = $store;
        $this->expectedTypes = (array) $expectedType;
        $this->allowEmpty = $allowEmpty;
        $this->acceptable = $acceptable;
    }

    /**
     * @return bool
     */
    protected function isEmptyAllowed()
    {
        return (bool) $this->allowEmpty;
    }

    /**
     * @param ResourceIdentifierInterface $identifier
     * @return bool
     */
    protected function doesExist(ResourceIdentifierInterface $identifier)
    {
        return $this->store->exists($identifier);
    }

    /**
     * @param $type
     * @return bool
     */
    protected function isSupportedType($type)
    {
        return in_array($type, $this->expectedTypes, true);
    }

    /**
     * @param RelationshipInterface $relationship
     * @param string|null $key
     * @return bool
     */
    protected function validateRelationship(RelationshipInterface $relationship, $key = null)
    {
        if (!$relationship->has(RelationshipInterface::DATA)) {
            $this->addError($this->errorFactory->memberRequired(
                RelationshipInterface::DATA,
                $key ? $this->getPathToRelationship($key) : $this->getPathToData()
            ));
            return false;
        }

        if (!$relationship->isHasOne() && !$relationship->isHasMany()) {
            $this->addError($this->errorFactory->memberRelationshipExpected(
                RelationshipInterface::DATA,
                $key ? $this->getPathToRelationship($key) : $this->getPathToData()
            ));
            return false;
        }

        return true;
    }

    /**
     * @param ResourceIdentifierInterface $identifier
     * @param string|null $key
     * @return bool
     */
    protected function validateIdentifier(ResourceIdentifierInterface $identifier, $key = null)
    {
        $valid = true;

        if (!$identifier->hasType()) {
            $this->addError($this->errorFactory->memberRequired(
                ResourceIdentifierInterface::TYPE,
                $key ? $this->getPathToRelationshipData($key) : $this->getPathToData()
            ));
            $valid = false;
        } elseif (!$this->isSupportedType($identifier->type())) {
            $this->addError($this->errorFactory->relationshipUnsupportedType(
                $this->expectedTypes,
                $identifier->type(),
                $key
            ));
            $valid = false;
        }

        if (!$identifier->hasId()) {
            $this->addError($this->errorFactory->memberRequired(
                ResourceIdentifierInterface::ID,
                $key ? $this->getPathToRelationshipId($key) : $this->getPathToData()
            ));
            $valid = false;
        }

        return $valid;
    }

    /**
     * @param ResourceIdentifierInterface $identifier
     * @param string|null
     * @return bool
     */
    protected function validateExists(ResourceIdentifierInterface $identifier, $key = null)
    {
        if (!$this->doesExist($identifier)) {
            $this->addError($this->errorFactory->relationshipDoesNotExist($identifier, $key));
            return false;
        }

        return true;
    }

    /**
     * @param ResourceIdentifierInterface $identifier
     * @param string|null $key
     * @param ResourceInterface|null $resource
     * @return bool
     */
    protected function validateAcceptable(
        ResourceIdentifierInterface $identifier,
        $key = null,
        ResourceInterface $resource = null
    ) {
        if ($this->acceptable && !$this->acceptable->accept($identifier, $key, $resource)) {
            $this->addError($this->errorFactory->relationshipNotAcceptable(
                $identifier,
                $key
            ));
            return false;
        }

        return true;
    }
}
