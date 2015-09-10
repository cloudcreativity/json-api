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

namespace CloudCreativity\JsonApi\Validator\ResourceIdentifier;

use CloudCreativity\JsonApi\Error\ErrorObject;
use CloudCreativity\JsonApi\Validator\AbstractValidator;

/**
 * Class ExpectedTypeValidator
 * @package CloudCreativity\JsonApi
 */
class ExpectedTypeValidator extends AbstractValidator
{

    const INVALID_VALUE = 'invalid-value';
    const UNSUPPORTED_TYPE = 'unsupported-type';

    /**
     * @var array
     */
    protected $templates = [
        self::INVALID_VALUE => [
            ErrorObject::CODE => self::INVALID_VALUE,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Invalid Type',
            ErrorObject::DETAIL => 'Expecting a non-empty string type member.',
        ],
        self::UNSUPPORTED_TYPE => [
            ErrorObject::CODE => self::UNSUPPORTED_TYPE,
            ErrorObject::STATUS => 409,
            ErrorObject::TITLE => 'Unsupported Type',
            ErrorObject::DETAIL => 'Received type is not supported: expecting only "%s" resource objects.',
        ],
    ];

    /**
     * @param string|null $expected
     */
    public function __construct($expected = null)
    {
        if (!is_null($expected)) {
            $this->setExpected($expected);
        }
    }

    /**
     * @var string|null
     */
    protected $expected;

    /**
     * @param string $type
     * @return $this
     */
    public function setExpected($type)
    {
        if (!is_string($type) || empty($type)) {
            throw new \InvalidArgumentException('Expecting a non-empty string expected type.');
        }

        $this->expected = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getExpected()
    {
        if (!is_string($this->expected)) {
            throw new \RuntimeException('No expected type set.');
        }

        return $this->expected;
    }

    /**
     * @param $type
     * @return bool
     */
    public function isExpected($type)
    {
        return $this->getExpected() === $type;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return true;
    }

    /**
     * @param mixed $value
     * @return void
     */
    protected function validate($value)
    {
        if (!is_string($value) || empty($value)) {
            $this->error(static::INVALID_VALUE);
        } elseif (!$this->isExpected($value)) {
            $err = $this->error(static::UNSUPPORTED_TYPE);
            $err->setDetail(sprintf($err->getDetail(), $this->getExpected()));
        }
    }
}
