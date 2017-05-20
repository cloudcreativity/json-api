<?php

/**
 * Copyright 2017 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Contracts\Http\Responses;

use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use Neomerx\JsonApi\Exceptions\ErrorCollection;

/**
 * Class ResponseFactory
 *
 * @package CloudCreativity\JsonApi
 */
interface ResponseFactoryInterface
{
    /**
     * @param $statusCode
     * @param array $headers
     * @return mixed
     */
    public function statusCode($statusCode, array $headers = []);

    /**
     * @param array $headers
     * @return mixed
     */
    public function noContent(array $headers = []);

    /**
     * @param mixed $meta
     * @param int $statusCode
     * @param array $headers
     * @return mixed
     */
    public function meta($meta, $statusCode = 200, array $headers = []);

    /**
     * @param mixed $data
     * @param array $links
     * @param mixed|null $meta
     * @param int $statusCode
     * @param array $headers
     * @return mixed
     */
    public function content($data, array $links = [], $meta = null, $statusCode = 200, array $headers = []);

    /**
     * @param object $resource
     * @param array $links
     * @param mixed|null $meta
     * @param array $headers
     * @return mixed
     */
    public function created($resource, array $links = [], $meta = null, array $headers = []);

    /**
     * @param $data
     * @param array $links
     * @param mixed|null $meta
     * @param int $statusCode
     * @param array $headers
     * @return mixed
     */
    public function relationship($data, array $links = [], $meta = null, $statusCode = 200, array $headers = []);

    /**
     * @param ErrorInterface|ErrorInterface[]|ErrorCollection|string $errors
     *      the error object(s) or a string error key to get the error from the error repository.
     * @param int|string|null $defaultStatusCode
     *      the default status code if it cannot be determined from the error objects.
     * @param array $headers
     * @return mixed
     */
    public function error($errors, $defaultStatusCode = null, array $headers = []);

    /**
     * @param ErrorResponseInterface
     * @return mixed
     */
    public function errors(ErrorResponseInterface $errors);
}
