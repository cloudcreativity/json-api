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

namespace CloudCreativity\JsonApi\Contracts\Object;

use CloudCreativity\JsonApi\Exceptions\DocumentException;
use Neomerx\JsonApi\Contracts\Document\DocumentInterface as NeomerxDocumentInterface;

/**
 * Interface RelationshipInterface
 * @package CloudCreativity\JsonApi
 */
interface RelationshipInterface extends StandardObjectInterface, MetaMemberInterface
{

    const DATA = NeomerxDocumentInterface::KEYWORD_DATA;
    const META = NeomerxDocumentInterface::KEYWORD_META;

    /**
     * Get the data member as a correctly casted object.
     *
     * If this is a has-one relationship, a ResourceIdentifierInterface object or null will be returned. If it is
     * a has-many relationship, a ResourceIdentifierCollectionInterface will be returned.
     *
     * @return ResourceIdentifierInterface|ResourceIdentifierCollectionInterface|null
     * @throws DocumentException
     *      if the value for the data member is not a valid relationship value.
     */
    public function getData();

    /**
     * Get the data member as a has-one relationship.
     *
     * @return ResourceIdentifierInterface|null
     * @throws DocumentException
     *      if the data member is not a resource identifier or null.
     */
    public function getIdentifier();

    /**
     * Is this a has-one relationship?
     *
     * @return bool
     */
    public function isHasOne();

    /**
     * Get the data member as a has-many relationship.
     *
     * @return ResourceIdentifierCollectionInterface
     * @throws DocumentException
     *      if the data member is not an array.
     */
    public function getIdentifiers();

    /**
     * Is this a has-many relationship?
     *
     * @return bool
     */
    public function isHasMany();

}
