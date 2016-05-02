<?php

namespace CloudCreativity\JsonApi\Contracts\Validators;

interface ValidatorFactoryInterface
{

    /**
     * Create a validator for a document containing a resource in its data member.
     *
     * @param ResourceValidatorInterface $resource
     *      the validator to use for the data member.
     * @return DocumentValidatorInterface
     */
    public function resourceDocument(ResourceValidatorInterface $resource);

    /**
     * Create a validator for a document containing a relationship in its data member.
     *
     * @param RelationshipValidatorInterface $relationship
     *      the validator to use for the data member.
     * @return DocumentValidatorInterface
     */
    public function relationshipDocument(RelationshipValidatorInterface $relationship);

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
    );

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
    );

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
    );
}
