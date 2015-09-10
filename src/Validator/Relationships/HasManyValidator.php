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

namespace CloudCreativity\JsonApi\Validator\Relationships;

use CloudCreativity\JsonApi\Contracts\Stdlib\ConfigurableInterface;
use CloudCreativity\JsonApi\Error\ErrorObject;
use CloudCreativity\JsonApi\Object\Relationships\Relationship;
use CloudCreativity\JsonApi\Object\ResourceIdentifier\ResourceIdentifier;
use CloudCreativity\JsonApi\Object\ResourceIdentifier\ResourceIdentifierCollection;
use CloudCreativity\JsonApi\Validator\AbstractValidator;
use CloudCreativity\JsonApi\Validator\Helper\RequiredTrait;

/**
 * Class HasManyValidator
 * @package CloudCreativity\JsonApi
 */
class HasManyValidator extends AbstractValidator implements ConfigurableInterface
{

    use RequiredTrait;

    // Config constants
    const REQUIRED = 'required';
    const TYPES = 'types';
    const TYPE = self::TYPES;
    const ALLOW_EMPTY = 'allowEmpty';
    const CALLBACK = 'callback';

    // Error constants
    const ERROR_INVALID_VALUE = BelongsToValidator::ERROR_INVALID_VALUE;
    const ERROR_INVALID_TYPE = BelongsToValidator::ERROR_INVALID_TYPE;
    const ERROR_INVALID_ID = BelongsToValidator::ERROR_INVALID_ID;
    const ERROR_INCOMPLETE_IDENTIFIER = BelongsToValidator::ERROR_INCOMPLETE_IDENTIFIER;
    const ERROR_EMPTY_DISALLOWED = BelongsToValidator::ERROR_NULL_DISALLOWED;
    const ERROR_INVALID_COLLECTION = 'invalid-resources';
    const ERROR_NOT_FOUND = BelongsToValidator::ERROR_NOT_FOUND;

    protected $templates = [
        self::ERROR_INVALID_VALUE => [
            ErrorObject::CODE => self::ERROR_INVALID_VALUE,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Invalid Value',
            ErrorObject::DETAIL => 'Value provided is invalid for a belongs-to relationship.',
        ],
        self::ERROR_INVALID_TYPE => [
            ErrorObject::CODE => self::ERROR_INVALID_TYPE,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Invalid Relationship',
            ErrorObject::DETAIL => 'This has-many relationship does not accept the specified resource object type.',
        ],
        self::ERROR_INVALID_ID => [
            ErrorObject::CODE => self::ERROR_INVALID_ID,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Invalid Relationship',
            ErrorObject::DETAIL => 'The supplied relationship id is missing or invalid.',
        ],
        self::ERROR_INCOMPLETE_IDENTIFIER => [
            ErrorObject::CODE => self::ERROR_INCOMPLETE_IDENTIFIER,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Incomplete Resource Identifier',
            ErrorObject::DETAIL => 'The supplied resource identifier object is not complete.',
        ],
        self::ERROR_EMPTY_DISALLOWED => [
            ErrorObject::CODE => self::ERROR_EMPTY_DISALLOWED,
            ErrorObject::STATUS => 422,
            ErrorObject::TITLE => 'Invalid Relationship',
            ErrorObject::DETAIL => 'This relationship cannot be set to an empty value.',
        ],
        self::ERROR_INVALID_COLLECTION => [
            ErrorObject::CODE => self::ERROR_INVALID_COLLECTION,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Invalid Relationship',
            ErrorObject::DETAIL => 'The has-many relationships provided is invalid.',
        ],
        self::ERROR_NOT_FOUND => [
            ErrorObject::CODE => self::ERROR_NOT_FOUND,
            ErrorObject::STATUS => 404,
            ErrorObject::TITLE => 'Invalid Relationship',
            ErrorObject::DETAIL => 'The resource for this relationship cannot be found.',
        ],
    ];

    /**
     * @var array
     */
    private $types = [];

    /**
     * @var bool
     */
    private $allowEmpty = true;

    /**
     * @var callable|null
     */
    private $callback;

    /**
     * @param $typeOrTypes
     */
    public function __construct($typeOrTypes = null)
    {
        if (!is_null($typeOrTypes)) {
            $this->setTypes($typeOrTypes);
        }
    }

