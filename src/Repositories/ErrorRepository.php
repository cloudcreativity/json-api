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

namespace CloudCreativity\JsonApi\Repositories;

use CloudCreativity\JsonApi\Contracts\Repositories\ErrorRepositoryInterface;
use CloudCreativity\JsonApi\Document\Error;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;

/**
 * Class ErrorRepository
 * @package CloudCreativity\JsonApi
 */
class ErrorRepository implements ErrorRepositoryInterface
{

    /**
     * @var array
     */
    private $errors;

    /**
     * ValidationMessageRepository constructor.
     * @param array $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @param string $key
     * @param int|null $status
     *      the status specified by the JSON API spec, or null if none specified.
     * @param array $values
     * @param array $merge
     * @return ErrorInterface
     */
    public function error($key, $status = null, array $values = [], array $merge = [])
    {
        $errorArray = $this->template($key, $status, $values, $merge);

        return Error::create($errorArray);
    }

    /**
     * @param $key
     * @param $pointer
     * @param int|null $status
     *      the status specified by the JSON API spec, or null if none specified.
     * @param array $values
     * @param array $merge
     * @return ErrorInterface
     */
    public function errorWithPointer($key, $pointer, $status = null, array $values = [], array $merge = [])
    {
        $errorArray = $this->template($key, $status, $values, $merge);

        return Error::createWithPointer($errorArray, $pointer);
    }

    /**
     * @param $key
     * @param $parameter
     * @param int|null $status
     *      the status specified by the JSON API spec, or null if none specified.
     * @param array $values
     * @param array $merge
     * @return Error
     */
    public function errorWithParameter($key, $parameter, $status = null, array $values = [], array $merge = [])
    {
        $errorArray = $this->template($key, $status, $values, $merge);

        return Error::createWithParameter($errorArray, $parameter);
    }

    /**
     * @param $key
     * @param null $status
     * @param array $values
     * @param array $merge
     * @return array
     */
    protected function template($key, $status = null, array $values = [], array $merge = [])
    {
        $errorArray = array_merge($this->get($key), $merge);

        if (is_int($status)) {
            $errorArray[Error::STATUS] = $status;
        }

        return $this->replacer($errorArray, $values);
    }

    /**
     * @param $key
     * @return array
     */
    protected function get($key)
    {
        return isset($this->errors[$key]) ? (array) $this->errors[$key] : [];
    }

    /**
     * @param array $error
     * @param array $values
     * @return array
     */
    protected function replacer(array $error, array $values)
    {
        if (!isset($error[Error::DETAIL])) {
            return $error;
        }

        foreach ($values as $key => $value) {
            $error[Error::DETAIL] = str_replace($key, $this->parseValue($value), $error[Error::DETAIL]);
        }

        return $error;
    }

    /**
     * @param $value
     * @return string
     */
    protected function parseValue($value)
    {
        if(is_object($value)) {
            return '<object>';
        } elseif (is_null($value)) {
            return 'null';
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif (is_scalar($value)) {
            return (string) $value;
        }

        $ret = [];

        foreach ((array) $value as $v) {
            $ret[] = $this->parseValue($v);
        }

        return implode(', ', $ret);
    }
}
