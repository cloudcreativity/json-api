<?php

namespace CloudCreativity\JsonApi\Contracts\Stdlib;

use Neomerx\JsonApi\Exceptions\ErrorCollection;

interface ErrorsAwareInterface
{

    /**
     * @param ErrorCollection $errors
     * @return $this
     */
    public function addErrors(ErrorCollection $errors);

    /**
     * @return ErrorCollection
     */
    public function errors();
}
