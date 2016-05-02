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

namespace CloudCreativity\JsonApi\Contracts\Object\Document;

use CloudCreativity\JsonApi\Contracts\Object\Relationships\RelationshipInterface;
use CloudCreativity\JsonApi\Contracts\Object\Resource\ResourceInterface;
use CloudCreativity\JsonApi\Contracts\Object\StandardObjectInterface;
use CloudCreativity\JsonApi\Exceptions\DocumentException;
use Neomerx\JsonApi\Contracts\Document\DocumentInterface as NeomerxDocumentInterface;

/**
 * Interface DocumentInterface
 * @package CloudCreativity\JsonApi
 */
interface DocumentInterface extends StandardObjectInterface
{

    const DATA = NeomerxDocumentInterface::KEYWORD_DATA;
    const META = NeomerxDocumentInterface::KEYWORD_META;

    /**
     * @return StandardObjectInterface
     * @deprecated use `data()`
     */
    public function getData();

    /**
     * Get the data member of the document as a standard object.
     *
     * @return StandardObjectInterface
     * @throws DocumentException
     *      if the data member is not an object, or is not present.
     */
    public function data();

    /**
     * @return ResourceInterface
     * @deprecated use `resource()`
     */
    public function getResourceObject();

    /**
     * Get the data member as a resource object.
     *
     * @return ResourceInterface
     * @throws DocumentException
     *      if the data member is not an object or is not present.
     */
    public function resource();

    /**
     * @return RelationshipInterface
     * @deprecated use `relationship()`
     */
    public function getRelationship();

    /**
     * Get the data member as a relationship.
     *
     * @return RelationshipInterface
     * @throws DocumentException
     *      if the data member is not an object or null, or is not present.
     */
    public function relationship();

    /**
     * @return StandardObjectInterface
     * @deprecated use `meta()`
     */
    public function getMeta();

    /**
     * Get the meta member of the document.
     *
     * @return StandardObjectInterface
     * @throws DocumentException
     *      if the meta member is present and is not an object.
     */
    public function meta();
}
