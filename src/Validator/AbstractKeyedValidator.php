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

namespace CloudCreativity\JsonApi\Validator;

use CloudCreativity\JsonApi\Contracts\Validator\KeyedValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface;
use CloudCreativity\JsonApi\Validator\Helper\AllowedKeysTrait;

/**
 * Class AbstractKeyedValidator
 * @package CloudCreativity\JsonApi
 */
abstract class AbstractKeyedValidator extends AbstractValidator implements KeyedValidatorInterface
{

    use AllowedKeysTrait;

    const ERROR_REQUIRED = 'required';

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
     * @param ValidatorInterface[] $validators
     * @return $this
     */
    public function setValidators(array $validators)
    {
        foreach ($validators as $key => $validator) {

            if (!$validator instanceof ValidatorInterface) {
                throw new \InvalidArgumentException('Expecting only ValidatorInterface instances.');
            }

            $this->setValidator($key, $validator);
        }

        return $this;
    }

    /**
     * @param $key
     * @return ValidatorInterface
     */
    public function getValidator($key)
    {
        if (!isset($this->_validators[$key])) {
            throw new \RuntimeException(sprintf('No validator for key "%s".', $key));
        }

        return $this->_validators[$key];
    }

    /**
     * @return ValidatorInterface[]
     */
    public function getValidators()
    {
        return $this->_validators;
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
     * @return bool
     */
    public function isRequired()
    {
        /** @var ValidatorInterface $validator */
        foreach ($this as $validator) {

            if ($validator->isRequired()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $key
     * @return bool
     */
    public function isRequiredKey($key)
    {
        if (!$this->hasValidator($key)) {
            return false;
        }

        return $this
            ->getValidator($key)
            ->isRequired();
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->_validators);
    }

    /**
     * @param object $value
     */
    protected function checkRequired($value)
    {
        if (!is_object($value)) {
            throw new \RuntimeException('Expecting an object to check required keys.');
        }

        /** @var ValidatorInterface $validator */
        foreach ($this as $key => $validator) {

            if (!$validator->isRequired()) {
                continue;
            }

            if (!isset($value->{$key})) {
                $this->error(static::ERROR_REQUIRED, '/' . $key);
            }
        }
    }
}
