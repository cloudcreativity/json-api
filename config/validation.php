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
];
