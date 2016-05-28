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

namespace CloudCreativity\JsonApi\Validators;

use CloudCreativity\JsonApi\Contracts\Object\ResourceIdentifierInterface;
use CloudCreativity\JsonApi\Contracts\Repositories\ErrorRepositoryInterface;
use CloudCreativity\JsonApi\Contracts\Validators\ValidatorErrorFactoryInterface;
use CloudCreativity\JsonApi\Validators\Helpers\CreatesPointersTrait;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;

class ValidatorErrorFactory implements ValidatorErrorFactoryInterface
{

    const MEMBER_REQUIRED = 'validation:member-required';
    const MEMBER_OBJECT_EXPECTED = 'validation:member-object-expected';
    const MEMBER_RELATIONSHIP_EXPECTED = 'validation:member-relationship-expected';
    const RESOURCE_UNSUPPORTED_TYPE = 'validation:resource-unsupported-type';
    const RESOURCE_UNSUPPORTED_ID = 'validation:resource-unsupported-id';
    const RESOURCE_INVALID_ATTRIBUTES = 'validation:resource-invalid-attributes';
    const RESOURCE_INVALID_RELATIONSHIPS = 'validation:resource-invalid-relationships';
    const RELATIONSHIP_UNSUPPORTED_TYPE = 'validation:relationship-unsupported-type';
    const RELATIONSHIP_HAS_ONE_EXPECTED = 'validation:relationship-has-one-expected';
    const RELATIONSHIP_HAS_MANY_EXPECTED = 'validation:relationship-has-many-expected';
    const RELATIONSHIP_EMPTY_NOT_ALLOWED = 'validation:relationship-empty-not-allowed';
    const RELATIONSHIP_DOES_NOT_EXIST = 'validation:relationship-does-not-exist';
    const RELATIONSHIP_NOT_ACCEPTABLE = 'validation:relationship-not-acceptable';

    use CreatesPointersTrait;

    /**
     * @var ErrorRepositoryInterface
     */
    private $repository;

