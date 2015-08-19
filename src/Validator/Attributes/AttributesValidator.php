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
use CloudCreativity\JsonApi\Contracts\Validator\KeyedValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface;
use CloudCreativity\JsonApi\Error\ErrorObject;
use CloudCreativity\JsonApi\Error\SourceObject;
use CloudCreativity\JsonApi\Validator\AbstractValidator;
use CloudCreativity\JsonApi\Validator\Helper\AllowedKeysTrait;
use CloudCreativity\JsonApi\Validator\Helper\RequiredKeysTrait;
use CloudCreativity\JsonApi\Validator\Type\TypeValidator;

/**
 * Class AttributesValidator
 * @package CloudCreativity\JsonApi
 */
class AttributesValidator extends AbstractValidator implements ConfigurableInterface, KeyedValidatorInterface
{

    use RequiredKeysTrait,
        AllowedKeysTrait;

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
     * Validators for use with keys within the attributes.
     *
     * @var array
     */
    protected $_validators = [];

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
     * @param $key
     * @return bool
     */
    public function hasValidator($key)
    {
        return isset($this->_validators[$key]);
    }

    /**
     * @return array
     */
    public function keys()
    {
        return array_keys($this->_validators);
    }

    /**
     * @param array $config
     * @return $this
     */
    public function configure(array $config)
    {
        if (isset($config[static::ALLOWED]) && is_array($config[static::ALLOWED])) {
            $this->setAllowedKeys($config[static::ALLOWED]);
        }

        if (isset($config[static::REQUIRED]) && is_array($config[static::REQUIRED])) {
            $this->setRequiredKeys($config[static::REQUIRED]);
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
        foreach ($this->getRequiredKeys() as $key) {

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

        if (!$this->isAllowedKey($key)) {
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
