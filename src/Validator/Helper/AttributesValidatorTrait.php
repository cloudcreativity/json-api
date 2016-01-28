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

namespace CloudCreativity\JsonApi\Validator\Helper;

use CloudCreativity\JsonApi\Contracts\Validator\KeyedValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface;
use CloudCreativity\JsonApi\Validator\Attributes\AttributesValidator;
use RuntimeException;

/**
 * Class AttributesValidatorTrait
 * @package CloudCreativity\JsonApi
 */
trait AttributesValidatorTrait
{

    /**
     * @var ValidatorInterface|null
     */
    private $attributesValidator;

    /**
     * @param ValidatorInterface $validator
     * @return $this
     */
    public function setAttributesValidator(ValidatorInterface $validator)
    {
        $this->attributesValidator = $validator;

        return $this;
    }

    /**
     * @return ValidatorInterface
     */
    public function getAttributesValidator()
    {
        if (!$this->attributesValidator instanceof ValidatorInterface) {
            $this->attributesValidator = new AttributesValidator();
        }

        return $this->attributesValidator;
    }

    /**
     * @return KeyedValidatorInterface
     */
    public function getKeyedAttributes()
    {
        $attributes = $this->getAttributesValidator();

        if (!$attributes instanceof KeyedValidatorInterface) {
            throw new RuntimeException(sprintf('%s expects attributes validator to be a %s instance.', static::class, KeyedValidatorInterface::class));
        }

        return $attributes;
    }

    /**
     * @return bool
     */
    public function hasKeyedAttributes()
    {
        return $this->getAttributesValidator() instanceof KeyedValidatorInterface;
    }
}
