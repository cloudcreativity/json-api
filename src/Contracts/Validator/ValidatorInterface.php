<?php

namespace CloudCreativity\JsonApi\Contracts\Validator;

use CloudCreativity\JsonApi\Contracts\Error\ErrorsAwareInterface;

interface ValidatorInterface extends ErrorsAwareInterface
{

    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value);
}