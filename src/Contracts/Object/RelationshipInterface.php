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

namespace CloudCreativity\JsonApi\Contracts\Object;

use CloudCreativity\JsonApi\Exceptions\DocumentException;
use Neomerx\JsonApi\Contracts\Document\DocumentInterface;

/**
 * Interface RelationshipInterface
 * @package CloudCreativity\JsonApi
 */
interface RelationshipInterface
{

    const DATA = DocumentInterface::KEYWORD_DATA;
    const META = DocumentInterface::KEYWORD_META;

    /**
     * @return ResourceIdentifierInterface|ResourceIdentifierCollectionInterface|null
     * @deprecated use `data()`
     */
    public function getData();

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
    public function data();

    /**
     * Get the data member as a has-one relationship.
     *
     * @return ResourceIdentifierInterface|null
     * @throws DocumentException
     *      if the data member is not a resource identifier or null.
     */
    public function hasOne();

    /**
     * Get the data member as a has-many relationship.
     *
     * @return ResourceIdentifierCollectionInterface
     * @throws DocumentException
     *      if the data member is not an array.
     */
    public function hasMany();

    /**
     * @return bool
     * @deprecated use `isHasOne` instead
     */
    public function isBelongsTo();

    /**
     * @return bool
     */
    public function isHasOne();

    /**
     * @return bool
     */
    public function isHasMany();

    /**
     * @return StandardObjectInterface
     * @deprecated use `meta()`
     */
    public function getMeta();

    /**
     * @return StandardObjectInterface
     * @throws DocumentException
     *      if the meta member is present and is not an object.
     */
    public function meta();

    /**
     * @return bool
     */
    public function hasMeta();
}
