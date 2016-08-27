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

namespace CloudCreativity\JsonApi\Http;
use CloudCreativity\JsonApi\Contracts\Http\RequestInterpreterInterface;

/**
 * Class InterpretsHttpRequests
 * @package CloudCreativity\JsonApi
 */
abstract class AbstractRequestInterpreter implements RequestInterpreterInterface
{

    /**
     * Is the current HTTP request method the one provided?
     *
     * @param string $method
     *      the expected method - case insensitive.
     * @return bool
     */
    abstract protected function isMethod($method);

    /**
     * Is this an index request?
     *
     * E.g. `GET /posts`
     *
     * @return bool
     */
    public function isIndex()
    {
        return $this->isMethod('get') && !$this->isResource();
    }

    /**
     * Is this a create resource request?
     *
     * E.g. `POST /posts`
     *
     * @return bool
     */
    public function isCreateResource()
    {
        return $this->isMethod('post') && !$this->isResource();
    }

    /**
     * Is this a read resource request?
     *
     * E.g. `GET /posts/1`
     *
     * @return bool
     */
    public function isReadResource()
    {
        return $this->isMethod('get') && $this->isResource() && !$this->isRelationship();
    }

    /**
     * Is this an update resource request?
     *
     * E.g. `PATCH /posts/1`
     *
     * @return bool
     */
    public function isUpdateResource()
    {
        return $this->isMethod('patch') && $this->isResource() && !$this->isRelationship();
    }

    /**
     * Is this a delete resource request?
     *
     * E.g. `DELETE /posts/1`
     *
     * @return bool
     */
    public function isDeleteResource()
    {
        return $this->isMethod('delete') && $this->isResource() && !$this->isRelationship();
    }

    /**
     * Is this a request for a related resource or resources?
     *
     * E.g. `GET /posts/1/author` or `GET /posts/1/comments`
     *
     * @return bool
     */
    public function isReadRelatedResource()
    {
        return $this->isRelationship() && !$this->isRelationshipData();
    }

    /**
     * Is this a request to read the data of a relationship?
     *
     * E.g. `GET /posts/1/relationships/author` or `GET /posts/1/relationships/comments`
     *
     * @return bool
     */
    public function isReadRelationship()
    {
        return $this->isMethod('get') && $this->isRelationshipData();
    }

    /**
     * Is this a request to modify the data of a relationship?
     *
     * @return bool
     */
    public function isModifyRelationship()
    {
        return $this->isReplaceRelationship() ||
            $this->isAddToRelationship() ||
            $this->isRemoveFromRelationship();
    }

    /**
     * Is this a request to replace the data of a relationship?
     *
     * E.g. `PATCH /posts/1/relationships/author` or `PATCH /posts/1/relationships/comments`
     */
    public function isReplaceRelationship()
    {
        return $this->isMethod('patch') && $this->isRelationshipData();
    }

    /**
     * Is this a request to add to the data of a has-many relationship?
     *
     * E.g. `POST /posts/1/relationships/comments`
     *
     * @return bool
     */
    public function isAddToRelationship()
    {
        return $this->isMethod('post') && $this->isRelationshipData();
    }

    /**
     * Is this a request to remove from the data of a has-many relationship?
     *
     * E.g. `DELETE /posts/1/relationships/comments`
     *
     * @return bool
     */
    public function isRemoveFromRelationship()
    {
        return $this->isMethod('delete') && $this->isRelationshipData();
    }

    /**
     * Is this a request where we expect a document to be sent by the client?
     *
     * @return bool
     */
    public function isExpectingDocument()
    {
        return $this->isCreateResource() ||
        $this->isUpdateResource() ||
        $this->isReplaceRelationship() ||
        $this->isAddToRelationship() ||
        $this->isRemoveFromRelationship();
    }

    /**
     * @return bool
     */
    protected function isResource()
    {
        return !empty($this->getResourceId());
    }

    /**
     * @return bool
     */
    protected function isRelationship()
    {
        return !empty($this->getRelationshipName());
    }

}
