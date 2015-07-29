<?php

namespace CloudCreativity\JsonApi\Validator\Type;

use CloudCreativity\JsonApi\Error\ErrorObject;

class FloatValidator extends AbstractTypeValidator
{

    use NullableTrait;

    /**
     * @param bool $nullable
     */
    public function __construct($nullable = false)
    {
        parent::__construct($nullable);

        $this->updateTemplate(static::ERROR_INVALID_VALUE, [
            ErrorObject::DETAIL => 'Expecting a float value.',
        ]);
    }

    /**
     * @param $value
     * @return bool
     */
    protected function isType($value)
    {
        return is_float($value);
    }
}