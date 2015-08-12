<?php

namespace CloudCreativity\JsonApi\Error;

use CloudCreativity\JsonApi\Contracts\Error\ErrorCollectionInterface;
use CloudCreativity\JsonApi\Contracts\Error\ErrorsAwareInterface;

class MultiErrorException extends \RuntimeException implements ErrorsAwareInterface
{

    /**
     * @var ErrorCollectionInterface
     */
    protected $_errors;

    /**
     * @param ErrorCollectionInterface $errors
     * @param $message
     * @param \Exception|null $previous
     */
    public function __construct(ErrorCollectionInterface $errors, $message = null, \Exception $previous = null)
    {
        parent::__construct($message, null, $previous);

        $this->_errors = $errors;
    }

    /**
     * @return ErrorCollectionInterface
     */
    public function getErrors()
    {
        return $this->_errors;
    }
}