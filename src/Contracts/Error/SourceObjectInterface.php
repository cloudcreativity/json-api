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

/**
 * Interface SourceObjectInterface
 * @package CloudCreativity\JsonApi
 */
interface SourceObjectInterface extends \JsonSerializable
{

    const POINTER = 'pointer';
    const PARAMETER = 'parameter';

    /**
     * @param string|null|\Closure $pointer
     * @return $this
     */
    public function setPointer($pointer);

    /**
     * @return string|null
     */
    public function getPointer();

    /**
     * @param string|null $parameter
     * @return $this
     */
    public function setParameter($parameter);

    /**
     * @return string|null
     */
    public function getParameter();
}
