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
     * A member that is expected to be a relationship is not an object, an array or a null value.
     */
    const MEMBER_MUST_BE_RELATIONSHIP = 'member:not-relationship';

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

    /**
     * If an empty relationship has been provided, but an empty value is not allowed.
     */
    const RELATIONSHIP_EMPTY_NOT_ALLOWED = 'relationship:empty-not-allowed';

    /**
     * If a related resource identifier does not exist.
     */
    const RELATIONSHIP_DOES_NOT_EXIST = 'relationship:does-not-exist';

    /**
     * If a related resource is not acceptable for the relationship.
     */
    const RELATIONSHIP_NOT_ACCEPTABLE = 'relationship:not-acceptable';

    /**
     * If a has-one relationship is expected, but a has-many is provided; and vice-versa.
     */
    const RELATIONSHIP_INVALID = 'relationship:invalid';

    /**
     * The resource identifier has a type member that is not supported for the relationship.
     */
    const RELATIONSHIP_UNSUPPORTED_TYPE = 'relationship:unsupportedType';
}
