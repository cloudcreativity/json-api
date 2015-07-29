<?php

namespace CloudCreativity\JsonApi\Validator\Type;

use CloudCreativity\JsonApi\Error\ErrorObject;

class IntegerValidator extends AbstractTypeValidator
{

    use NullableTrait;

    /**
     * @param bool $nullable
     */
    public function __construct($nullable = false)
    {
        parent::__construct($nullable);

        $this->updateTemplate(static::ERROR_INVALID_VALUE, [
            ErrorObject::DETAIL => 'Expecting an integer value.',
        ]);
    }

    /**
     * @param $value
     * @return bool
     */
    protected function isType($value)
    {
        return is_int($value);
    }
}