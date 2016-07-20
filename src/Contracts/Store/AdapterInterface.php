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

namespace CloudCreativity\JsonApi\Contracts\Store;

use CloudCreativity\JsonApi\Contracts\Object\ResourceIdentifierInterface;

/**
 * Interface AdapterInterface
 * @package CloudCreativity\JsonApi
 */
interface AdapterInterface
{

    /**
     * Is this adapter responsible for the supplied resource type?
     *
     * @param $resourceType
     * @return bool
     */
    public function recognises($resourceType);

    /**
     * @param ResourceIdentifierInterface $identifier
     * @return mixed
     */
    public function exists(ResourceIdentifierInterface $identifier);

    /**
     * @param ResourceIdentifierInterface $identifier
     * @return object|null
     *      the record, or null if it does not exist.
     */
    public function find(ResourceIdentifierInterface $identifier);
}
