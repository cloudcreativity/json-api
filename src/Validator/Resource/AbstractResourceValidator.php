<?php

/**
 * Copyright 2015 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Validator\Resource;

use CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface;
use CloudCreativity\JsonApi\Error\ErrorObject;
use CloudCreativity\JsonApi\Object\Resource\Resource;
use CloudCreativity\JsonApi\Object\StandardObject;
use CloudCreativity\JsonApi\Validator\AbstractValidator;

/**
 * Class AbstractResourceValidator
 * @package CloudCreativity\JsonApi
 */
abstract class AbstractResourceValidator extends AbstractValidator
{

    const ERROR_INVALID_VALUE = 'invalid-value';
    const ERROR_MISSING_TYPE = 'missing-type';
    const ERROR_MISSING_ID = 'missing-id';
    const ERROR_UNEXPECTED_ID = 'unexpected-id';
    const ERROR_MISSING_ATTRIBUTES = 'missing-attributes';
    const ERROR_UNEXPECTED_ATTRIBUTES = 'unexpected-attributes';
    const ERROR_MISSING_RELATIONSHIPS = 'missing-relationships';
    const ERROR_UNEXPECTED_RELATIONSHIPS = 'unexpected-relationships';

    /**
     * @var array
     */
    protected $templates = [
        self::ERROR_INVALID_VALUE => [
            ErrorObject::CODE => self::ERROR_INVALID_VALUE,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Invalid Value',
            ErrorObject::DETAIL => 'Resource must be an object.',
        ],
        self::ERROR_MISSING_TYPE => [
            ErrorObject::CODE => self::ERROR_MISSING_TYPE,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Missing Resource Type',
            ErrorObject::DETAIL => 'Resource object must have a type member.',
        ],
        self::ERROR_MISSING_ID => [
            ErrorObject::CODE => self::ERROR_MISSING_ID,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Missing Resource ID',
            ErrorObject::DETAIL => 'Resource object must have an id member.',
        ],
        self::ERROR_UNEXPECTED_ID => [
            ErrorObject::CODE => self::ERROR_UNEXPECTED_ID,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Unexpected Resource ID',
            ErrorObject::DETAIL => 'Not expecting resource object to have an id member.',
        ],
        self::ERROR_MISSING_ATTRIBUTES => [
            ErrorObject::CODE => self::ERROR_MISSING_ATTRIBUTES,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Missing Resource Attributes',
            ErrorObject::DETAIL => 'Resource object must have an attributes member.',
        ],
        self::ERROR_UNEXPECTED_ATTRIBUTES => [
            ErrorObject::CODE => self::ERROR_UNEXPECTED_ATTRIBUTES,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Unexpected Resource Attributes',
            ErrorObject::DETAIL => 'Not expecting resource object to have an attributes member.',
        ],
        self::ERROR_MISSING_RELATIONSHIPS => [
            ErrorObject::CODE => self::ERROR_MISSING_RELATIONSHIPS,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Missing Resource Relationships',
            ErrorObject::DETAIL => 'Resource object must have a relationships member.',
        ],
        self::ERROR_UNEXPECTED_RELATIONSHIPS => [
            ErrorObject::CODE => self::ERROR_UNEXPECTED_RELATIONSHIPS,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Unexpected Resource Relationships',
            ErrorObject::DETAIL => 'Not expecting resource object to have a relationships member.',
        ],
    ];

    /**
     * @return ValidatorInterface
     */
    abstract public function getTypeValidator();

    /**
     * Get the id validator or null if no id is expected.
     *
     * @return ValidatorInterface|null
     */
    abstract public function getIdValidator();

    /**
     * Whether attributes must always be present in the resource object.
     *
     * @return bool
     */
    abstract public function isExpectingAttributes();

    /**
     * Get the attributes validator or null if the resource object must not have attributes.
     *
     * @return ValidatorInterface
     */
    abstract public function getAttributesValidator();

    /**
     * Whether relationships must always be present in the resource object.
     *
     * @return bool
     */
    abstract public function isExpectingRelationships();

    /**
     * Get the relationships validator or null if the resource object must not have relationships.
     *
     * @return ValidatorInterface|null
     */
    abstract public function getRelationshipsValidator();

    /**
     * @return bool
     */
    public function hasIdValidator()
    {
        return $this->getIdValidator() instanceof ValidatorInterface;
    }

