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

namespace CloudCreativity\JsonApi\Contracts\Object\Resource;

use CloudCreativity\JsonApi\Contracts\Object\Relationships\RelationshipsInterface;
use CloudCreativity\JsonApi\Contracts\Object\ResourceIdentifier\ResourceIdentifierInterface;
use CloudCreativity\JsonApi\Contracts\Object\StandardObjectInterface;
use CloudCreativity\JsonApi\Exceptions\DocumentException;
use Neomerx\JsonApi\Contracts\Document\DocumentInterface;
use RuntimeException;

/**
 * Interface ResourceObjectInterface
 * @package CloudCreativity\JsonApi
 */
interface ResourceInterface extends StandardObjectInterface
{

    const TYPE = DocumentInterface::KEYWORD_TYPE;
    const ID = DocumentInterface::KEYWORD_ID;
    const ATTRIBUTES = DocumentInterface::KEYWORD_ATTRIBUTES;
    const RELATIONSHIPS = DocumentInterface::KEYWORD_RELATIONSHIPS;
    const META = DocumentInterface::KEYWORD_META;

    /**
     * Get the type member.
     *
     * @return string
     * @throws DocumentException
     *      if no type is set, is empty or is not a string.
     */
    public function type();

    /**
     * @return string
     * @deprecated use `type()`
     */
    public function getType();

    /**
     * @return string|int
     * @deprecated use `id()`
     */
    public function getId();

    /**
     * @return string|int
     * @throws DocumentException
     *      if no id is set, is not a string or integer, or is an empty string.
     */
    public function id();

    /**
     * @return bool
     */
    public function hasId();

    /**
     * @return ResourceIdentifierInterface
     * @deprecated use `identifier()`
     */
    public function getIdentifier();

    /**
     * Get the type and id members as a resource identifier object.
     *
     * @return ResourceIdentifierInterface
     * @throws DocumentException
     *      if the type and/or id members are not valid.
     */
    public function identifier();

    /**
     * @return StandardObjectInterface
     * @deprecated use `attributes`
     */
    public function getAttributes();

    /**
     * @return StandardObjectInterface
     * @throws DocumentException
     *      if the attributes member is present and is not an object.
     */
    public function attributes();

    /**
     * @return bool
     */
    public function hasAttributes();

    /**
     * @return RelationshipsInterface
     * @deprecated use `relationships()`
     */
    public function getRelationships();

    /**
     * @return RelationshipsInterface
     * @throws DocumentException
     *      if the relationships member is present and is not an object.
     */
    public function relationships();

    /**
     * @return bool
     */
    public function hasRelationships();

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
