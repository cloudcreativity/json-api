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

namespace CloudCreativity\JsonApi\Contracts\Repositories;

use CloudCreativity\JsonApi\Contracts\Utils\ConfigurableInterface;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;

/**
 * Interface ErrorRepositoryInterface
 * @package CloudCreativity\JsonApi
 */
interface ErrorRepositoryInterface extends ConfigurableInterface
{

    /**
     * @param string $key
     * @param array $values
     *      values to substitute into error detail.
     * @return ErrorInterface
     */
    public function error($key, array $values = []);

    /**
     * @param $key
     * @param $pointer
     * @param array $values
     *      values to substitute into error detail.
     * @return ErrorInterface
     */
    public function errorWithPointer($key, $pointer, array $values = []);

    /**
     * @param $key
     * @param $parameter
     * @param array $values
     *      values to substitute into error detail.
     * @return ErrorInterface
     */
    public function errorWithParameter($key, $parameter, array $values = []);
}
