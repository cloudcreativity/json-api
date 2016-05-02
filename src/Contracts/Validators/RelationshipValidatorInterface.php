<?php

namespace CloudCreativity\JsonApi\Contracts\Validators;

use CloudCreativity\JsonApi\Contracts\Object\Relationships\RelationshipInterface;
use CloudCreativity\JsonApi\Contracts\Object\Resource\ResourceInterface;
use CloudCreativity\JsonApi\Contracts\Stdlib\ErrorsAwareInterface;

interface RelationshipValidatorInterface extends ErrorsAwareInterface
{

    /**
     * Is the provided relationship valid?
     *
     * @param RelationshipInterface $relationship
     * @param ResourceInterface|null $resource
     *      if a full resource is being validated, the resource for context.
     * @return bool
     */
    public function isValid(RelationshipInterface $relationship, ResourceInterface $resource = null);

    /**
     * Must this relationship exist as a member on the relationships object?
     *
     * @return bool
     */
    public function isRequired();

}
