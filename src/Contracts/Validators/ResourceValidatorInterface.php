<?php

namespace CloudCreativity\JsonApi\Contracts\Validators;

use CloudCreativity\JsonApi\Contracts\Stdlib\ErrorsAwareInterface;
use CloudCreativity\JsonApi\Contracts\Object\Resource\ResourceInterface;

interface ResourceValidatorInterface extends ErrorsAwareInterface
{

    /**
     * @param ResourceInterface $resource
     * @return bool
     */
    public function isValid(ResourceInterface $resource);
}
