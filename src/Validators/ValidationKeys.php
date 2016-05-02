<?php

namespace CloudCreativity\JsonApi\Validators;

class ValidationKeys
{

    /**
     * A compulsory member has not been included in document.
     */
    const MEMBER_REQUIRED = 'member:required';

    /**
     * A non-object has been provided for a member that must be an object.
     */
    const MEMBER_MUST_BE_OBJECT = 'member:non-object';

    /**
     * "A server MUST return 409 Conflict when processing a POST request in which the resource object's type is
     * not among the type(s) that constitute the collection represented by the endpoint."
     * http://jsonapi.org/format/#crud-creating
     *
     * "A server MUST return 409 Conflict when processing a PATCH request in which the resource object's type
     * and id do not match the server's endpoint."
     * http://jsonapi.org/format/#crud-updating
     */
    const RESOURCE_UNSUPPORTED_TYPE = 'resource:unsupported-type';

    /**
     * "A server MUST return 409 Conflict when processing a PATCH request in which the resource object's type
     * and id do not match the server's endpoint."
     * http://jsonapi.org/format/#crud-updating
     */
    const RESOURCE_UNSUPPORTED_ID = 'resource:unsupported-id';

    /**
     * Used when attributes are invalid but there are no validation error messages in the attributes validator.
     */
    const RESOURCE_ATTRIBUTES_INVALID = 'resource:invalid-attributes';

    /**
     * Used when relationships are invalid but there are no validation error messages in the relationships validator.
     */
    const RESOURCE_RELATIONSHIPS_INVALID = 'resource:invalid-relationships';
}
