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

use Neomerx\JsonApi\Contracts\Document\ErrorInterface;

/**
 * Interface ErrorRepositoryInterface
 * @package CloudCreativity\JsonApi
 */
interface ErrorRepositoryInterface
{

    /**
     * @param string $key
     * @param int|null $status
     *      the status specified by the JSON API spec, or null if none specified.
     * @param array $values
     *      values to substitute into error detail.
     * @param array $merge
     *      error keys to merge in before creating the error object.
     * @return ErrorInterface
     */
    public function error($key, $status = null, array $values = [], array $merge = []);

    /**
     * @param $key
     * @param $pointer
     * @param int|null $status
     *      the status specified by the JSON API spec, or null if none specified.
     * @param array $values
     * @param array $merge
     * @return ErrorInterface
     */
    public function errorWithPointer($key, $pointer, $status = null, array $values = [], array $merge = []);

    /**
     * @param $key
     * @param $parameter
     * @param int|null $status
     *      the status specified by the JSON API spec, or null if none specified.
     * @param array $values
     * @param array $merge
     * @return ErrorInterface
     */
    public function errorWithParameter($key, $parameter, $status = null, array $values = [], array $merge = []);
}
