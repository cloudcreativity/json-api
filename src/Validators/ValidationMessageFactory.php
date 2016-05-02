<?php

namespace CloudCreativity\JsonApi\Validators;

use CloudCreativity\JsonApi\Contracts\Validators\ValidationMessageFactoryInterface;
use CloudCreativity\JsonApi\Document\Error;
use CloudCreativity\JsonApi\Exceptions\RepositoryException;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;

class ValidationMessageFactory implements ValidationMessageFactoryInterface
{

    /**
     * @var array
     */
    private $errors;

    /**
     * ValidationMessageRepository constructor.
     * @param array $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @param string $key
     * @param array $values
     * @return ErrorInterface
     */
    public function error($key, array $values = [])
    {
        if (!$this->has($key)) {
            throw new RepositoryException("Did not recognise error key: $key");
        }

        $arr = $this->get($key);

        return Error::create($this->replacer($arr, $values));
    }

    /**
     * @param $key
     * @return bool
     */
    protected function has($key)
    {
        return isset($this->errors[$key]);
    }

    /**
     * @param $key
     * @return array
     */
    protected function get($key)
    {
        return isset($this->errors[$key]) ? (array) $this->errors[$key] : [];
    }

    /**
     * @param array $error
     * @param array $values
     * @return array
     */
    protected function replacer(array $error, array $values)
    {
        if (!isset($error[Error::DETAIL])) {
            return $error;
        }

        foreach ($values as $key => $value) {
            $error[Error::DETAIL] = str_replace($key, $value, $error[Error::DETAIL]);
        }

        return $error;
    }
}
