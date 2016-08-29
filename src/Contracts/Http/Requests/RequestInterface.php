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

namespace CloudCreativity\JsonApi\Contracts\Http\Requests;

use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;

interface RequestInterface
{

    /**
     * The resource type in the request URI.
     *
     * @return string
     */
    public function getResourceType();

    /**
     * The resource id in the request URI, if there is one.
     *
     * @return string|null
     */
    public function getResourceId();

    /**
     * The relationship name in the request URI, if there is one.
     *
     * @return string|null
     */
    public function getRelationshipName();

    /**
     * Get the encoding parameters sent by the client.
     *
     * @return EncodingParametersInterface
     */
    public function getParameters();

    /**
     * Get the request content sent by the client, if content is expected.
     *
     * @return DocumentInterface|null
     */
    public function getDocument();

    /**
     * Get the domain object that the request relates to, if there is one.
     *
     * This will only return an object if the request has a resource id
     * and the id is valid.
     *
     * @return object|null
     */
    public function getRecord();

}
