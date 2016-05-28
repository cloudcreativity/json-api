<?php

namespace CloudCreativity\JsonApi\Validators;

use CloudCreativity\JsonApi\Contracts\Validators\AttributesValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\DocumentValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\RelationshipsValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\RelationshipValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\ResourceValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\ValidationMessageFactoryInterface;
use CloudCreativity\JsonApi\Contracts\Validators\ValidatorFactoryInterface;

class ValidatorFactory implements ValidatorFactoryInterface
{

    /**
     * @var ValidationMessageFactoryInterface
     */
    private $messages;

    /**
     * ValidatorFactory constructor.
     * @param ValidationMessageFactoryInterface $messages
     */
    public function __construct(ValidationMessageFactoryInterface $messages)
    {
        $this->messages = $messages;
    }

    /**
     * Create a validator for a document containing a resource in its data member.
     *
     * @param ResourceValidatorInterface $resource
     *      the validator to use for the data member.
     * @return DocumentValidatorInterface
     */
    public function resourceDocument(ResourceValidatorInterface $resource)
    {
        return new ResourceDocumentValidator($this->messages, $resource);
    }

    /**
     * Create a validator for a document containing a relationship in its data member.
     *
     * @param RelationshipValidatorInterface $relationship
     *      the validator to use for the data member.
     * @return DocumentValidatorInterface
     */
    public function relationshipDocument(RelationshipValidatorInterface $relationship)
    {
        // TODO: Implement relationshipDocument() method.
    }

    /**
     * Create a validator for a resource object.
     *
     * @param $expectedType
     *      the expected resource type.
     * @param string|int|null $expectedId
     *      the expected resource id, or null if none expected (create request).
     * @param AttributesValidatorInterface|null $attributes
     *      the validator to use for the attributes member.
     * @param RelationshipsValidatorInterface|null $relationships
     *      the validator to use for the relationships member.
     * @return ResourceValidatorInterface
     */
    public function resource(
        $expectedType,
        $expectedId = null,
        AttributesValidatorInterface $attributes = null,
        RelationshipsValidatorInterface $relationships = null
    ) {
        return new ResourceValidator(
            $this->messages,
            $expectedType,
            $expectedId,
            $attributes,
            $relationships
        );
    }

    /**
     * Create a relationship validator for a has-one relationship.
     *
     * @param string|string[] $expectedType
     *      the expected type or types
     * @param bool $required
     *      must the relationship exist as a member on the relationship object?
     * @param bool $allowEmpty
     *      is an empty has-one relationship acceptable?
     * @param callable|null $exists
     *      if a non-empty relationship, does the type/id exist?
     * @param callable|null $acceptable
     *      if a non-empty relationship that exists, is it acceptable?
     * @return RelationshipValidatorInterface
     */
    public function hasOne(
        $expectedType,
        $required = false,
        $allowEmpty = false,
        callable $exists = null,
        callable $acceptable = null
    ) {
        // TODO: Implement hasOne() method.
    }

    /**
     * Create a relationship validator for a has-many relationship.
     *
     * @param $expectedType
     *      the expected type or types.
     * @param bool $required
     *      must the relationship exist as a member on the relationship object?
     * @param bool $allowEmpty
     *      is an empty has-many relationship acceptable?
     * @param callable|null $exists
     *      does the type/id of an identifier within the relationship exist?
     * @param callable|null $acceptable
     *      if an identifier exists, is it acceptable within this relationship?
     * @return RelationshipValidatorInterface
     */
    public function hasMany(
        $expectedType,
        $required = false,
        $allowEmpty = false,
        callable $exists = null,
        callable $acceptable = null
    ) {
        // TODO: Implement hasMany() method.
    }


}
