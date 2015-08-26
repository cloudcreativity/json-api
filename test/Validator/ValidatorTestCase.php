<?php

namespace CloudCreativity\JsonApi\Validator;

use CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface;
use CloudCreativity\JsonApi\Error\ErrorObject;

class ValidatorTestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * @param ValidatorInterface $validator
     * @return ErrorObject
     */
    protected function getError(ValidatorInterface $validator)
    {
        if (0 === count($validator->getErrors())) {
            $this->fail('No errors found.');
        } elseif (1 < count($validator->getErrors())) {
            $this->fail('More than one error found.');
        }

        $error = current($validator->getErrors()->getAll());

        if (!$error instanceof ErrorObject) {
            $this->fail('Not an error object.');
        }

        return $error;
    }
}
