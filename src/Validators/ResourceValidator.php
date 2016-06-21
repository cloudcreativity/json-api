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
use CloudCreativity\JsonApi\Contracts\Validators\ValidatorErrorFactoryInterface;

/**
 * Class ResourceValidator
 * @package CloudCreativity\JsonApi
 */
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
     * @var ResourceValidatorInterface|null
     */
    private $context;

    /**
     * ResourceValidator constructor.
     * @param ValidatorErrorFactoryInterface $errorFactory
     * @param string $expectedType
     * @param string|int|null $expectedId
     * @param AttributesValidatorInterface|null $attributes
     * @param RelationshipsValidatorInterface|null $relationships
     * @param ResourceValidatorInterface|null $context
     */
    public function __construct(
        ValidatorErrorFactoryInterface $errorFactory,
        $expectedType,
        $expectedId = null,
        AttributesValidatorInterface $attributes = null,
        RelationshipsValidatorInterface $relationships = null,
        ResourceValidatorInterface $context = null
    ) {
        parent::__construct($errorFactory);
        $this->expectedType = $expectedType;
        $this->expectedId = $expectedId;
        $this->attributes = $attributes;
        $this->relationships = $relationships;
        $this->context = $context;
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

        if ($valid && !$this->validateContext($resource)) {
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
            $this->addError($this->errorFactory->memberRequired(
                ResourceInterface::TYPE,
                $this->getPathToData()
            ));
            return false;
        }

        /** Must be the expected type */
        if ($this->expectedType !== $resource->type()) {
            $this->addError($this->errorFactory->resourceUnsupportedType(
                $this->expectedType,
                $resource->type()
            ));
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
            $this->addError($this->errorFactory->memberRequired(
                ResourceInterface::ID,
                $this->getPathToData()
            ));
            return false;
        }

        /** If expecting an id, must match the one we're expecting */
        if (!is_null($this->expectedId) && $this->expectedId != $resource->id()) {
            $this->addError($this->errorFactory->resourceUnsupportedId(
                $this->expectedId,
                $resource->id()
            ));
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
            $this->addError($this->errorFactory->memberObjectExpected(
                ResourceInterface::ATTRIBUTES,
                $this->getPathToAttributes()
            ));
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
            $this->addError($this->errorFactory->resourceInvalidAttributes());
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
            $this->addError($this->errorFactory->memberObjectExpected(
                ResourceInterface::RELATIONSHIPS,
                $this->getPathToRelationships()
            ));
            return false;
        }

        /** Ok if no relationships validator or one that returns true for `isValid()` */
        if (!$this->relationships || $this->relationships->isValid($resource)) {
            return true;
        }

        /** Ensure there is at least one error message. */
        if (0 < count($this->relationships->errors())) {
            $this->addErrors($this->relationships->errors());
        } else {
            $this->addError($this->errorFactory->resourceInvalidRelationships());
        }

        return false;
    }

    /**
     * @param ResourceInterface $resource
     * @return bool
     */
    protected function validateContext(ResourceInterface $resource)
    {
        if (!$this->context || $this->context->isValid($resource)) {
            return true;
        }

        $this->addErrors($this->context->errors());

        return false;
    }
}
