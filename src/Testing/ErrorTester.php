<?php

/**
 * Copyright 2016 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Testing;

use CloudCreativity\JsonApi\Document\Error;
use Exception;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use PHPUnit_Framework_Assert as PHPUnit;

/**
 * Class ErrorTester
 * @package CloudCreativity\JsonApi
 */
class ErrorTester
{

    /**
     * @var Error
     */
    private $error;

    /**
     * @var int
     */
    private $index;

    /**
     * @param array $error
     *      the error read out of the error response, as an array.
     * @param int $index
     *      the index within the error collection at which this error exists.
     * @return ErrorTester
     */
    public static function create(array $error, $index = 0)
    {
        try {
            $error = Error::create($error);
        } catch (Exception $ex) {
            PHPUnit::fail(sprintf('Invalid error at index %d', $index));
        }

        return new self($error, $index);
    }

    /**
     * ErrorTester constructor.
     * @param ErrorInterface $error
     * @param int $index
     *      the index within the error collection at which this error exists.
     */
    public function __construct(ErrorInterface $error, $index = 0)
    {
        $this->error = Error::cast($error);
        $this->index = $index;
    }

    /**
     * @return Error
     */
    public function getError()
    {
        return clone $this->error;
    }

    /**
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Assert that the error code equals the expected code.
     *
     * @param $expected
     * @param string|null $message
     * @return $this
     */
    public function assertCode($expected, $message = null)
    {
        $message = $message ?: sprintf('Invalid code at error index %d', $this->index);
        PHPUnit::assertEquals($expected, $this->error->getCode(), $message);

        return $this;
    }

    /**
     * Assert that the error status equals the expected status.
     *
     * @param $expected
     * @param string|null $message
     * @return $this
     */
    public function assertStatus($expected, $message = null)
    {
        $message = $message ?: sprintf('Invalid status at error index %d', $this->index);
        PHPUnit::assertEquals($expected, $this->error->getStatus(), $message);

        return $this;
    }

    /**
     * Assert that the error source pointer equals the expected pointer.
     *
     * @param $expected
     * @param string|null $message
     * @return $this
     */
    public function assertPointer($expected, $message = null)
    {
        $message = $message ?: sprintf('Invalid source pointer at error index %d', $this->index);
        PHPUnit::assertEquals($expected, $this->error->getSourcePointer(), $message);

        return $this;
    }

    /**
     * Assert that the error source parameter equals the expected parameter.
     *
     * @param $expected
     * @param string|null $message
     * @return $this
     */
    public function assertParameter($expected, $message = null)
    {
        $message = $message ?: sprintf('Invalid source parameter at error index %d', $this->index);
        PHPUnit::assertEquals($expected, $this->error->getSourceParameter(), $message);

        return $this;
    }
}
