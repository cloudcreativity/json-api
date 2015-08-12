<?php

namespace CloudCreativity\JsonApi\Error;

use CloudCreativity\JsonApi\Contracts\Error\ErrorCollectionInterface;

trait ErrorsAwareTrait
{

    /**
     * @var ErrorCollectionInterface|null
     */
    protected $_errors;

    /**
     * @param ErrorCollectionInterface $errors
     * @return $this
     */
    public function setErrors(ErrorCollectionInterface $errors)
    {
        $this->_errors = $errors;

        return $this;
    }

    /**
     * @return ErrorCollectionInterface
     */
    public function getErrors()
    {
        if (!$this->_errors instanceof ErrorCollectionInterface) {
            $this->_errors = new ErrorCollection();
        }

        return $this->_errors;
    }
}