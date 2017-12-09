<?php

/**
 * Copyright 2017 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Contracts\Store;

use CloudCreativity\JsonApi\Contracts\Object\ResourceIdentifierCollectionInterface;
use CloudCreativity\JsonApi\Contracts\Object\ResourceIdentifierInterface;
use CloudCreativity\JsonApi\Exceptions\RecordNotFoundException;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;

/**
 * Interface StoreInterface
 *
 * The store is responsible for:
 *
 * - Looking up domain records based on a JSON API resource identifier.
 * - Querying domain records based on JSON API query parameters.
 *
 * So that the store can query multiple different types of domain records, it delegates
 * requests to objects that implement the `AdapterInterface`.
 *
 * @package CloudCreativity\JsonApi
 */
interface StoreInterface
{

    /**
     * Is the supplied resource type valid?
     *
     * @param $resourceType
     * @return bool
     */
    public function isType($resourceType);

    /**
     * Query the store for records using the supplied parameters.
     *
     * @param string $resourceType
     * @param EncodingParametersInterface $params
     * @return mixed
     */
    public function query($resourceType, EncodingParametersInterface $params);

    /**
     * Query the store for a single record using the supplied parameters.
     *
     * @param ResourceIdentifierInterface $identifier
     * @param EncodingParametersInterface $params
     * @return object|null
     */
    public function queryRecord(ResourceIdentifierInterface $identifier, EncodingParametersInterface $params);

    /**
     * Query the store for related records using the supplied parameters.
     *
     * For example, if a client is querying the `comments` relationship of a `posts` resource,
     * the store would be queried as follows:
     *
     * ```
     * $comments = $store->queryRelated('posts', $post, 'comments', $encodingParameters);
     * ```
     *
     * @param string $resourceType
     *      the JSON API resource type of the record on which the relationship exists
     * @param $record
     *      the domain record on which the relationship exists.
     * @param $relationshipName
     *      the name of the relationship that is being queried.
     * @param EncodingParametersInterface $params
     *      the encoding parameters to use for the query.
     * @return mixed
     *      the related records
     */
    public function queryRelated(
        $resourceType,
        $record,
        $relationshipName,
        EncodingParametersInterface $params
    );

    /**
     * Query the store for relationship data using the supplied parameters.
     *
     * For example, if a client is querying the `comments` relationship of a `posts` resource,
     * the store would be queried as follows:
     *
     * ```
     * $comments = $store->queryRelationship('posts', $post, 'comments', $encodingParameters);
     * ```
     *
     * @param string $resourceType
     *      the JSON API resource type of the record on which the relationship exists
     * @param $record
     *      the domain record on which the relationship exists.
     * @param $relationshipName
     *      the name of the relationship that is being queried.
     * @param EncodingParametersInterface $params
     *      the encoding parameters to use for the query.
     * @return mixed
     *      the related records
     */
    public function queryRelationship(
        $resourceType,
        $record,
        $relationshipName,
        EncodingParametersInterface $params
    );

    /**
     * Does the domain record this resource identifier refers to exist?
     *
     * @param ResourceIdentifierInterface $identifier
     * @return bool
     */
    public function exists(ResourceIdentifierInterface $identifier);

    /**
     * Find the domain record that this resource identifier refers to.
     *
     * @param ResourceIdentifierInterface $identifier
     * @return object|null
     *      the record, or null if it does not exist.
     */
    public function find(ResourceIdentifierInterface $identifier);

    /**
     * Find the domain record that this resource identifier refers to, or fail if it cannot be found.
     *
     * @param ResourceIdentifierInterface $identifier
     * @return object
     *      the record
     * @throws RecordNotFoundException
     *      if the record does not exist.
     */
    public function findOrFail(ResourceIdentifierInterface $identifier);

    /**
     * @param ResourceIdentifierInterface $identifier
     * @return object
     *      the record
     * @throws RecordNotFoundException
     *      if the record does not exist.
     * @deprecated use `findOrFail`
     */
    public function findRecord(ResourceIdentifierInterface $identifier);

    /**
     * Find many domain records using the supplied resource identifiers.
     *
     * The returned collection MUST contain only domain records that match the
     * supplied identifiers, and MUST NOT contain duplicate domain records (even if there
     * are duplicate identifiers). If it cannot find any domain records for the supplied
     * identifiers, it must still return a collection - i.e. the returned collection can
     * be of a length shorter than the collection of identifiers.
     *
     * @param ResourceIdentifierCollectionInterface $identifiers
     * @return array
     *      an array of domain records that match the supplied identifiers.
     */
    public function findMany(ResourceIdentifierCollectionInterface $identifiers);

}
