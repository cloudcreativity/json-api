<?php

namespace CloudCreativity\JsonApi\Validator;

use CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface;
use CloudCreativity\JsonApi\Error\ErrorObject;
use CloudCreativity\JsonApi\Error\ErrorsAwareTrait;

abstract class AbstractValidator implements ValidatorInterface
{

    use ErrorsAwareTrait;

    /**
     * @var array
     */
    protected $templates = [];

    /**
     * @param $key
     * @param array $template
     * @return $this
     */
    public function setTemplate($key, array $template)
    {
        $this->templates[$key] = $template;

        return $this;
    }

    /**
     * @param $key
     * @param array $template
     * @return $this
     */
    public function updateTemplate($key, array $template)
    {
        $this->setTemplate($key, array_merge_recursive($this->template($key), $template));

        return $this;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        $messages = $this->getErrors()->clear();

        $this->validate($value);

        return $messages->isEmpty();
    }

    /**
     * Runs the validation logic for this validator.
     *
     * @param $value
     * @return void
     */
    abstract protected function validate($value);

    /**
     * @param $key
     * @param $pointer
     * @return ErrorObject
     */
    protected function error($key, $pointer = null)
    {
        $error = ErrorObject::create($this->template($key));
        $this->getErrors()->add($error);

        if (!is_null($pointer)) {
            $error->source()->setPointer($pointer);
        }

        return $error;
    }

    /**
     * @param $key
     * @return array
     */
    protected function template($key)
    {
        return isset($this->templates[$key]) ? (array) $this->templates[$key] : [];
    }
}