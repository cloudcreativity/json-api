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
use CloudCreativity\JsonApi\Contracts\Validators\RelationshipValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\ValidationMessageFactoryInterface;
use CloudCreativity\JsonApi\Validators\ValidationKeys as Keys;

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
     * @var callable|null
     */
    private $acceptable;

    /**
     * HasOneValidator constructor.
     * @param ValidationMessageFactoryInterface $messages
     * @param StoreInterface $store;
     * @param $expectedType
     * @param bool $allowEmpty
     * @param callable|null $acceptable
     */
    public function __construct(
        ValidationMessageFactoryInterface $messages,
        StoreInterface $store,
        $expectedType,
        $allowEmpty = false,
        callable $acceptable = null
    ) {
        parent::__construct($messages);
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
     * @param ResourceIdentifierInterface $identifier
     * @param ResourceInterface|null $resource
     * @return bool
     */
    protected function isAcceptable(
        ResourceIdentifierInterface $identifier,
        ResourceInterface $resource = null
    ) {
        $callback = $this->acceptable;

        return $callback ? (bool) $callback($identifier, $resource) : true;
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
     * @return bool
     */
    protected function validateRelationship(RelationshipInterface $relationship)
    {
        if (!$relationship->has('data')) {
            $this->addDataError(Keys::MEMBER_REQUIRED, [
                ':member' => 'data',
            ]);
            return false;
        }

        if (!$relationship->isHasOne() && !$relationship->isHasMany()) {
            $this->addDataError(Keys::MEMBER_MUST_BE_RELATIONSHIP, [
                ':member' => 'data',
            ]);
        }

        return true;
    }

    /**
     * @param ResourceIdentifierInterface $identifier
     * @return bool
     */
    protected function validateIdentifier(ResourceIdentifierInterface $identifier)
    {
        $valid = true;

        if (!$identifier->hasType()) {
            $this->addDataError(Keys::MEMBER_REQUIRED, [
                ':member' => 'type',
            ]);
            $valid = false;
        } elseif (!$this->isSupportedType($identifier->type())) {
            $this->addDataTypeError(Keys::RELATIONSHIP_UNSUPPORTED_TYPE, [
                ':actual' => $identifier->type(),
                ':expected' => implode(', ', $this->expectedTypes)
            ]);
            $valid = false;
        }

        if (!$identifier->hasId()) {
            $this->addDataError(Keys::MEMBER_REQUIRED, [
                ':member' => 'id',
            ]);
            $valid = false;
        }

        return $valid;
    }

    /**
     * @param ResourceIdentifierInterface $identifier
     * @return bool
     */
    protected function validateExists(ResourceIdentifierInterface $identifier)
    {
        if (!$this->doesExist($identifier)) {
            $this->addDataError(Keys::RELATIONSHIP_DOES_NOT_EXIST);
            return false;
        }

        return true;
    }

    /**
     * @param ResourceIdentifierInterface $identifier
     * @param ResourceInterface|null $resource
     * @return bool
     */
    protected function validateAcceptable(
        ResourceIdentifierInterface $identifier,
        ResourceInterface $resource = null
    ) {
        if (!$this->isAcceptable($identifier, $resource)) {
            $this->addDataError(Keys::RELATIONSHIP_NOT_ACCEPTABLE);
            return false;
        }

        return true;
    }
}
