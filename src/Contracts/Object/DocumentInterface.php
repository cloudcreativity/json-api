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

use CloudCreativity\JsonApi\Exceptions\RuntimeException;
use Neomerx\JsonApi\Contracts\Document\DocumentInterface as NeomerxDocumentInterface;

/**
 * Interface DocumentInterface
 * @package CloudCreativity\JsonApi
 */
interface DocumentInterface extends StandardObjectInterface, MetaMemberInterface
{

    const DATA = NeomerxDocumentInterface::KEYWORD_DATA;
    const META = NeomerxDocumentInterface::KEYWORD_META;

    /**
     * Get the data member of the document as a standard object.
     *
     * @return StandardObjectInterface
     * @throws RuntimeException
     *      if the data member is not an object, or is not present.
     */
    public function getData();

    /**
     * Get the data member as a resource object.
     *
     * @return ResourceInterface
     * @throws RuntimeException
     *      if the data member is not an object or is not present.
     */
    public function getResource();

    /**
     * Get the document as a relationship.
     *
     * @return RelationshipInterface
     */
    public function getRelationship();

}
