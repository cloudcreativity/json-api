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

use CloudCreativity\JsonApi\Contracts\Stdlib\ConfigurableInterface;
use CloudCreativity\JsonApi\Contracts\Validator\KeyedValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface;
use CloudCreativity\JsonApi\Validator\Type\TypeValidator;

/**
 * Class AttrTrait
 * @package CloudCreativity\JsonApi
 */
trait AttributeTrait
{

    /**
     * @return KeyedValidatorInterface
     */
    abstract public function getKeyedAttributes();

    /**
     * Helper method to add a type validator for the specified key.
     *
     * @param $key
     * @param string|ValidatorInterface $type
     * @param array $options
     * @return $this
     */
    public function attribute($key, $type = null, array $options = [])
    {
        $attributes = $this->getKeyedAttributes();
        $validator = $this->parseType($type);

        if ($validator instanceof ConfigurableInterface) {
            $validator->configure($options);
        }

        $attributes->setValidator($key, $validator);

        return $this;
    }

    /**
     * Short-hand method for `static::attribute`.
     *
     * @param $key
     * @param string|ValidatorInterface $type
     * @param array $options
     * @return $this
     */
    public function attr($key, $type = null, array $options = [])
    {
        return $this->attribute($key, $type, $options);
    }


    /**
     * @param string|null|ValidatorInterface
     * @return ValidatorInterface
     */
    protected function parseType($type)
    {
        if ($type instanceof ValidatorInterface) {
            return $type;
        } elseif (is_null($type)) {
            return new TypeValidator();
        } elseif (!is_string($type)) {
            throw new \InvalidArgumentException('Expecting a string, ValidatorInterface or null.');
        }

        if (class_exists($type)) {
            $class = $type;
        } else {
            $class = sprintf('CloudCreativity\JsonApi\Validator\Type\%sValidator', ucfirst($type));

            if (!class_exists($class)) {
                throw new \InvalidArgumentException(sprintf('Unrecognised attribute type: %s.', $type));
            }
        }

        $validator = new $class();

        if (!$validator instanceof ValidatorInterface) {
            throw new \InvalidArgumentException(sprintf('Type %s does not resolve to a %s instance.', $type, ValidatorInterface::class));
        }

        return $validator;
    }
}