    /**
     * @param $typeOrTypes
     * @return $this
     */
    public function setTypes($typeOrTypes)
    {
        $this->types = is_array($typeOrTypes) ? $typeOrTypes : [$typeOrTypes];

        return $this;
    }

    /**
     * @param $type
     * @return bool
     */
    public function isType($type)
    {
        return in_array($type, $this->types, true);
    }

    /**
     * @param $allow
     * @return $this
     */
    public function setAllowEmpty($allow)
    {
        $this->allowEmpty = (bool) $allow;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEmptyAllowed()
    {
        return (bool) $this->allowEmpty;
    }

    /**
     * @param $callback
     * @return $this
     */
    public function setCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Expecting a valid callback.');
        }

        $this->callback = $callback;

        return $this;
    }

    /**
     * @return callable|null
     */
    public function getCallback()
    {
        if (!$this->hasCallback()) {
            throw new \RuntimeException('No callback set.');
        }

        return $this->callback;
    }

    /**
     * @return bool
     */
    public function hasCallback()
    {
        return is_callable($this->callback);
    }

    /**
     * @param array $config
     * @return $this
     */
    public function configure(array $config)
    {
        if (array_key_exists(static::REQUIRED, $config)) {
            $this->setRequired($config[static::REQUIRED]);
        }

        if (isset($config[static::TYPES])) {
            $this->setTypes($config[static::TYPES]);
        }

        if (array_key_exists(static::ALLOW_EMPTY, $config)) {
            $this->setAllowEmpty($config[static::ALLOW_EMPTY]);
        }

        if (isset($config[static::CALLBACK])) {
            $this->setCallback($config[static::CALLBACK]);
        }

        return $this;
    }

    /**
     * @param $value
     */
    protected function validate($value)
    {
        // must be an object.
        if (!is_object($value)) {
            $this->error(static::ERROR_INVALID_VALUE);
            return;
        }

        $object = new Relationship($value);

        // must be a has many relationship
        if (!$object->isHasMany()) {
            $this->error(static::ERROR_INVALID_VALUE, '/' . Relationship::DATA);
            return;
        }

        /** @var ResourceIdentifierCollection $data */
        $data = $object->getData();

        // if empty, empty relationship must be allowed.
        if ($data->isEmpty() && !$this->isEmptyAllowed()) {
            $this->error(static::ERROR_EMPTY_DISALLOWED, '/' . Relationship::DATA);
        }

        // if empty, is valid at this point
        if ($data->isEmpty()) {
            return;
        }

        // check that each resource identifier is valid.
        foreach ($data as $key => $identifier) {
            $this->validateIdentifier($identifier, $key);
        }

        if ($this->hasCallback()) {
            $this->validateCallback($data);
        }
    }

    /**
     * @param ResourceIdentifier $identifier
     * @param $index
     */
    protected function validateIdentifier(ResourceIdentifier $identifier, $index)
    {
        $pointer = sprintf('/%s/%s', Relationship::DATA, $index);

        // type and id must both be present
        if (!$identifier->hasType() || !$identifier->hasId()) {
            $this->error(static::ERROR_INCOMPLETE_IDENTIFIER, $pointer);
        }

        // type must be acceptable
        if ($identifier->hasType() && !$this->isType($identifier->getType())) {
            $this->error(static::ERROR_INVALID_TYPE, $pointer . '/' . ResourceIdentifier::TYPE);
        }

        $id = $identifier->hasId() ? $identifier->getId() : null;

        // id must be set and be either a non-empty string or an integer.
        if ($identifier->hasId() && ((!is_string($id) && !is_int($id)) || (is_string($id) && empty($id)))) {
            $this->error(static::ERROR_INVALID_ID, $pointer . '/' . ResourceIdentifier::ID);
        }
    }

    /**
     * @param ResourceIdentifierCollection $collection
     */
    protected function validateCallback(ResourceIdentifierCollection $collection)
    {
        $check = call_user_func($this->getCallback(), $collection);
        $pointer = '/' . Relationship::DATA;

        if (!is_array($check) && false == $check) {
            $this->error(static::ERROR_INVALID_COLLECTION, $pointer);
        }

        if (!is_array($check)) {
            return;
        }

        $count = count($collection);

        foreach ($check as $index) {

            if (!is_numeric($index) || 0 > $index || $count <= $index) {
                throw new \RuntimeException('Invalid error index.');
            }

            $this->error(static::ERROR_NOT_FOUND, $pointer . '/' . $index);
        }
    }
}
