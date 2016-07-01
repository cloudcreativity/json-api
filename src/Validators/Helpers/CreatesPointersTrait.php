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

namespace CloudCreativity\JsonApi\Validators\Helpers;

use CloudCreativity\JsonApi\Utils\Pointer as P;

/**
 * Class CreatesPointersTrait
 * @package CloudCreativity\JsonApi
 * @deprecated use the Pointer class directly.
 */
trait CreatesPointersTrait
{

    /**
     * @return string
     */
    protected function getPathToData()
    {
        return P::data();
    }

    /**
     * @return string
     */
    protected function getPathToType()
    {
        return P::type();
    }

    /**
     * @return string
     */
    protected function getPathToId()
    {
        return P::id();
    }

    /**
     * @return string
     */
    protected function getPathToAttributes()
    {
        return P::attributes();
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getPathToAttribute($name)
    {
        return P::attribute($name);
    }

    /**
     * @return string
     */
    protected function getPathToRelationships()
    {
        return P::relationships();
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getPathToRelationship($name)
    {
        return P::relationship($name);
    }

    /**
     * @param $name
     * @return string
     */
    protected function getPathToRelationshipData($name)
    {
        return P::relationshipData($name);
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getPathToRelationshipType($name)
    {
        return P::relationshipType($name);
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getPathToRelationshipId($name)
    {
        return P::relationshipId($name);
    }
}
