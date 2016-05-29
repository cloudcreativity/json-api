<?php

namespace CloudCreativity\JsonApi\Contracts\Stdlib;

use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use Neomerx\JsonApi\Exceptions\ErrorCollection;

interface ErrorsAwareInterface
{

    /**
     * @param ErrorCollection|ErrorInterface[] $errors
     * @return $this
     */
    public function addErrors($errors);

    /**
     * @return ErrorCollection
     */
    public function errors();
}
