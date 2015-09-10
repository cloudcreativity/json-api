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

namespace CloudCreativity\JsonApi\Contracts\Exceptions\Renderer;

use Neomerx\JsonApi\Contracts\Parameters\SupportedExtensionsInterface;

/**
 * Interface ErrorRendererInterface
 * @package CloudCreativity\JsonApi
 */
interface ExceptionRendererInterface
{

    /**
     * @param int $statusCode
     * @return $this
     */
    public function withStatusCode($statusCode);

    /**
     * @param array $headers
     * @return $this
     */
    public function withHeaders(array $headers);

    /**
     * @param SupportedExtensionsInterface $extensions
     * @return $this
     */
    public function withSupportedExtensions(SupportedExtensionsInterface $extensions);

    /**
     * @param \Exception $e
     * @return mixed
     */
    public function render(\Exception $e);
}
