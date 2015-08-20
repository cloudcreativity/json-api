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
 * Class ExpectedIdValidator
 * @package CloudCreativity\JsonApi
 */
class ExpectedIdValidator extends AbstractValidator
{

    const INVALID_VALUE = 'invalid-value';
    const UNEXPECTED_ID = 'unexpected-id';

    /**
     * @var array
     */
    protected $templates = [
        self::INVALID_VALUE => [
            ErrorObject::CODE => self::INVALID_VALUE,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Invalid ID',
            ErrorObject::DETAIL => 'Expecting a non-empty string or integer id member.',
        ],
        self::UNEXPECTED_ID => [
            ErrorObject::CODE => self::UNEXPECTED_ID,
            ErrorObject::STATUS => 409,
            ErrorObject::TITLE => 'Unexpected ID',
            ErrorObject::DETAIL => 'The id received is not expected.',
        ],
    ];

    /**
     * @var int|string|null
     */
    protected $_expected;

    /**
     * @param null $expected
     */
    public function __construct($expected = null)
    {
        if (!is_null($expected)) {
            $this->setExpected($expected);
        }
    }

    /**
     * @param int|string $id
     * @return $this
     */
    public function setExpected($id)
    {
        if (!is_int($id) && !is_string($id)) {
            throw new \InvalidArgumentException('Expecting an integer or string.');
        }

        $this->_expected = $id;

        return $this;
    }

    /**
     * @return int|string
     */
    public function getExpected()
    {
        if (!is_int($this->_expected) && !is_string($this->_expected)) {
            throw new \RuntimeException('No expected id set.');
        }

        return $this->_expected;
    }

    /**
     * @param $id
     * @return bool
     */
    public function isExpected($id)
    {
        return $this->getExpected() == $id;
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
        if (!is_string($value) && !is_int($value)) {
            $this->error(static::INVALID_VALUE);
        } elseif (!$this->isExpected($value)) {
            $this->error(static::UNEXPECTED_ID);
        }
    }
}
