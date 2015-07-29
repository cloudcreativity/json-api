<?php

namespace CloudCreativity\JsonApi\Contracts\Validator;

use CloudCreativity\JsonApi\Error\ErrorCollection;

interface ValidatorInterface
{

    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value);

    /**
     * @return ErrorCollection
     */
    public function getErrors();
}