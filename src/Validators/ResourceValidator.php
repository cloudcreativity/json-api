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

use CloudCreativity\JsonApi\Contracts\Object\ResourceInterface;
use CloudCreativity\JsonApi\Contracts\Validators\AttributesValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\RelationshipsValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\ResourceValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\ValidationMessageFactoryInterface;
use CloudCreativity\JsonApi\Validators\ValidationKeys as Keys;

class ResourceValidator extends AbstractValidator implements ResourceValidatorInterface
{

    /**
     * @var string
     */
    private $expectedType;

    /**
     * @var int|null|string
     */
    private $expectedId;

    /**
     * @var AttributesValidatorInterface|null
     */
    private $attributes;

    /**
     * @var RelationshipsValidatorInterface|null
     */
    private $relationships;

    /**
     * ResourceValidator constructor.
     * @param ValidationMessageFactoryInterface $messages
     * @param string $expectedType
     * @param string|int|null $expectedId
     * @param AttributesValidatorInterface|null $attributes
     * @param RelationshipsValidatorInterface|null $relationships
     */
    public function __construct(
        ValidationMessageFactoryInterface $messages,
        $expectedType,
        $expectedId = null,
        AttributesValidatorInterface $attributes = null,
        RelationshipsValidatorInterface $relationships = null
    ) {
        parent::__construct($messages);
        $this->expectedType = $expectedType;
        $this->expectedId = $expectedId;
        $this->attributes = $attributes;
        $this->relationships = $relationships;
    }

    /**
     * @param ResourceInterface $resource
     * @return bool
     */
    public function isValid(ResourceInterface $resource)
    {
        $this->reset();

        $valid = $this->validateType($resource);

        if (!$this->validateId($resource)) {
            $valid = false;
        }

        if (!$this->validateAttributes($resource)) {
            $valid = false;
        }

        if (!$this->validateRelationships($resource)) {
            $valid = false;
        }

        return $valid;
    }

    /**
     * @param ResourceInterface $resource
     * @return bool
     */
    protected function validateType(ResourceInterface $resource)
    {
        /** Type is required */
        if (!$resource->has(ResourceInterface::TYPE)) {
            $this->addDataTypeError(
                Keys::MEMBER_REQUIRED,
                [':member' => ResourceInterface::TYPE]
            );
            return false;
        }

        /** Must be the expected type */
        if ($this->expectedType !== $resource->type()) {
            $this->addDataTypeError(
                Keys::RESOURCE_UNSUPPORTED_TYPE,
                [':actual' => $resource->type(), ':expected' => $this->expectedType],
                409
            );
            return false;
        }

        return true;
    }

    /**
     * @param ResourceInterface $resource
     * @return bool
     */
    protected function validateId(ResourceInterface $resource)
    {
        /** If expecting an id, one must be provided */
        if (!is_null($this->expectedId) && !$resource->has(ResourceInterface::ID)) {
            $this->addDataIdError(
                Keys::MEMBER_REQUIRED,
                [':member' => ResourceInterface::ID]
            );
            return false;
        }

        /** If expecting an id, must match the one we're expecting */
        if (!is_null($this->expectedId) && $this->expectedId != $resource->id()) {
            $this->addDataIdError(
                Keys::RESOURCE_UNSUPPORTED_ID,
                [':expected' => $this->expectedId, ':actual' => $resource->id()],
                409
            );
            return false;
        }

        return true;
    }

    /**
     * @param ResourceInterface $resource
     * @return bool
     */
    protected function validateAttributes(ResourceInterface $resource)
    {
        $raw = $resource->get(ResourceInterface::ATTRIBUTES);

        /** Attributes member must be an object. */
        if ($resource->has(ResourceInterface::ATTRIBUTES) && !is_object($raw)) {
            $this->addDataAttributesError(
                Keys::MEMBER_MUST_BE_OBJECT,
                [':member' => ResourceInterface::ATTRIBUTES]
            );
            return false;
        }

        /** Ok if no attributes validator or one that returns true for `isValid()` */
        if (!$this->attributes || $this->attributes->isValid($resource)) {
            return true;
        }

        /** Ensure that at least one error message is added. */
        if (0 < count($this->attributes->errors())) {
            $this->addErrors($this->attributes->errors());
        } else {
            $this->addDataAttributesError(Keys::RESOURCE_ATTRIBUTES_INVALID);
        }

        return false;
    }

    /**
     * @param ResourceInterface $resource
     * @return bool
     */
    protected function validateRelationships(ResourceInterface $resource)
    {
        $raw = $resource->get(ResourceInterface::RELATIONSHIPS);

        /** Relationships member must be an object. */
        if ($resource->has(ResourceInterface::RELATIONSHIPS) && !is_object($raw)) {
            $this->addDataRelationshipsError(
                Keys::MEMBER_MUST_BE_OBJECT,
                [':member' => ResourceInterface::RELATIONSHIPS]
            );
            return false;
        }

        /** Ok if no relationships validator or one that returns true for `isValid()` */
        if (!$this->relationships || $this->relationships->isValid($resource)) {
            return true;
        }

        if (0 < count($this->relationships->errors())) {
            $this->addErrors($this->relationships->errors());
        } else {
            $this->addDataRelationshipsError(Keys::RESOURCE_RELATIONSHIPS_INVALID);
        }

        return false;
    }
}
