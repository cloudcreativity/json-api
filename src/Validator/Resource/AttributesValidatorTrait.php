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
use CloudCreativity\JsonApi\Validator\Attributes\AttributesValidator;

/**
 * Class AttributesValidatorTrait
 * @package CloudCreativity\JsonApi
 */
trait AttributesValidatorTrait
{

    /**
     * @var ValidatorInterface|null
     */
    protected $_attributesValidator;

    /**
     * @var bool
     */
    protected $_expectingAttributes = true;

    /**
     * @param ValidatorInterface $validator
     * @return $this
     */
    public function setAttributesValidator(ValidatorInterface $validator)
    {
        $this->_attributesValidator = $validator;

        return $this;
    }

    /**
     * @return ValidatorInterface
     */
    public function getAttributesValidator()
    {
        if (!$this->_attributesValidator instanceof ValidatorInterface) {
            $this->_attributesValidator = new AttributesValidator();
        }

        return $this->_attributesValidator;
    }

    /**
     * @param $bool
     * @return $this
     */
    public function setExpectingAttributes($bool)
    {
        $this->_expectingAttributes = (bool) $bool;

        return $this;
    }

    /**
     * @return bool
     */
    public function isExpectingAttributes()
    {
        return (bool) $this->_expectingAttributes;
    }

    /**
     * @return AttributesValidator
     */
    public function getAttributes()
    {
        $validator = $this->getAttributesValidator();

        if (!$validator instanceof AttributesValidator) {
            throw new \RuntimeException(sprintf('Attributes validator is not a %s instance.', AttributesValidator::class));
        }

        return $validator;
    }
}