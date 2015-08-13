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

namespace CloudCreativity\JsonApi\Object\Document;

use CloudCreativity\JsonApi\Object\Relationships\Relationship;
use CloudCreativity\JsonApi\Object\Resource\ResourceObject;
use CloudCreativity\JsonApi\Object\StandardObject;

/**
 * Class Document
 * @package CloudCreativity\JsonApi
 */
class Document extends StandardObject
{

    const DATA = 'data';

    /**
     * Get the primary data as a resource object.
     *
     * @return ResourceObject
     */
    public function getResourceObject()
    {
        return new ResourceObject($this->get(static::DATA));
    }

    /**
     * Get the document as a relationship object.
     *
     * @return Relationship
     */
    public function getRelationship()
    {
        return new Relationship($this->getProxy());
    }
}