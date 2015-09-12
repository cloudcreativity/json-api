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

namespace CloudCreativity\JsonApi\Error;

use CloudCreativity\JsonApi\Contracts\Error\ErrorCollectionInterface;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;

/**
 * Class ErrorCollection
 * @package CloudCreativity\JsonApi
 */
class ErrorCollection implements \IteratorAggregate, ErrorCollectionInterface
{

    /**
     * @var array
     */
    private $stack = [];

    /**
     * @param array $errors
     */
    public function __construct(array $errors = [])
    {
        $this->addMany($errors);
    }

    /**
     * @return void
     */
    public function __clone()
    {
        $stack = [];

        /** @var ErrorInterface $error */
        foreach ($this as $error) {
            $stack[] = clone $error;
        }

        $this->stack = $stack;
    }

    /**
     * @param ErrorInterface $error
     * @return $this
     */
    public function add(ErrorInterface $error)
    {
        $this->stack[] = $error;

        return $this;
    }

    /**
     * @param array $errors
     * @return $this
     */
    public function addMany(array $errors)
    {
        foreach ($errors as $error) {

            if (!$error instanceof ErrorInterface) {
                throw new \InvalidArgumentException('Expecting only ErrorInterface objects.');
            }

            $this->add($error);
        }

        return $this;
    }

    /**
     * @param array|ErrorInterface $error
     * @return $this
     */
    public function error($error)
    {
        if (is_array($error)) {
            $error = ErrorObject::create($error);
        }

        if (!$error instanceof ErrorInterface) {
            throw new \InvalidArgumentException('Expecting an ErrorInterface object or an array.');
        }

        $this->add($error);

        return $this;
    }

    /**
     * @param ErrorCollectionInterface $errors
     * @return $this
     */
    public function merge(ErrorCollectionInterface $errors)
    {
        foreach ($errors as $error) {
            $this->add(clone $error);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->stack = [];

        return $this;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->stack;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        $request = null;
        $internal = null;

        /** @var ErrorInterface $error */
        foreach ($this as $error) {

            $status = $error->getStatus();

            if (400 <= $status && 499 >= $status) {
                $request = is_null($request) ? $status : 400;
            } elseif (500 <= $status && 599 >= $status) {
                $internal = is_null($internal) ? $status : 500;
            }
        }

        if (!is_null($internal)) {
            return (string) $internal;
        }

        return !is_null($request) ? (string) $request : '500';
    }

    /**
     * @param string|\Closure $pointer
     * @return $this
     */
    public function setSourcePointer($pointer)
    {
        foreach ($this as $error) {

            if ($error instanceof ErrorObject) {
                $error->source()->setPointer($pointer);
            }
        }

        return $this;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getAll());
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->stack);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->stack);
    }

}
