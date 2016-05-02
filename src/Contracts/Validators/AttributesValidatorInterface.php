<?php

namespace CloudCreativity\JsonApi\Contracts\Validators;

use CloudCreativity\JsonApi\Contracts\Stdlib\ErrorsAwareInterface;
use CloudCreativity\JsonApi\Contracts\Object\Resource\ResourceInterface;

interface AttributesValidatorInterface extends ErrorsAwareInterface
{

    /**
     * Are the attributes on the supplied resource valid?
     *
     * @param ResourceInterface $resource
     * @return bool
     */
    public function isValid(ResourceInterface $resource);

}
