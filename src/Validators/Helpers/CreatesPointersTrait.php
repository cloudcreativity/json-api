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

use Neomerx\JsonApi\Contracts\Document\DocumentInterface;

trait CreatesPointersTrait
{

    /**
     * @return string
     */
    protected function getPathToData()
    {
        return '/' . DocumentInterface::KEYWORD_DATA;
    }

    /**
     * @return string
     */
    protected function getPathToType()
    {
        return sprintf('%s/%s', $this->getPathToData(), DocumentInterface::KEYWORD_TYPE);
    }

    /**
     * @return string
     */
    protected function getPathToId()
    {
        return sprintf('%s/%s', $this->getPathToData(), DocumentInterface::KEYWORD_ID);
    }

    /**
     * @return string
     */
    protected function getPathToAttributes()
    {
        return sprintf('%s/%s', $this->getPathToData(), DocumentInterface::KEYWORD_ATTRIBUTES);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getPathToAttribute($name)
    {
        return sprintf('%s/%s', $this->getPathToAttributes(), $name);
    }

    /**
     * @return string
     */
    protected function getPathToRelationships()
    {
        return sprintf('%s/%s', $this->getPathToData(), DocumentInterface::KEYWORD_RELATIONSHIPS);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getPathToRelationship($name)
    {
        return sprintf('%s/%s', $this->getPathToRelationships(), $name);
    }

    /**
     * @param $name
     * @return string
     */
    protected function getPathToRelationshipData($name)
    {
        return sprintf('%s/%s', $this->getPathToRelationship($name), DocumentInterface::KEYWORD_DATA);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getPathToRelationshipType($name)
    {
        return sprintf('%s/%s', $this->getPathToRelationshipData($name), DocumentInterface::KEYWORD_TYPE);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getPathToRelationshipId($name)
    {
        return sprintf('%s/%s', $this->getPathToRelationshipData($name), DocumentInterface::KEYWORD_ID);
    }
}
