<?php

namespace CloudCreativity\JsonApi\Contracts\Error;

use Neomerx\JsonApi\Contracts\Document\ErrorInterface;

interface ErrorCollectionInterface extends \Traversable, \Countable
{

    /**
     * @param ErrorInterface $error
     * @return $this
     */
    public function add(ErrorInterface $error);

    /**
     * @param ErrorInterface[] $errors
     * @return $this
     */
    public function addMany(array $errors);

    /**
     * @return ErrorInterface[]
     */
    public function getAll();

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param string|\Closure $pointer
     * @return $this
     */
    public function setSourcePointer($pointer);

    /**
     * @return bool
     */
    public function isEmpty();

    /**
     * @return $this
     */
    public function clear();

    /**
     * @param ErrorCollectionInterface $errors
     * @return $this
     */
    public function merge(ErrorCollectionInterface $errors);

}