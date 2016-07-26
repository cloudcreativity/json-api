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

use Closure;
use Countable;
use IteratorAggregate;
use PHPUnit_Framework_Assert as PHPUnit;

/**
 * Class AbstractTraversableTester
 * @package CloudCreativity\JsonApi
 */
abstract class AbstractTraversableTester implements IteratorAggregate, Countable
{

    /**
     * @return bool
     */
    abstract public function isEmpty();

    /**
     * @param Closure $closure
     * @return array
     */
    public function map(Closure $closure)
    {
        $ret = [];

        foreach ($this as $key => $value) {
            $ret[$key] = $closure($value, $key);
        }

        return $ret;
    }

    /**
     * @param Closure $closure
     * @param mixed $carry
     * @return mixed
     */
    public function reduce(Closure $closure, $carry = null)
    {
        foreach ($this as $key => $value) {
            $carry = $closure($carry, $value, $key);
        }

        return $carry;
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        return iterator_to_array($this);
    }

    /**
     * @param string $message
     * @return $this
     */
    public function assertEmpty($message = '')
    {
        PHPUnit::assertEmpty($this, $message);

        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function assertNotEmpty($message = '')
    {
        PHPUnit::assertNotEmpty($this, $message);

        return $this;
    }
}
