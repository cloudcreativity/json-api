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

namespace CloudCreativity\JsonApi\Validator\Attributes;

use CloudCreativity\JsonApi\Contracts\Stdlib\ConfigurableInterface;
use CloudCreativity\JsonApi\Error\ErrorObject;
use CloudCreativity\JsonApi\Error\SourceObject;
use CloudCreativity\JsonApi\Validator\AbstractValidator;
use CloudCreativity\JsonApi\Validator\Type\StringValidator;
use CloudCreativity\JsonApi\Validator\Type\TypeValidator;
use CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface;

/**
 * Class AttributesValidator
 * @package CloudCreativity\JsonApi
 */
class AttributesValidator extends AbstractValidator implements ConfigurableInterface
{

    // Config constants
    const ALLOWED = 'allowed';
    const REQUIRED = 'required';

    // Error constants
    const ERROR_INVALID_VALUE = 'invalid-value';
    const ERROR_UNRECOGNISED_ATTRIBUTE = 'not-recognised';
    const ERROR_REQUIRED_ATTRIBUTE = 'required';

    /**
     * @var array
     */
    protected $templates = [
        self::ERROR_INVALID_VALUE => [
            ErrorObject::CODE => self::ERROR_INVALID_VALUE,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Invalid Value',
            ErrorObject::DETAIL => 'Attributes must be an object.',
        ],
        self::ERROR_UNRECOGNISED_ATTRIBUTE => [
            ErrorObject::CODE => self::ERROR_UNRECOGNISED_ATTRIBUTE,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Unrecognised Attribute',
            ErrorObject::DETAIL => 'Attribute key is not recognised and cannot be accepted.',
        ],
        self::ERROR_REQUIRED_ATTRIBUTE => [
            ErrorObject::CODE => self::ERROR_REQUIRED_ATTRIBUTE,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Required Attribute',
            ErrorObject::DETAIL => 'Missing required attribute "%s".',
        ],
    ];

    /**
     * @var array|null
     *      null means any allowed, array means only the supplied keys are allowed.
     */
    protected $_allowed;

    /**
     * @var array|null
     */
    protected $_required;

    /**
     * Validators for use with keys within the attributes.
     *
     * @var array
     */
    protected $_validators = [];

    /**
     * @param array $keys
     * @return $this
     */
    public function setAllowed(array $keys)
    {
        $this->_allowed = $keys;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAllowed($key)
    {
        return is_array($this->_allowed) ? in_array($key, $this->_allowed) : true;
    }

    /**
     * @param array $keys
     * @return $this
     */
    public function setRequired(array $keys)
    {
        $this->_required = $keys;

        return $this;
    }

    /**
     * @return array
     */
    public function getRequired()
    {
        return (array) $this->_required;
    }

    /**
     * @param $key
     * @param ValidatorInterface $validator
     * @return $this
     */
    public function setValidator($key, ValidatorInterface $validator)
    {
        $this->_validators[$key] = $validator;

        return $this;
    }

    /**
     * @param $key
     * @return ValidatorInterface
     */
    public function getValidator($key)
    {
        if (!isset($this->_validators[$key])) {
            $this->_validators[$key] = new TypeValidator();
        }

        return $this->_validators[$key];
    }

    /**
     * Helper method to add a type validator for the specified key.
     *
     * @param $key
     * @param null $type
     * @param array $options
     * @return $this
     */
    public function attr($key, $type = null, array $options = [])
    {
        if (is_null($type)) {
            $type = 'type';
        }

        $class = sprintf('CloudCreativity\JsonApi\Validator\Type\%sValidator', ucfirst($type));

        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Unrecognised attribute type: %s.', $type));
        }

        /** @var ValidatorInterface $validator */
        $validator = new $class();

        if ($validator instanceof ConfigurableInterface) {
            $validator->configure($options);
        }

        $this->setValidator($key, $validator);

        return $this;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function configure(array $config)
    {
        if (isset($config[static::ALLOWED]) && is_array($config[static::ALLOWED])) {
            $this->setAllowed($config[static::ALLOWED]);
        }

        if (isset($config[static::REQUIRED]) && is_array($config[static::REQUIRED])) {
            $this->setRequired($config[static::REQUIRED]);
        }

        return $this;
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

        // Check provided keys.
        foreach (get_object_vars($value) as $key => $v) {
            $this->checkKey($key)
                ->checkValue($key, $v);
        }

        // Check that required keys exist.
        foreach ($this->getRequired() as $key) {

            if (!isset($value->{$key})) {
                $err = $this->error(static::ERROR_REQUIRED_ATTRIBUTE);
                $err->setDetail(sprintf($err->getDetail(), $key));
            }
        }
    }

    /**
     * @param $key
     * @return $this
     */
    protected function checkKey($key)
    {
        $pointer = '/' . $key;

        if (!$this->isAllowed($key)) {
            $this->error(static::ERROR_UNRECOGNISED_ATTRIBUTE)
                ->setSource([
                    SourceObject::POINTER => $pointer,
                ]);
        }

        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    protected function checkValue($key, $value)
    {
        $validator = $this->getValidator($key);

        if ($validator->isValid($value)) {
            return $this;
        }

        $errors = $validator
            ->getErrors()
            ->setSourcePointer(function ($current) use ($key) {
                return '/' . $key . $current;
            });

        $this->getErrors()->merge($errors);

        return $this;
    }

}
