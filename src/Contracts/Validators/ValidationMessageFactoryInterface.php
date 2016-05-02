<?php

namespace CloudCreativity\JsonApi\Contracts\Validators;

use Neomerx\JsonApi\Contracts\Document\ErrorInterface;

interface ValidationMessageFactoryInterface
{

    /**
     * @param string $key
     * @param array $values
     *      values to substitute into error detail.
     * @return ErrorInterface
     */
    public function error($key, array $values = []);
}
