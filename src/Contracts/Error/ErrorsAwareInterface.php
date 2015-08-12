<?php

namespace CloudCreativity\JsonApi\Contracts\Error;

interface ErrorsAwareInterface
{

    /**
     * @return ErrorCollectionInterface
     */
    public function getErrors();
}