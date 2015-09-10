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

use CloudCreativity\JsonApi\Error\ErrorObject;
use CloudCreativity\JsonApi\Validator\AbstractKeyedValidator;

/**
 * Class RelationshipsValidator
 * @package CloudCreativity\JsonApi
 */
class RelationshipsValidator extends AbstractKeyedValidator
{

    const ERROR_INVALID_VALUE = 'invalid-value';
    const ERROR_UNRECOGNISED_RELATIONSHIP = 'not-recognised';

    /**
     * @var array
     */
    protected $templates = [
        self::ERROR_INVALID_VALUE => [
            ErrorObject::CODE => self::ERROR_INVALID_VALUE,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Invalid Value',
            ErrorObject::DETAIL => 'Invalid relationships object value.',
        ],
        self::ERROR_REQUIRED => [
            ErrorObject::CODE => self::ERROR_REQUIRED,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Required Relationship',
            ErrorObject::DETAIL => 'Missing required relationship "%s".',
        ],
        self::ERROR_UNRECOGNISED_RELATIONSHIP => [
            ErrorObject::CODE => self::ERROR_UNRECOGNISED_RELATIONSHIP,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Unrecognised Relationship',
            ErrorObject::DETAIL => 'Relationship is not recognised and cannot be accepted.',
        ],
    ];

    /**
     * @param array $validators
     */
    public function __construct(array $validators = [])
    {
        $this->setValidators($validators);
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

        // Validate each provided relationship
        foreach (get_object_vars($value) as $key => $v) {
            $this->checkKey($key)
                ->checkValue($key, $v);
        }

        // Check required relationships
        $this->checkRequired($value);
    }

    /**
     * @param $key
     * @return $this
     */
    protected function checkKey($key)
    {
        if (!$this->hasValidator($key)) {
            $this->error(static::ERROR_UNRECOGNISED_RELATIONSHIP)
                ->source()
                ->setPointer(sprintf('/%s', $key));
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
        if (!$this->hasValidator($key)) {
            return $this;
        }

        $validator = $this->getValidator($key);

        if (!$validator->isValid($value)) {
            $this->getErrors()
                ->merge($validator
                    ->getErrors()
                    ->setSourcePointer(function ($current) use ($key) {
                        return '/' . $key . $current;
                    }));
        }

        return $this;
    }
}
