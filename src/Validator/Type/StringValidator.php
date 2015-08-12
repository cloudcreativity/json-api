<?php

namespace CloudCreativity\JsonApi\Validator\Type;

use CloudCreativity\JsonApi\Error\ErrorObject;

class StringValidator extends TypeValidator
{

    use NullableTrait;

    /**
     * @param bool $nullable
     */
    public function __construct($nullable = false)
    {
        parent::__construct($nullable);

        $this->updateTemplate(static::ERROR_INVALID_VALUE, [
            ErrorObject::DETAIL => 'Expecting a string value.',
        ]);
    }

    /**
     * @param $value
     * @return bool
     */
    protected function isType($value)
    {
        return is_string($value);
    }
}