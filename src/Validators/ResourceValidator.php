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
use CloudCreativity\JsonApi\Utils\ErrorsAwareTrait;
use CloudCreativity\JsonApi\Utils\Pointer as P;

/**
 * Class ResourceValidator
 * @package CloudCreativity\JsonApi
 */
class ResourceValidator implements ResourceValidatorInterface
{

    use ErrorsAwareTrait;

    /**
     * @var ValidatorErrorFactoryInterface
     */
    private $errorFactory;

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
        $this->errorFactory = $errorFactory;
        $this->expectedType = $expectedType;
        $this->expectedId = $expectedId;
        $this->attributes = $attributes;
        $this->relationships = $relationships;
        $this->context = $context;
    }

    /**
     * @inheritdoc
     */
    public function isValid(ResourceInterface $resource, $record = null)
    {
        $this->reset();

        $valid = $this->validateType($resource);

        if (!$this->validateId($resource)) {
            $valid = false;
        }

        if (!$this->validateAttributes($resource, $record)) {
            $valid = false;
        }

        if (!$this->validateRelationships($resource, $record)) {
            $valid = false;
        }

        if ($valid && !$this->validateContext($resource, $record)) {
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
            $this->addError($this->errorFactory->memberRequired(ResourceInterface::TYPE, P::data()));
            return false;
        }

        /** Must be the expected type */
        if ($this->expectedType !== $resource->getType()) {
            $this->addError($this->errorFactory->resourceUnsupportedType(
                $this->expectedType,
                $resource->getType()
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
            $this->addError($this->errorFactory->memberRequired(ResourceInterface::ID, P::data()));
            return false;
        }

        /** If expecting an id, must match the one we're expecting */
        if (!is_null($this->expectedId) && $this->expectedId != $resource->getId()) {
            $this->addError($this->errorFactory->resourceUnsupportedId(
                $this->expectedId,
                $resource->getId()
            ));
            return false;
        }

        return true;
    }

    /**
     * @param ResourceInterface $resource
     * @param object|null $record
     * @return bool
     */
    protected function validateAttributes(ResourceInterface $resource, $record = null)
    {
        $raw = $resource->get(ResourceInterface::ATTRIBUTES);

        /** Attributes member must be an object. */
        if ($resource->has(ResourceInterface::ATTRIBUTES) && !is_object($raw)) {
            $this->addError($this->errorFactory->memberObjectExpected(
                ResourceInterface::ATTRIBUTES,
                P::attributes()
            ));
            return false;
        }

        /** Ok if no attributes validator or one that returns true for `isValid()` */
        if (!$this->attributes || $this->attributes->isValid($resource, $record)) {
            return true;
        }

        /** Ensure that at least one error message is added. */
        if (0 < count($this->attributes->getErrors())) {
            $this->addErrors($this->attributes->getErrors());
        } else {
            $this->addError($this->errorFactory->resourceInvalidAttributes());
        }

        return false;
    }

    /**
     * @param ResourceInterface $resource
     * @param object|null $record
     * @return bool
     */
    protected function validateRelationships(ResourceInterface $resource, $record = null)
    {
        $raw = $resource->get(ResourceInterface::RELATIONSHIPS);

        /** Relationships member must be an object. */
        if ($resource->has(ResourceInterface::RELATIONSHIPS) && !is_object($raw)) {
            $this->addError($this->errorFactory->memberObjectExpected(
                ResourceInterface::RELATIONSHIPS,
                P::relationships()
            ));
            return false;
        }

        /** Ok if no relationships validator or one that returns true for `isValid()` */
        if (!$this->relationships || $this->relationships->isValid($resource, $record)) {
            return true;
        }

        /** Ensure there is at least one error message. */
        if (0 < count($this->relationships->getErrors())) {
            $this->addErrors($this->relationships->getErrors());
        } else {
            $this->addError($this->errorFactory->resourceInvalidRelationships());
        }

        return false;
    }

    /**
     * @param ResourceInterface $resource
     * @param object|null $record
     * @return bool
     */
    protected function validateContext(ResourceInterface $resource, $record = null)
    {
        if (!$this->context || $this->context->isValid($resource, $record)) {
            return true;
        }

        $this->addErrors($this->context->getErrors());

        return false;
    }
}
