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
use Neomerx\JsonApi\Contracts\Document\DocumentInterface as Keys;
use PHPUnit_Framework_Assert as PHPUnit;
use stdClass;

/**
 * Class ErrorTester
 * @package CloudCreativity\JsonApi
 */
class ErrorTester
{

    /**
     * @var stdClass
     */
    private $error;

    /**
     * @var int
     */
    private $index;

    /**
     * ErrorTester constructor.
     * @param stdClass $error
     * @param int $index
     *      the index within the error collection at which this error exists.
     */
    public function __construct(stdClass $error, $index = 0)
    {
        $this->error = $error;
        $this->index = $index;
    }

    /**
     * @return stdClass
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
     * @return mixed
     */
    public function getCode()
    {
        return isset($this->error->{Keys::KEYWORD_ERRORS_CODE}) ?
            $this->error->{Keys::KEYWORD_ERRORS_CODE} : null;
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
        PHPUnit::assertEquals($expected, $this->getCode(), $message);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return isset($this->error->{Keys::KEYWORD_ERRORS_STATUS}) ?
            $this->error->{Keys::KEYWORD_ERRORS_STATUS} : null;
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
        PHPUnit::assertEquals($expected, $this->getStatus(), $message);

        return $this;
    }

    /**
     * @return stdClass|null
     */
    public function getSource()
    {
        $source = isset($this->error->{Keys::KEYWORD_ERRORS_SOURCE}) ?
            $this->error->{Keys::KEYWORD_ERRORS_SOURCE} : null;

        if (!is_null($source) && !$source instanceof stdClass) {
            PHPUnit::fail(sprintf('Invalid error source at index %d', $this->index));
        }

        return $source;
    }

    /**
     * @return mixed
     */
    public function getSourcePointer()
    {
        $source = $this->getSource() ?: new stdClass();

        return isset($source->{Error::SOURCE_POINTER}) ? $source->{Error::SOURCE_POINTER} : null;
    }

    /**
     * @return mixed
     */
    public function getSourceParameter()
    {
        $source = $this->getSource() ?: new stdClass();

        return isset($source->{Error::SOURCE_PARAMETER}) ? $source->{Error::SOURCE_PARAMETER} : null;
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
        PHPUnit::assertEquals($expected, $this->getSourcePointer(), $message);

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
        PHPUnit::assertEquals($expected, $this->getSourceParameter(), $message);

        return $this;
    }
}
