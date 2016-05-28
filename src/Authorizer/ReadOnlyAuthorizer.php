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

namespace CloudCreativity\JsonApi\Authorizer;

use CloudCreativity\JsonApi\Contracts\Object\ResourceInterface;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;

class ReadOnlyAuthorizer extends AbstractAuthorizer
{

    /**
     * Can the client read many resources at once?
     *
     * Encoding parameters are provided in case the parameters such as
     * filtering or inclusion paths affect whether the resources can be read.
     *
     * @param EncodingParametersInterface $parameters
     *      the parameters provided by the client
     * @return bool
     */
    public function canReadMany(EncodingParametersInterface $parameters)
    {
        return true;
    }

    /**
     * Can the client create the provided resource?
     *
     * @param ResourceInterface $resource
     *      the resource provided by the client.
     * @param EncodingParametersInterface $parameters
     *      the parameters provided by the client
     * @return bool
     */
    public function canCreate(ResourceInterface $resource, EncodingParametersInterface $parameters)
    {
        return false;
    }

    /**
     * Can the client read the specified record?
     *
     * @param object $record
     *      the record that the client is trying to read.
     * @param EncodingParametersInterface $parameters
     *      the parameters provided by the client
     * @return bool
     */
    public function canRead($record, EncodingParametersInterface $parameters)
    {
        return true;
    }

    /**
     * Can the client update the specified record?
     *
     * @param object $record
     *      the record that the client is trying to update.
     * @param EncodingParametersInterface $parameters
     *      the parameters provided by the client
     * @return bool
     */
    public function canUpdate($record, EncodingParametersInterface $parameters)
    {
        return false;
    }

    /**
     * Can the client delete the specified record?
     *
     * @param object $record
     *      the record that the client is trying to delete.
     * @param EncodingParametersInterface $parameters
     *      the parameters provided by the client
     * @return bool
     */
    public function canDelete($record, EncodingParametersInterface $parameters)
    {
        return false;
    }


}