    /**
     * @return bool
     */
    public function hasAttributesValidator()
    {
        return $this->getAttributesValidator() instanceof ValidatorInterface;
    }

    /**
     * @return bool
     */
    public function hasRelationshipsValidator()
    {
        return $this->getRelationshipsValidator() instanceof ValidatorInterface;
    }

    /**
     * @param $value
     */
    protected function validate($value)
    {
        if (!is_object($value)) {
            $this->error(static::ERROR_INVALID_VALUE);
            return;
        }

        $object = new StandardObject($value);

        $this->validateType($object)
            ->validateId($object)
            ->validateAttributes($object)
            ->validateRelationships($object);
    }

    /**
     * @param StandardObject $object
     * @return $this
     */
    protected function validateType(StandardObject $object)
    {
        if (!$object->has(Resource::TYPE)) {
            $this->error(static::ERROR_MISSING_TYPE);
            return $this;
        }

        $type = $this->getTypeValidator();

        if (!$type->isValid($object->get(Resource::TYPE))) {
            $this->getErrors()
                ->merge($type
                    ->getErrors()
                    ->setSourcePointer('/' . Resource::TYPE));
        }

        return $this;
    }

    /**
     * @param StandardObject $object
     * @return $this
     */
    protected function validateId(StandardObject $object)
    {
        // Is valid if no id and $this does not have an id validator.
        if (!$object->has(Resource::ID) && !$this->hasIdValidator()) {
            return $this;
        }

        if (!$object->has(Resource::ID) && $this->hasIdValidator()) {
            $this->error(static::ERROR_MISSING_ID);
            return $this;
        } elseif ($object->has(Resource::ID) && !$this->hasIdValidator()) {
            $this->error(static::ERROR_UNEXPECTED_ID, '/' . Resource::ID);
            return $this;
        }

        $validator = $this->getIdValidator();

        if (!$validator->isValid($object->get(Resource::ID))) {
            $this->getErrors()
                ->merge($validator
                    ->getErrors()
                    ->setSourcePointer('/' . Resource::ID));
        }

        return $this;
    }

    /**
     * @param StandardObject $object
     * @return $this
     */
    protected function validateAttributes(StandardObject $object)
    {
        // valid if the object does not have attributes, and attributes are not expected.
        if (!$object->has(Resource::ATTRIBUTES) && !$this->isExpectingAttributes()) {
            return $this;
        }

        if (!$object->has(Resource::ATTRIBUTES) && $this->isExpectingAttributes()) {
            $this->error(static::ERROR_MISSING_ATTRIBUTES);
            return $this;
        } elseif ($object->has(Resource::ATTRIBUTES) && !$this->hasAttributesValidator()) {
            $this->error(static::ERROR_UNEXPECTED_ATTRIBUTES, '/' . Resource::ATTRIBUTES);
            return $this;
        }

        $validator = $this->getAttributesValidator();

        if (!$validator->isValid($object->get(Resource::ATTRIBUTES))) {
            $this->getErrors()
                ->merge($validator
                    ->getErrors()
                    ->setSourcePointer(function ($current) {
                        return sprintf('/%s%s', Resource::ATTRIBUTES, $current);
                    }));
        }

        return $this;
    }

    /**
     * @param StandardObject $object
     * @return $this
     */
    protected function validateRelationships(StandardObject $object)
    {
        // valid if no relationships and not expecting relationships
        if (!$object->has(Resource::RELATIONSHIPS) && !$this->isExpectingRelationships()) {
            return $this;
        }

        if (!$object->has(Resource::RELATIONSHIPS) && $this->isExpectingRelationships()) {
            $this->error(static::ERROR_MISSING_RELATIONSHIPS);
            return $this;
        } elseif ($object->has(Resource::RELATIONSHIPS) && !$this->hasRelationshipsValidator()) {
            $this->error(static::ERROR_UNEXPECTED_RELATIONSHIPS, '/' . Resource::RELATIONSHIPS);
            return $this;
        }

        $validator = $this->getRelationshipsValidator();

        if (!$validator->isValid($object->get(Resource::RELATIONSHIPS))) {
            $this->getErrors()
                ->merge($validator
                    ->getErrors()
                    ->setSourcePointer(function ($current) {
                        return sprintf('/%s%s', Resource::RELATIONSHIPS, $current);
                    }));
        }

        return $this;
    }
}