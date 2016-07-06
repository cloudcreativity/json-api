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

namespace CloudCreativity\JsonApi\Contracts\Utils;

use Neomerx\JsonApi\Contracts\Document\ErrorInterface;

/**
 * Interface ErrorIdProviderInterface
 * @package CloudCreativity\JsonApi
 */
interface ErrorIdProviderInterface
{

    /**
     * Issue an id for the supplied error.
     *
     * @param ErrorInterface $error
     * @return string|int|null
     *      the error id, as a string or integer, or null if none is to be allocated.
     */
    public function issueId(ErrorInterface $error);
}
