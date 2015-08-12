<?php

namespace CloudCreativity\JsonApi\Error;

use CloudCreativity\JsonApi\Contracts\Error\ErrorsAwareInterface;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;

class ErrorException extends \RuntimeException implements ErrorsAwareInterface
{

    /**
     * @var ErrorInterface
     */
    protected $_error;

    /**
     * @param ErrorInterface $error
     * @param \Exception|null $previous
     */
    public function __construct(ErrorInterface $error, \Exception $previous = null)
    {
        parent::__construct($error->getTitle(), $error->getCode(), $previous);

        $this->_error = $error;
    }

    /**
     * @return ErrorInterface
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * @return ErrorCollection
     */
    public function getErrors()
    {
        return new ErrorCollection([$this->getError()]);
    }
}