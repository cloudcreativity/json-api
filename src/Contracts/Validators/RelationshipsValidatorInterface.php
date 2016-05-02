<?php

namespace CloudCreativity\JsonApi\Contracts\Validators;

use CloudCreativity\JsonApi\Contracts\Stdlib\ErrorsAwareInterface;
use CloudCreativity\JsonApi\Contracts\Object\Resource\ResourceInterface;

interface RelationshipsValidatorInterface extends ErrorsAwareInterface
{

    /**
     * Set the keys that are allowed on the relationships object.
     *
     * @param string|string[] $keys
     * @return $this
     */
    public function allowedKeys($keys);

    /**
     * Add keys that are allowed on the relationships object.
     *
     * @param string|string[] $keys
     * @return $this
     */
    public function addAllowedKeys($keys);

    /**
     * @param $key
     * @param RelationshipValidatorInterface $validator
     * @return bool
     */
    public function add($key, RelationshipValidatorInterface $validator);

    /**
     * Are the relationships on the supplied resource valid?
     *
     * @param ResourceInterface $resource
     * @return bool
     */
    public function isValid(ResourceInterface $resource);
}
