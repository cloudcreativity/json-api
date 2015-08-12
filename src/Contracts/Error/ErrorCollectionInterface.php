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

namespace CloudCreativity\JsonApi\Contracts\Error;

use Neomerx\JsonApi\Contracts\Document\ErrorInterface;

/**
 * Interface ErrorCollectionInterface
 * @package CloudCreativity\JsonApi
 */
interface ErrorCollectionInterface extends \Traversable, \Countable
{

    /**
     * @param ErrorInterface $error
     * @return $this
     */
    public function add(ErrorInterface $error);

    /**
     * @param ErrorInterface[] $errors
     * @return $this
     */
    public function addMany(array $errors);

    /**
     * @return ErrorInterface[]
     */
    public function getAll();

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param string|\Closure $pointer
     * @return $this
     */
    public function setSourcePointer($pointer);

    /**
     * @return bool
     */
    public function isEmpty();

    /**
     * @return $this
     */
    public function clear();

    /**
     * @param ErrorCollectionInterface $errors
     * @return $this
     */
    public function merge(ErrorCollectionInterface $errors);

}