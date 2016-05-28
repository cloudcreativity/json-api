<?php

use CloudCreativity\JsonApi\Document\Error;
use CloudCreativity\JsonApi\Validators\ValidationKeys as Keys;

return [

    /**
     * A compulsory member has not been included in document.
     */
    Keys::MEMBER_REQUIRED => [
        Error::CODE => 'required',
        Error::TITLE => 'Required Member',
        Error::DETAIL => "The member ':member' is required.",
        Error::STATUS => 400,
    ],

    /**
     * A non-object has been provided for a member that must be an object.
     */
    Keys::MEMBER_MUST_BE_OBJECT => [
        Error::CODE => 'non-object',
        Error::TITLE => 'Object Expected',
        Error::DETAIL => "The member ':member' must be an object.",
        Error::STATUS => 400,
    ],

    /**
     * A member that is expected to be a relationship is not an object, array or null value.
     */
    Keys::MEMBER_MUST_BE_RELATIONSHIP => [
        Error::CODE => 'non-relationship',
        Error::TITLE => 'Relationship Expected',
        Error::DETAIL => "The member ':member' must be a relationship object.",
        Error::STATUS => 400,
    ],

    /**
     * "A server MUST return 409 Conflict when processing a POST request in which the resource object's type is
     * not among the type(s) that constitute the collection represented by the endpoint."
     * http://jsonapi.org/format/#crud-creating
     *
     * "A server MUST return 409 Conflict when processing a PATCH request in which the resource object's type
     * and id do not match the server's endpoint."
     * http://jsonapi.org/format/#crud-updating
     */
    Keys::RESOURCE_UNSUPPORTED_TYPE => [
        Error::CODE => 'unsupported-type',
        Error::TITLE => 'Unsupported Resource',
        Error::DETAIL => "Resource ':actual' is not among the type(s) supported by this endpoint. Expecting only ':expected' resources.",
        Error::STATUS => 409,
    ],

    /**
     * "A server MUST return 409 Conflict when processing a PATCH request in which the resource object's type
     * and id do not match the server's endpoint."
     * http://jsonapi.org/format/#crud-updating
     */
    Keys::RESOURCE_UNSUPPORTED_ID => [
        Error::CODE => 'unsupported-id',
        Error::TITLE => 'Unsupported Resource',
        Error::DETAIL => "Resource id ':actual' is not supported by this endpoint. Expecting only resource ':expected'.",
        Error::STATUS => 409,
    ],

    /**
     * Used when attributes are invalid but there are no validation error messages in the attributes validator.
     */
    Keys::RESOURCE_ATTRIBUTES_INVALID => [
        Error::CODE => 'invalid',
        Error::TITLE => 'Invalid Attributes',
        Error::DETAIL => 'The attributes member is invalid.',
        Error::STATUS => 400,
    ],

    /**
     * Used when relationships are invalid but there are no validation error messages in the relationships validator.
     */
    Keys::RESOURCE_RELATIONSHIPS_INVALID => [
        Error::CODE => 'invalid',
        Error::TITLE => 'Invalid Relationships',
        Error::DETAIL => 'The relationships member is invalid.',
        Error::STATUS => 400,
    ],

    /**
     * Used when a has-one relationship is expected, but a has-many has been provided; or vice-versa.
     */
    Keys::RELATIONSHIP_INVALID => [
        Error::CODE => 'invalid',
        Error::TITLE => 'Invalid Relationship',
        Error::DETAIL => 'The provided relationship must be a :relationship relationship',
        Error::STATUS => 400,
    ],

    /**
     * When an empty relationship is not allowed.
     */
    Keys::RELATIONSHIP_EMPTY_NOT_ALLOWED => [
        Error::CODE => 'invalid',
        Error::TITLE => 'Invalid Relationship',
        Error::DETAIL => 'The provided relationship cannot be empty.',
        Error::STATUS => 422,
    ],

    /**
     * "A server MUST return 404 Not Found when processing a request that references a related resource that does
     * not exist."
     * http://jsonapi.org/format/#crud-updating-relationships
     */
    Keys::RELATIONSHIP_DOES_NOT_EXIST => [
        Error::CODE => 'invalid',
        Error::TITLE => 'Invalid Relationship',
        Error::DETAIL => 'The related resource does not exist.',
        Error::STATUS => 404,
    ],

    /**
     * When a related resource is not logically acceptable for the relationship.
     */
    Keys::RELATIONSHIP_NOT_ACCEPTABLE => [
        Error::CODE => 'invalid',
        Error::TITLE => 'Invalid Relationship',
        Error::DETAIL => 'The related resource is not acceptable.',
        Error::STATUS => 422,
    ],

    /**
     * When a related resource is not of the correct type for the relationship.
     */
    Keys::RELATIONSHIP_UNSUPPORTED_TYPE => [
        Error::CODE => 'unsupported-type',
        Error::TITLE => 'Invalid Relationship',
        Error::DETAIL => "Resource ':actual' is not among the type(s) supported by this relationship. Expecting only ':expected' resources.",
        Error::STATUS => 400,
    ],
];