    /**
     * ValidatorErrorFactory constructor.
     * @param ErrorRepositoryInterface $repository
     */
    public function __construct(ErrorRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * A compulsory member has not been included in the document.
     *
     * @param string $memberKey
     *      the name of the member that is missing.
     * @param string $pointer
     *      the pointer of where the member is expected in the document.
     * @return ErrorInterface
     */
    public function memberRequired($memberKey, $pointer)
    {
        return $this->repository->errorWithPointer(self::MEMBER_REQUIRED, $pointer, null, [
            'member' => $memberKey,
        ]);
    }

    /**
     * A member is expected to be an object.
     *
     * @param string $memberKey
     *      the name of the member that is not an object.
     * @param string $pointer
     *      the pointer of where the member is in the document.
     * @return ErrorInterface
     */
    public function memberObjectExpected($memberKey, $pointer)
    {
        return $this->repository->errorWithPointer(self::MEMBER_OBJECT_EXPECTED, $pointer, null, [
            'member' => $memberKey,
        ]);
    }

    /**
     * A member is expected to be a relationship - object, array or null.
     *
     * @param $memberKey
     * @param $pointer
     * @return ErrorInterface
     */
    public function memberRelationshipExpected($memberKey, $pointer)
    {
        return $this->repository->errorWithPointer(self::MEMBER_RELATIONSHIP_EXPECTED, $pointer, null, [
            'member' => $memberKey,
        ]);
    }

    /**
     * The resource type is not supported.
     *
     * "A server MUST return 409 Conflict when processing a POST request in which the resource object's type is
     * not among the type(s) that constitute the collection represented by the endpoint."
     * http://jsonapi.org/format/#crud-creating
     *
     * "A server MUST return 409 Conflict when processing a PATCH request in which the resource object's type
     * and id do not match the server's endpoint."
     * http://jsonapi.org/format/#crud-updating
     *
     * @param string|string[] $expected
     *      the allowed resource type or types.
     * @param string $actual
     *      the actual resource type received.
     * @return ErrorInterface
     */
    public function resourceUnsupportedType($expected, $actual)
    {
        return $this->repository->errorWithPointer(
            self::RESOURCE_UNSUPPORTED_TYPE,
            $this->getPathToType(),
            self::STATUS_UNSUPPORTED_TYPE,
            ['expected' => $expected, 'actual' => $actual]
        );
    }

    /**
     * The resource id is not supported.
     *
     * "A server MUST return 409 Conflict when processing a PATCH request in which the resource object's type
     * and id do not match the server's endpoint."
     * http://jsonapi.org/format/#crud-updating
     *
     * @param string $expected
     * @param string $actual
     * @return ErrorInterface
     */
    public function resourceUnsupportedId($expected, $actual)
    {
        return $this->repository->errorWithPointer(
            self::RESOURCE_UNSUPPORTED_ID,
            $this->getPathToId(),
            self::STATUS_UNSUPPORTED_ID,
            ['expected' => $expected, 'actual' => $actual]
        );
    }

    /**
     * A generic error if attributes are invalid, but there are no other messages explaining why.
     *
     * @return ErrorInterface
     */
    public function resourceInvalidAttributes()
    {
        return $this->repository->errorWithPointer(
            self::RESOURCE_INVALID_ATTRIBUTES,
            $this->getPathToAttributes()
        );
    }

    /**
     * A generic error if relationships are invalid, but there are no other messages explaining why.
     *
     * @return ErrorInterface
     */
    public function resourceInvalidRelationships()
    {
        return $this->repository->errorWithPointer(
            self::RESOURCE_INVALID_RELATIONSHIPS,
            $this->getPathToRelationships()
        );
    }

    /**
     * The related resource is not of the correct type for the relationship.
     *
     * @param string|string[] $expected
     *      the allowed resource type or types.
     * @param $actual
     *      the actual resource type received.
     * @param string|null $relationshipKey
     *      the relationship key, or null if validating the relationship in the data member of a document.
     * @return ErrorInterface
     */
    public function relationshipUnsupportedType($expected, $actual, $relationshipKey = null)
    {
        return $this->repository->errorWithPointer(
            self::RELATIONSHIP_UNSUPPORTED_TYPE,
            $relationshipKey ? $this->getPathToRelationshipType($relationshipKey) : $this->getPathToType(),
            null,
            ['expected' => $expected, 'actual' => $actual]
        );
    }

    /**
     * A has-many relationship was provided for a has-one relationship.
     *
     * @param string|null $relationshipKey
     *      the relationship key, or null if validating the relationship in the data member of a document.
     * @return ErrorInterface
     */
    public function relationshipHasOneExpected($relationshipKey = null)
    {
        return $this->repository->errorWithPointer(
            self::RELATIONSHIP_HAS_ONE_EXPECTED,
            $relationshipKey ? $this->getPathToRelationship($relationshipKey) : $this->getPathToData()
        );
    }

    /**
     * A has-one relationship was provided for a has-many relationship.
     *
     * @param string|null $relationshipKey
     *      the relationship key, or null if validating the relationship in the data member of a document.
     * @return ErrorInterface
     */
    public function relationshipHasManyExpected($relationshipKey = null)
    {
        return $this->repository->errorWithPointer(
            self::RELATIONSHIP_HAS_MANY_EXPECTED,
            $relationshipKey ? $this->getPathToRelationship($relationshipKey) : $this->getPathToData()
        );
    }

    /**
     * An empty relationship was provided, but is not allowed.
     *
     * @param string|null $relationshipKey
     * @return ErrorInterface
     */
    public function relationshipEmptyNotAllowed($relationshipKey = null)
    {
        return $this->repository->errorWithPointer(
            self::RELATIONSHIP_EMPTY_NOT_ALLOWED,
            $relationshipKey ? $this->getPathToRelationship($relationshipKey) : $this->getPathToData()
        );
    }

    /**
     * A request references a resource that does not exist.
     *
     * "A server MUST return 404 Not Found when processing a request that references a related resource that does
     * not exist."
     * http://jsonapi.org/format/#crud-updating-relationships
     *
     * @param ResourceIdentifierInterface $identifier
     *      the resource identifier that does not exist.
     * @param string|null $relationshipKey
     *      the relationship key, or null if validating the relationship in the data member of a document.
     * @return ErrorInterface
     */
    public function relationshipDoesNotExist(ResourceIdentifierInterface $identifier, $relationshipKey = null)
    {
        return $this->repository->errorWithPointer(
            self::RELATIONSHIP_DOES_NOT_EXIST,
            $relationshipKey ? $this->getPathToRelationship($relationshipKey) : $this->getPathToData(),
            self::STATUS_RELATED_RESOURCE_DOES_NOT_EXIST,
            ['type' => $identifier->type(), 'id' => $identifier->id()]
        );
    }

    /**
     * A resource is not logically acceptable for the relationship.
     *
     * @param ResourceIdentifierInterface $identifier
     *      the related resource that is not acceptable.
     * @param string|null $relationshipKey
     *      the relationship key, or null if validating the relationship in the data member of a document.
     * @return ErrorInterface
     */
    public function relationshipNotAcceptable(ResourceIdentifierInterface $identifier, $relationshipKey = null)
    {
        return $this->repository->errorWithPointer(
            self::RELATIONSHIP_NOT_ACCEPTABLE,
            $relationshipKey ? $this->getPathToRelationship($relationshipKey) : $this->getPathToData(),
            null,
            ['type' => $identifier->type(), 'id' => $identifier->id()]
        );
    }

}
